<?php

namespace Oro\Bundle\InfinitePayBundle\Action\Mapper;

use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\ResponseBodyInterface;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;

/**
 * Maps capture response to payment transaction.
 */
class CaptureResponseMapper implements ResponseMapperInterface
{
    /**
     * @param PaymentTransaction    $paymentTransaction
     * @param ResponseBodyInterface $response
     *
     * @return PaymentTransaction
     */
    #[\Override]
    public function mapResponseToPaymentTransaction(
        PaymentTransaction $paymentTransaction,
        ResponseBodyInterface $response
    ) {
        $status = $response->getResponse()->getResponseData()->getStatus();
        $active = ($status === '1');
        $paymentTransaction->setActive($active);
        $paymentTransaction->setReference($response->getResponse()->getResponseData()->getRefNo());

        return $paymentTransaction;
    }
}
