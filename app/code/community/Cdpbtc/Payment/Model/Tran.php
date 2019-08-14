<?php
class Cdpbtc_Payment_Model_Tran extends Mage_Core_Model_Abstract
{
	protected $_eventPrefix = 'cdpbtc_payment_tran';
	protected $_eventObject = 'tran';
	protected function _construct()
	{
		$this->_init('cdpbtc_payment/tran');
	}

	public function TranSave($data){
		$this->setOrderid($data["orderid"]);
		$this->setTxid($data["txid"]);
		$this->setPrice($data["price"]);
		$this->setStatus($data["status"]);
		try {
			$this->save();
		} catch (Exception $e) {
			Mage::logException($e);
			return false;
		}
		return true;
	}
	
	public function TranUpdate($data){
		$this->loadByOrderid($data["orderid"]);
		$this->setStatus($data["status"]);
		try {
			$this->save();
		} catch (Exception $e) {
			Mage::logException($e);
			return false;
		}
		return true;
	}

	public function TranRead($Orderid){
		$trans = $this->getCollection()->AddFilter('orderid',$Orderid);
		if($trans->getSize()==1){
			foreach ($trans as $tran) {
				return $tran;
			}
		}
		else{
			return false;
		}
	}

	public function loadByTxid($Txid){
		$this->_getResource()->loadByTxid($this,$Txid);
		return $this;
	}
	
	public function CheckIfTranExist($Txid){
		$data=$this->loadByTxid($Txid);
		
		if($this->offsetExists("cdp_id")){
			return true;
		}
		return false;
	}
	
	public function loadByOrderid($Orderid){
		$this->_getResource()->loadByOrderid($this,$Orderid);
		return $this;
	}
	
	public function GetTranStatus($Orderid){
		$data=$this->loadByOrderid($Orderid);
		if($data){
			return $data->getStatus();
		}
		return false;
	}

	/*public function TranUpdateStatus($Address,$status){
		$data=$this->loadByAddress($Address);
		echo $data->getCdp_id();
		$this->load($data->getCdp_id())->addData(array("status"=>$status));
		try {
			$this->setId($data->getCdp_id())->save();
			echo "Data updated successfully.";

		} catch (Exception $e){
			echo $e->getMessage();
		}
	}*/
}
