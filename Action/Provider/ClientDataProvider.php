<?php

namespace Oro\Bundle\InfinitePayBundle\Action\Provider;

use Oro\Bundle\InfinitePayBundle\Method\Config\InfinitePayConfigInterface;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\ClientData;

/**
 * This provider provides required data for InfinitePay API requests
 */
class ClientDataProvider implements ClientDataProviderInterface
{
    /**
     * @inheritdoc
     */
    public function getClientData($orderId, InfinitePayConfigInterface $config)
    {
        $clientData = new ClientData();
        $clientData->setClientRef($config->getClientRef());
        $message = $config->getClientRef().$orderId;
        $clientData->setSecurityCd(
            $this->generateSecurityCode($message, $config->getSecret())
        );

        return $clientData;
    }

    /**
     * @param string $message
     * @param string $secret
     *
     * @return string
     */
    private function generateSecurityCode($message, $secret)
    {
        $hmac = hash_hmac('sha256', $message, $secret);

        return base64_encode($hmac);
    }
}
