<?php

namespace Oro\Bundle\InfinitePayBundle\Tests\Unit\Action\Mapper;

use Oro\Bundle\InfinitePayBundle\Action\Mapper\ActivationResponseMapper;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\ActivateOrderResponse;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\ResponseActivation;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\ResponseData;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;

class ActivationResponseMapperTest extends \PHPUnit\Framework\TestCase
{
    public function testMapResponseToPaymentTransactionSuccess()
    {
        $responseMapper = new ActivationResponseMapper();
        $paymentTransaction = new PaymentTransaction();
        $response = $this->getResponse('1');
        $actualPaymentTransaction = $responseMapper->mapResponseToPaymentTransaction($paymentTransaction, $response);
        $this->assertTrue($actualPaymentTransaction->isSuccessful());
    }

    public function testMapResponseToPaymentTransactionFailure()
    {
        $responseMapper = new ActivationResponseMapper();
        $paymentTransaction = new PaymentTransaction();
        $response = $this->getResponse('0');
        $actualPaymentTransaction = $responseMapper->mapResponseToPaymentTransaction($paymentTransaction, $response);
        $this->assertFalse($actualPaymentTransaction->isSuccessful());
    }

    private function getResponse(string $status): ActivateOrderResponse
    {
        $responseActivation = new ResponseActivation();
        $responseActivation->setResponseData((new ResponseData())->setStatus($status));

        return (new ActivateOrderResponse())->setResponse($responseActivation);
    }
}
