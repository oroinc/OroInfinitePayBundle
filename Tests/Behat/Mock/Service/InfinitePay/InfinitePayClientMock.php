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
    public const EMAIL_SUCCESS = 'email_for_success_emulation@test.com';

    public const TEST_ADD_INFO = 'test add info';
    public const TEST_ORDER_ID = 'test order id';
    public const TEST_DB_ID = 'test bd id';
    public const TEST_GUAR_AMT = 'test guar amt';
    public const TEST_REF_NO = 'test ref no';
    public const TEST_STATUS_SUCCESS = '1';
    public const TEST_STATUS_FAIL = '0';
    public const TEST_ERROR_MESSAGE = 'test error message';

    /** @var InfinitePayConfigInterface */
    protected $config;

    /** @var array */
    protected $options;

    public function __construct(InfinitePayConfigInterface $config, array $options = [])
    {
        $this->config = $config;
        $this->options = $options;
    }

    #[\Override]
    public function reserveOrder(ReserveOrder $parameters)
    {
        return new ReserveOrderResponse($this->fillResponse(new ResponseReservation(), $parameters));
    }

    #[\Override]
    public function callCaptureOrder(CaptureOrder $parameters)
    {
        return new CaptureOrderResponse();
    }

    #[\Override]
    public function activateOrder(ActivateOrder $parameters)
    {
        return new ActivateOrderResponse();
    }

    #[\Override]
    public function applyTransactionOnActivatedOrder(ApplyTransaction $parameters)
    {
        return new ApplyTransactionResponse();
    }

    #[\Override]
    public function cancelOrder(CancelOrder $parameters)
    {
        return new CancelOrderResponse();
    }

    #[\Override]
    public function modifyReservedOrder(ModifyReservedOrder $parameters)
    {
        return new ModifyReservedOrderResponse();
    }

    #[\Override]
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
