<?php

namespace Oro\Bundle\InfinitePayBundle\Method\Config;

use Oro\Bundle\PaymentBundle\Method\Config\ParameterBag\AbstractParameterBagPaymentConfig;

class InfinitePayConfig extends AbstractParameterBagPaymentConfig implements InfinitePayConfigInterface
{
    public const CLIENT_REF_KEY = 'client_ref';
    public const USERNAME_KEY = 'username';
    public const PASSWORD_KEY = 'password';
    public const SECRET_KEY = 'secret';

    public const AUTO_CAPTURE_KEY = 'auto_capture';
    public const AUTO_ACTIVATE_KEY = 'auto_activate';

    public const TEST_MODE_KEY = 'test_mode';
    public const DEBUG_MODE_KEY = 'debug_mode';

    public const INVOICE_DUE_PERIOD_KEY = 'invoice_due_period';
    public const INVOICE_SHIPPING_DURATION_KEY = 'invoice_shipping_duration';

    /**
     * @return bool
     */
    #[\Override]
    public function isAutoCaptureEnabled()
    {
        return (bool) $this->get(self::AUTO_CAPTURE_KEY);
    }

    /**
     * @return bool
     */
    #[\Override]
    public function isAutoActivateEnabled()
    {
        return (bool) $this->get(self::AUTO_ACTIVATE_KEY);
    }

    /**
     * @return bool
     */
    #[\Override]
    public function isTestModeEnabled()
    {
        return (bool) $this->get(self::TEST_MODE_KEY);
    }

    /**
     * @return bool
     */
    #[\Override]
    public function isDebugModeEnabled()
    {
        return (bool) $this->get(self::DEBUG_MODE_KEY);
    }

    /**
     * @return string
     */
    #[\Override]
    public function getClientRef()
    {
        return (string) $this->get(self::CLIENT_REF_KEY);
    }

    /**
     * @return string
     */
    #[\Override]
    public function getUsername()
    {
        return (string) $this->get(self::USERNAME_KEY);
    }

    /**
     * @return string
     */
    #[\Override]
    public function getPassword()
    {
        return (string) $this->get(self::PASSWORD_KEY);
    }

    /**
     * @return string
     */
    #[\Override]
    public function getSecret()
    {
        return (string) $this->get(self::SECRET_KEY);
    }

    /**
     * @return int
     */
    #[\Override]
    public function getInvoiceDuePeriod()
    {
        return (int) $this->get(self::INVOICE_DUE_PERIOD_KEY);
    }

    /**
     * @return int
     */
    #[\Override]
    public function getShippingDuration()
    {
        return (int) $this->get(self::INVOICE_SHIPPING_DURATION_KEY);
    }
}
