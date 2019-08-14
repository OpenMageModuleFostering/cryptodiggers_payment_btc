<?php
class Cdpbtc_Payment_Model_Resource_Tran_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
	public function _construct()
	{
		$this->_init('cdpbtc_payment/tran');
	}
}