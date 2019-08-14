<?php
class Cdpbtc_Payment_Model_Config
{
	/**
     * Payment API authentication methods source getter
     *
     * @return array
     */
    public function getAvailableCurrency()
    {
        return array(
            '1' => 'EUR',
            '2' => 'USD'
        );
    }
}

?>