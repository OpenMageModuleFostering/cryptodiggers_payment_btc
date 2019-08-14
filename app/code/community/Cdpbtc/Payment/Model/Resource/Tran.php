<?php
class Cdpbtc_Payment_Model_Resource_Tran extends Mage_Core_Model_Resource_Db_Abstract
{
	protected function _construct()
	{
		$this->_init('cdpbtc_payment/tran', 'cdp_id');
	}

	public function loadByTxid(Cdpbtc_Payment_Model_Tran $tran,$Txid){

		$select = $this->_getLoadByUniqueKeySelect($Txid);
		$data_id   = $this->_getReadAdapter()->fetchOne($select);
		
		if($data_id){
			$this->load($tran,$data_id);
		}
		else{
			return false;
		}
		return $this;
	}

	public function loadByOrderId(Cdpbtc_Payment_Model_Tran $tran,$OrderId){

		$select = $this->_getLoadOrderId($OrderId);
		$data_id   = $this->_getReadAdapter()->fetchOne($select);

		if($data_id){
			$this->load($tran,$data_id);
		}
		return $this;
	}
	
	private function _getLoadByUniqueKeySelect($Txid)
	{
		return $this->_getReadAdapter()->select()
		->from($this->getMainTable())
		->where($this->getMainTable().'.txid = ?', $Txid);
	}

	private function _getLoadOrderId($Orderid)
	{
		return $this->_getReadAdapter()->select()
		->from($this->getMainTable())
		->where($this->getMainTable().'.orderid = ?', $Orderid);
	}
}