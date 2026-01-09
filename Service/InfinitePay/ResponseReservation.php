<?php

namespace Oro\Bundle\InfinitePayBundle\Service\InfinitePay;

/**
 * Represents a reservation response.
 */
class ResponseReservation extends GenericResponse
{
    /**
     * @var DebtorCorrectedData
     */
    protected $DEBTOR_CORRECTED_DATA;

    /**
     * @return DebtorCorrectedData
     */
    public function getDebtorCorrectedData()
    {
        return $this->DEBTOR_CORRECTED_DATA;
    }

    /**
     * @param DebtorCorrectedData $DEBTOR_CORRECTED_DATA
     *
     * @return \Oro\Bundle\InfinitePayBundle\Service\InfinitePay\ResponseReservation
     */
    public function setDebtorCorrectedData($DEBTOR_CORRECTED_DATA)
    {
        $this->DEBTOR_CORRECTED_DATA = $DEBTOR_CORRECTED_DATA;

        return $this;
    }
}
