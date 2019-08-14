<?php
class Cdpbtc_Payment_Model_PaymentMethod extends Mage_Payment_Model_Method_Abstract
{
	/**
	 * unique internal payment method identifier
	 *
	 * @var string [a-z0-9_]
	 */
	protected $_code = 'cdpbtc_payment';

	/**
	 * Here are examples of flags that will determine functionality availability
	 * of this module to be used by frontend and backend.
	 *
	 * @see all flags and their defaults in Mage_Payment_Model_Method_Abstract
	 *
	 * It is possible to have a custom dynamic logic by overloading
	 * public function can* for each flag respectively
	 */

	/**
	 * Is this payment method a gateway (online auth/charge) ?
	 */
	protected $_isGateway               = true;

	/**
	 * Can authorize online?
	 */
	protected $_canAuthorize            = true;

	/**
	 * Can capture funds online?
	 */
	protected $_canCapture              = false;

	/**
	 * Can capture partial amounts online?
	 */
	protected $_canCapturePartial       = false;

	/**
	 * Can refund online?
	 */
	protected $_canRefund               = false;

	/**
	 * Can void transactions online?
	 */
	protected $_canVoid                 = false;

	/**
	 * Can use this payment method in administration panel?
	 */
	protected $_canUseInternal          = false;

	/**
	 * Can show this payment method as an option on checkout payment page?
	 */
	protected $_canUseCheckout          = true;

	/**
	 * Is this payment method suitable for multi-shipping checkout?
	 */
	protected $_canUseForMultishipping  = true;

	/**
	 * Can save credit card information for future processing?
	 */
	protected $_canSaveCc = false;

	/**
	 * Here you will need to implement authorize, capture and void public methods
	 *
	 * @see examples of transaction specific public methods such as
	 * authorize, capture and void in Mage_Paygate_Model_Authorizenet
	 */
	public function canUseCheckout()
	{
		$CDP_apikey = Mage::getStoreConfig('payment/cdpbtc_payment/api_key');
		if (!$CDP_apikey or !strlen($CDP_apikey))
		{
			Mage::log('cdpbtc_payment/cdp: API key not entered');
			return false;
		}

		$CDP_seckey = Mage::getStoreConfig('payment/cdpbtc_payment/sec_key');
		if (!$CDP_seckey or !strlen($CDP_seckey))
		{
			Mage::log('cdpbtc_payment/cdp: Security key not entered');
			return false;
		}

		$CDP_timeout = Mage::getStoreConfig('payment/cdpbtc_payment/payment_timeout');
		if ((!$CDP_timeout or !strlen($CDP_timeout)))
		{
			Mage::log('cdpbtc_payment/timeout: Timeout value not entered');
			return false;
		}
		else{
			if($CDP_timeout<10 || $CDP_timeout>30){
				Mage::log('cdpbtc_payment/timeout: Timeout value set to incorrect value');
				return false;
			}
		}

		return $this->_canUseCheckout;
	}

	public function authorize(Varien_Object $payment, $amount)
	{
		return $this->CheckForPayment($payment);
	}

	function CheckForPayment($payment)
	{
		$tran = Mage::getModel('cdpbtc_payment/tran');
		//$quoteId = $payment->getOrder()->getQuoteId();
		$quote = Mage::getSingleton('checkout/session')->getQuote();
		$orderId=$quote->getReservedOrderId();
		$data=$tran->TranRead($orderId);
		if (!$data)
		{
			Mage::throwException("Order not paid for. Please pay first and then Place your Order.");
		}
		//Mage::throwException("Order paid".$data->getStatus());
		if($data->getStatus()!=2){
			$payment->setIsTransactionPending(true)->save();
		}
		return $this;
	}
}
?>