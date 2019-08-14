<?php 

class Cdpbtc_Payment_Model_Observer
{
	public function hookToOrderSaveEvent()
	{
		$order = new Mage_Sales_Model_Order();
		$incrementId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
		$order->loadByIncrementId($incrementId);
		
		
		$tran = Mage::getModel('cdpbtc_payment/tran');
		$data=$tran->TranRead($incrementId);
		Mage::log('cdpbtc_payment/cdp - OrderId:'.$incrementId."-".$data->getStatus());
		
		switch ($data->getStatus()){
			case 2:
				$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true)->save();
				break;
			case 3:
				$order->setState(Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW, true)->save();
				break;
			default:
				$order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, true)->save();
				
		}
		/*if($data->getStatus()==2){
			$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true)->save();
		}
		else{
			if($data->getStatus()==3){
				$order->setState(Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW, true)->save();
			}
			else{
				$order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, true)->save();
			}
		}*/
		$order->save();
	}
}