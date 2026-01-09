<?php

namespace Oro\Bundle\InfinitePayBundle\Action;

use Oro\Bundle\InfinitePayBundle\Action\Provider\AutomationProviderInterface;
use Oro\Bundle\InfinitePayBundle\Method\Config\InfinitePayConfigInterface;
use Oro\Bundle\OrderBundle\Entity\Order;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;

/**
 * Handles reserve action for payment transactions.
 */
class Reserve extends ActionAbstract
{
    /**
     * @internal
     */
    public const DEFAULT_ADDITIONAL_DATA = [
        'email' => '',
        'legalForm' => '',
    ];

    /**
     * @var AutomationProviderInterface
     */
    protected $automationProvider;

    public function setAutomationProvider(AutomationProviderInterface $automationProvider)
    {
        $this->automationProvider = $automationProvider;
    }

    /**
     * @param PaymentTransaction $paymentTransaction
     * @param Order              $order
     *
     * @return array
     */
    #[\Override]
    public function execute(PaymentTransaction $paymentTransaction, Order $order)
    {
        $additionalData = $this->getAdditionalDataFromPaymentTransaction($paymentTransaction);

        $paymentMethodConfig = $this->getPaymentMethodConfig($paymentTransaction->getPaymentMethod());

        $reserveOrder = $this->requestMapper->createRequestFromOrder($order, $paymentMethodConfig, $additionalData);
        $reserveOrder = $this->automationProvider->setAutomation(
            $reserveOrder,
            $order,
            $paymentMethodConfig
        );
        $paymentResponse = $this->gateway->reserve(
            $reserveOrder,
            $paymentMethodConfig
        );

        $paymentTransaction = $this->responseMapper->mapResponseToPaymentTransaction(
            $paymentTransaction,
            $paymentResponse
        );
        $paymentTransaction->setSuccessful(
            $this->isSuccessfulAutoActivation($paymentTransaction, $paymentMethodConfig)
        );

        return $this->createResponseFromPaymentTransaction($paymentTransaction);
    }

    /**
     * @param PaymentTransaction $paymentTransaction
     *
     * @return array
     */
    private function createResponseFromPaymentTransaction(PaymentTransaction $paymentTransaction)
    {
        $response = ['success' => $paymentTransaction->isActive()];
        if (!$paymentTransaction->isActive()) {
            $response['successUrl'] = null;
        }

        return $response;
    }

    /**
     * @param PaymentTransaction $paymentTransaction
     *
     * @return array
     *
     * @throws \LogicException
     * @throws \ErrorException
     */
    private function getAdditionalDataFromPaymentTransaction(PaymentTransaction $paymentTransaction)
    {
        $transactionOptions = $paymentTransaction->getTransactionOptions();

        if (!array_key_exists('additionalData', $transactionOptions)) {
            throw new \ErrorException('Additional data was not found in transaction options');
        }

        $additionalData = json_decode($transactionOptions['additionalData'], true);
        if (!is_array($additionalData)) {
            throw new \LogicException('Additional data could not be decoded');
        }

        // Ensure additional data has requried elements.
        $additionalData += self::DEFAULT_ADDITIONAL_DATA;

        // Remove undesired elements.
        $filteredAdditionalData = array_intersect_key($additionalData, self::DEFAULT_ADDITIONAL_DATA);

        return $filteredAdditionalData;
    }

    /**
     * @param PaymentTransaction $paymentTransaction
     * @param InfinitePayConfigInterface $config
     * @return bool
     */
    private function isSuccessfulAutoActivation(
        PaymentTransaction $paymentTransaction,
        InfinitePayConfigInterface $config
    ) {
        return $config !== null && $config->isAutoActivateEnabled() && $paymentTransaction->isActive();
    }
}
