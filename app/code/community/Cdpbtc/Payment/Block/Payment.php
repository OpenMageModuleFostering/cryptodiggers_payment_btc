<?php
class Cdpbtc_Payment_Block_Payment extends Mage_Checkout_Block_Onepage_Payment
{
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('cdpbtc/btcpayment.phtml');
			
	}

	private function roundup_prec($in,$prec)
	{
		$fact = pow(10,$prec);
		return ceil($fact*$in)/$fact;
	}

	public function GetBTCPaymenet(){
		if (Mage::registry('customer_save_observer_executed')){
			return $this;
		}

		$tran = Mage::getModel('cdpbtc_payment/tran');
		$quote = $this->getQuote();

		$order = Mage::getModel('sales/order')->load($quote->getId());
		$Incrementid = $order->getIncrementId();

		if (!Mage::getStoreConfig('payment/cdpbtc_payment/active'))
		return 'disabled';
		//return $quote->getId();
		$quote->reserveOrderId()->save();
		$data="https://www.cryptodiggers.eu/api/api.php?apikey=".Mage::getStoreConfig('payment/cdpbtc_payment/api_key')."&a=eshop_payment&timeout=".Mage::getStoreConfig('payment/cdpbtc_payment/payment_timeout')."&order_id=".$quote->getReservedOrderId()."&amount=".number_format($quote->getGrandTotal(), 2, '.', '')."&currency=".Mage::getStoreConfig('payment/cdpbtc_payment/fiat_currency')."&currency_crypto=6&wait=".Mage::getStoreConfig('payment/cdpbtc_payment/wait_confirmations');
		//$data="http://192.168.0.10/wallet/api.php?apikey=".Mage::getStoreConfig('payment/cdpbtc_payment/api_key')."&a=eshop_payment&timeout=".Mage::getStoreConfig('payment/cdpbtc_payment/payment_timeout')."&order_id=".$quote->getReservedOrderId()."&amount=".number_format($quote->getGrandTotal(), 2, '.', '')."&currency=".Mage::getStoreConfig('payment/cdpbtc_payment/fiat_currency')."&currency_crypto=6&wait=".Mage::getStoreConfig('payment/cdpbtc_payment/wait_confirmations');
		$retVal=$this->getApi($data);
		//echo $data;
		unset($data);
		//print_r($retVal);
		//echo "";
		$data["msg"]="Error occured during payment. Please contact eshop or choose another payment methode.";
		if($retVal){
			if($retVal["error"]==0){
				$data["link"]="https://www.cryptodiggers.eu/api/api.php?iframe=".$retVal["iframe_id"]."&a=eshop_payment&timeout=".
				//$data["link"]="http://127.0.0.1/wallet/api.php?iframe=".$retVal["iframe_id"]."&a=eshop_payment&timeout=".
				Mage::getStoreConfig('payment/cdpbtc_payment/payment_timeout')."&order_id=".$quote->getReservedOrderId()."
				&amount=".number_format($this->roundup_prec($quote->getGrandTotal(),2), 2, '.', '')."
				&currency=".Mage::getStoreConfig('payment/cdpbtc_payment/fiat_currency')."
				&currency_crypto=6&wait=".Mage::getStoreConfig('payment/cdpbtc_payment/wait_confirmations');
				$data["msg"]="";
				$data["status"]=true;
			}
			else{
				$data["msg"]=$retVal["error_msg"];
				$data["status"]=false;
			}
		}
		else{
			$data["msg"]="Payment method not available at this moment. Please try to checkout once again.";
			$data["status"]=false;
		}
		Mage::register('customer_save_observer_executed',true);
		return $data;
	}

	private function getApi($target,$post=NULL, $auth=NULL) {
		$proxy_use=Mage::getStoreConfig('payment/cdpbtc_payment/proxy_use');
		$proxy_server=Mage::getStoreConfig('payment/cdpbtc_payment/proxy_server');
		$proxy_port=Mage::getStoreConfig('payment/cdpbtc_payment/proxy_port');
		static $ch = null;
		static $ch = null;
		$url=$target;
		if (is_null($ch)) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			if($proxy_use==1){
				curl_setopt($ch, CURLOPT_PROXY, $proxy_server);
				curl_setopt($ch, CURLOPT_PROXYPORT, $proxy_port);
			}
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; PHP client; '.php_uname('s').'; PHP/'.phpversion().')');
		}

		if(is_array($post)){
			$postdata = http_build_query($post, '', '&');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		}
		curl_setopt($ch, CURLOPT_URL, $url . $target);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		//echo $target;

		$res = curl_exec($ch);
		if ($res === false) {
			return false;
		}
		$dec = json_decode($res, true);
		if (!$dec) {
			return false;
		}
		return $dec;
	}

}