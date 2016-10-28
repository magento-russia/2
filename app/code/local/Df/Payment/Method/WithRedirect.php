<?php
namespace Df\Payment\Method;
use Df_Sales_Model_Order as O;
abstract class WithRedirect extends \Df\Payment\Method {
	/**
	 * @used-by \Df\Payment\Request\Payment::urlCustomerReturn()
	 * @used-by \Df\YandexMoney\Action\CustomerReturn::getToken()
	 * @param O $order
	 * @return string
	 */
	public function getCustomerReturnUrl(O $order) {return
		\Mage::getUrl(df_cc_path($this->getCode(), 'customerReturn'), [
			'_query' => [self::REQUEST_PARAM__ORDER_INCREMENT_ID => $order->getIncrementId()]
			// Без _nosid система будет формировать ссылку вида
			// http://localhost.com:811/df-avangard/customerReturn/?___SID=U&magentoOrderIncrementId=100000053
			,'_nosid' => true
		]);
	}

	/**
	 * @override
	 * @used-by Mage_Checkout_Model_Type_Onepage::saveOrder():
			$redirectUrl = $this->getQuote()->getPayment()->getOrderPlaceRedirectUrl();
			if (!$redirectUrl && $order->getCanSendNewEmailFlag()) {
				try {
					$order->sendNewOrderEmail();
				} catch (Exception $e) {
					Mage::logException($e);
				}
			}
			// add order information to the session
			$this->_checkoutSession->setLastOrderId($order->getId())
				->setRedirectUrl($redirectUrl)
				->setLastRealOrderId($order->getIncrementId());
	 * @return string
	 */
	public function getOrderPlaceRedirectUrl() {return
		\Mage::getUrl('df-payment/redirect', ['_secure' => true])
	;}

	/**
	 * Обратите внимание, что платёжный шлюз Альфа-Банка (@see \Df\Alfabank\Method)
	 * не нуждается в получении параметров при перенаправлении на него покупателя.
	 * Вместо этого модуль Альфа-Банк передаёт эти параметры предварительным запросом
	 * @see \Df\Alfabank\Method::getRegistrationResponse()
	 * и платёжный шлюз возвращает модулю уникальный веб-адрес
	 * @see \Df\Alfabank\Method::getPaymentPageUrl()
	 * на который модуль перенаправляет покупателя без параметров.
	 * Если в других модулях потребуется такое же поведение (перенаправление без параметров),
	 * то посмотрите, как устроен модуль Альфа-Банк.
	 * @used-by \Df\Payment\Block\Redirect::getFormFields()
	 * @return array(string => string|int)
	 */
	public function getPaymentPageParams() {return \Df\Payment\Request\Payment::params($this);}

	/**
	 * @used-by \Df\Payment\Block\Redirect::getTargetURL()
	 * @return string
	 */
	public function getPaymentPageUrl() {return $this->configS()->getUrlPaymentPage();}

	/**
	 * Method that will be executed instead of authorize or capture
	 * if flag isInitilizeNeeded set to true
	 * @override
	 * @param string|bool|null $paymentAction
	 * @param \Varien_Object $stateObject
	 * @return \Mage_Payment_Model_Method_Abstract
	 */
	public function initialize($paymentAction, $stateObject) {
		df_assert($stateObject instanceof \Varien_Object);
		parent::initialize($paymentAction, $stateObject);
		$stateObject->addData(array(
			'state' => O::STATE_PENDING_PAYMENT
			,'status' => O::STATE_PENDING_PAYMENT
			,'is_notified' => false
		));
		return $this;
	}

	/**
	 * @override
	 * @return bool
	 */
	public function isInitializeNeeded() {return true;}


	const REQUEST_PARAM__ORDER_INCREMENT_ID = 'magentoOrderIncrementId';
}