<?php

namespace Oro\Bundle\InfinitePayBundle\Action\Provider;

use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\DebtorData;
use Oro\Bundle\OrderBundle\Entity\Order;
use Oro\Bundle\OrderBundle\Entity\OrderAddress;
use Oro\Bundle\PaymentBundle\Provider\AddressExtractor;
use Oro\Component\PhpUtils\Network\DnsResolver;

/**
 * Converts company data into format used by requests
 */
class DebtorDataProvider implements DebtorDataProviderInterface
{
    const NOT_AVAILABLE = 'na';
    const DB_NEW_CUSTOMER_HAS_ACCOUNT = '1';
    const NEG_PAY_HIST_FALSE = '0';

    /**
     * @var CompanyDataProviderInterface
     */
    protected $companyDataProvider;
    /**
     * @var RequestProvider
     */
    protected $requestProvider;

    /** @var AddressExtractor */
    protected $addressExtractorProvider;

    /** @var DnsResolver */
    protected $dnsResolver;

    /**
     * @param CompanyDataProviderInterface $companyDataProvider
     * @param RequestProvider $requestProvider
     * @param AddressExtractor $addressExtractor
     * @param DnsResolver $dnsResolver
     */
    public function __construct(
        CompanyDataProviderInterface $companyDataProvider,
        RequestProvider $requestProvider,
        AddressExtractor $addressExtractor,
        DnsResolver $dnsResolver
    ) {
        $this->companyDataProvider = $companyDataProvider;
        $this->requestProvider = $requestProvider;
        $this->addressExtractorProvider = $addressExtractor;
        $this->dnsResolver = $dnsResolver;
    }

    /**
     * @param Order $order
     *
     * @return DebtorData
     */
    public function getDebtorData(Order $order)
    {
        /** @var OrderAddress $billingAddress */
        $billingAddress = $this->addressExtractorProvider->extractAddress($order);

        $debtorData = new DebtorData();

        $companyData = $this->companyDataProvider->getCompanyData($billingAddress, $order->getCustomer());
        $debtorData->setCompanyData($companyData);

        $ipAddress = $this->requestProvider->getClientIp();

        $debtorData
            ->setDbNew(self::DB_NEW_CUSTOMER_HAS_ACCOUNT)
            ->setNegPayHist(self::NEG_PAY_HIST_FALSE)
            ->setBdSalut(static::NOT_AVAILABLE)
            ->setIpAdd($ipAddress)
            ->setIsp($this->dnsResolver->getHostnameByIp($ipAddress))
            ->setBdZip($billingAddress->getPostalCode())
            ->setBdCountry($billingAddress->getCountryIso3())
            ->setBdStreet($billingAddress->getStreet())
            ->setBdCity($billingAddress->getCity())
            ->setBdNameFs($billingAddress->getFirstName())
            ->setBdNameLs($billingAddress->getLastName())
        ;

        return $debtorData;
    }
}
