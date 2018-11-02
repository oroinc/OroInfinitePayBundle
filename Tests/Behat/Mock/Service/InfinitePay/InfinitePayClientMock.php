<?php

namespace Oro\Bundle\InfinitePayBundle\Tests\Behat\Mock\Service\InfinitePay;

use Oro\Bundle\InfinitePayBundle\Method\Config\InfinitePayConfigInterface;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\ActivateOrder;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\ActivateOrderResponse;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\ApplyTransaction;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\ApplyTransactionResponse;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\CancelOrder;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\CancelOrderResponse;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\CaptureOrder;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\CaptureOrderResponse;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\CheckStatusOrder;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\CheckStatusOrderResponse;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\ErrorDataList;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\GenericResponse;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\InfinitePayClientInterface;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\ModifyReservedOrder;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\ModifyReservedOrderResponse;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\ReserveOrder;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\ReserveOrderResponse;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\ResponseData;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\ResponseReservation;

class InfinitePayClientMock implements InfinitePayClientInterface
{
    const EMAIL_SUCCESS = 'email_for_success_emulation@test.com';

    const TEST_ADD_INFO = 'test add info';
    const TEST_ORDER_ID = 'test order id';
    const TEST_DB_ID = 'test bd id';
    const TEST_GUAR_AMT = 'test guar amt';
    const TEST_REF_NO = 'test ref no';
    const TEST_STATUS_SUCCESS = '1';
    const TEST_STATUS_FAIL = '0';
    const TEST_ERROR_MESSAGE = 'test error message';

    /** @var InfinitePayConfigInterface */
    protected $config;

    /** @var array */
    protected $options;

    /**
     * @param InfinitePayConfigInterface $config
     * @param array $options
     */
    public function __construct(InfinitePayConfigInterface $config, array $options = [])
    {
        $this->config = $config;
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function reserveOrder(ReserveOrder $parameters)
    {
        return new ReserveOrderResponse($this->fillResponse(new ResponseReservation(), $parameters));
    }

    /**
     * {@inheritdoc}
     */
    public function callCaptureOrder(CaptureOrder $parameters)
    {
        return new CaptureOrderResponse();
    }

    /**
     * {@inheritdoc}
     */
    public function activateOrder(ActivateOrder $parameters)
    {
        return new ActivateOrderResponse();
    }

    /**
     * {@inheritdoc}
     */
    public function applyTransactionOnActivatedOrder(ApplyTransaction $parameters)
    {
        return new ApplyTransactionResponse();
    }

    /**
     * {@inheritdoc}
     */
    public function cancelOrder(CancelOrder $parameters)
    {
        return new CancelOrderResponse();
    }

    /**
     * {@inheritdoc}
     */
    public function modifyReservedOrder(ModifyReservedOrder $parameters)
    {
        return new ModifyReservedOrderResponse();
    }

    /**
     * {@inheritdoc}
     */
    public function checkStatusOrder(CheckStatusOrder $parameters)
    {
        return new CheckStatusOrderResponse();
    }

    /**
     * @param GenericResponse $response
     * @param ReserveOrder $parameters
     * @return GenericResponse
     */
    protected function fillResponse(GenericResponse $response, ReserveOrder $parameters)
    {
        $isSuccess = $parameters->getRequest()->getDebtorData()->getBdEmail() === self::EMAIL_SUCCESS;
        $responseData = new ResponseData(
            self::TEST_ADD_INFO,
            self::TEST_ORDER_ID,
            self::TEST_DB_ID,
            self::TEST_GUAR_AMT,
            self::TEST_REF_NO,
            $isSuccess ? self::TEST_STATUS_SUCCESS : self::TEST_STATUS_FAIL
        );
        $response->setResponseData($responseData);
        if (!$isSuccess) {
            $errorData = new ErrorDataList();
            $errorData->setError([self::TEST_ERROR_MESSAGE]);
            $response->setErrorData($errorData);
        }

        return $response;
    }
}
