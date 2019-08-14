<?php
class Cdpbtc_Payment_ProcessController extends Mage_Core_Controller_Front_Action
{
	public function tranSaveAction(){
		$CDP_seckey = Mage::getStoreConfig('payment/cdpbtc_payment/sec_key');

		if(array_key_exists("cdp_hash",$_POST)){
			if($_POST["cdp_hash"]==hash('sha512',$CDP_seckey.$_POST["txid"])){
				$tran = Mage::getModel('cdpbtc_payment/tran');
				if(!$tran->CheckIfTranExist($_POST["txid"])){
					if($tran->TranSave(array("orderid"=>$_POST["orderid"],"txid"=>$_POST["txid"],"price"=>$_POST["amount"],"status"=>$_POST["status"]))){
						if($_POST["status"]>=2){
							$order = Mage::getModel('sales/order')->loadByIncrementId($_POST["orderid"]);
							if ($order->getId())
							{
								if($_POST["status"]==2){
									foreach($order->getInvoiceCollection() as $i){
										$i->pay()->save();
									}
									$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true)->save();
								}
								if($_POST["status"]==3){
									$order->setState(Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW, true)->save();
									Mage::log('cdpbtc_payment/cdp: Transaction not saved. - '.$_POST["txid"]." - ".$_POST["status"]);
								}
							}
						}
						echo "OK";
						return true;
					}
					else{
						Mage::log('cdpbtc_payment/cdp: Transaction not saved. - '.$_POST["txid"]);
					}
				}
				else{
					if($tran->TranUpdate(array("orderid"=>$_POST["orderid"],"status"=>$_POST["status"]))){
						if($_POST["status"]>=2){
							$order = Mage::getModel('sales/order')->loadByIncrementId($_POST["orderid"]);
							if ($order->getId())
							{
								if($_POST["status"]==2){
									foreach($order->getInvoiceCollection() as $i){
										$i->pay()->save();
									}
									$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true)->save();
								}
								if($_POST["status"]==3){
									$order->setState(Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW, true)->save();
									Mage::log('cdpbtc_payment/cdp: Transaction not saved. - '.$_POST["txid"]." - ".$_POST["status"]);
								}
									
							}
						}
						echo "OK";
						return true;
					}
					else{
						Mage::log('cdpbtc_payment/cdp: Transaction not saved. - '.$_POST["txid"]);
					}
				}
			}
			else{
				Mage::log('cdpbtc_payment/cdp: Security key incorrect. - '.$_POST["txid"]);
			}
		}
		else{
			Mage::log('cdpbtc_payment/cdp: Security key not send.');
		}
		return false;
	}
}