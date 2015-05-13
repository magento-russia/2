<?php
abstract class Df_Payment_Model_Method_WithRedirect extends Df_Payment_Model_Method_Base {
	/**
	 * @param Df_Sales_Model_Order $order
	 * @return string
	 */
	public function getCustomerReturnUrl(Df_Sales_Model_Order $order) {
		return Mage::getUrl(df_concat_url($this->getCode(), 'customerReturn'), array(
			'_query' => array(self::REQUEST_PARAM__ORDER_INCREMENT_ID => $order->getIncrementId())
			// Без _nosid система будет формировать ссылку вида
			// http://localhost.com:811/df-avangard/customerReturn/?___SID=U&magentoOrderIncrementId=100000053
			,'_nosid' => true
		));
	}

	/**
	 * Вызывается из Mage_Checkout_Model_Type_Onepage::saveOrder()
	 * [code]
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
	 * [/code]
	 * @override
	 * @return string
	 */
	public function getOrderPlaceRedirectUrl() {
		return
			Mage::getUrl(
				df_concat_url(self::RM__ROUTER_NAME, self::RM__REDIRECT_CONTROLLER_SHORT_NAME)
				,array('_secure' => true)
			)
		;
	}

	/**
	 * Обратите внимание, что платёжный шлюз Альфа-Банка (@see Df_Alfabank_Model_Payment)
	 * не нуждается в получении параметров при перенаправлении на него покупателя.
	 * Вместо этого модуль Альфа-Банк передаёт эти параметры предварительным запросом,
	 * и платёжный шлюз возвращает модулю уникальный веб-адрес,
	 * на который модуль перенаправляет покупателя без параметров.
	 *
	 * Если в других модулях потребуется такое же поведение (перенаправление без параметров),
	 * то посмотрите, как устроен модуль Альфа-Банк:
	 * он перекрывает метод @see Df_Payment_Model_Method_WithRedirect::getPaymentPageParams()
	 * (@see Df_Alfabank_Model_Payment::getPaymentPageParams()),
	 * а класс @see Df_Alfabank_Model_Request_Payment используется
	 * не для получения параметров перенаправления покупателя на платёжный шлюз,
	 * а для предварительной регистрации заказа в платёжном шлюзе.
	 * @return array(string => mixed)
	 */
	public function getPaymentPageParams() {return $this->getRequestPayment()->getParams();}

	/** @return string */
	public function getPaymentPageUrl() {return $this->getRmConfig()->service()->getUrlPaymentPage();}

	/**
	 * Method that will be executed instead of authorize or capture
	 * if flag isInitilizeNeeded set to true
	 * @override
	 * @param string|bool|null $paymentAction
	 * @param Varien_Object $stateObject
	 * @return Mage_Payment_Model_Method_Abstract
	 */
	public function initialize($paymentAction, $stateObject) {
		df_assert($stateObject instanceof Varien_Object);
		parent::initialize($paymentAction, $stateObject);
		$stateObject->addData(array(
			'state' => Mage_Sales_Model_Order::STATE_PENDING_PAYMENT
			,'status' => Mage_Sales_Model_Order::STATE_PENDING_PAYMENT
			,'is_notified' => false
		));
		return $this;
	}

	/**
	 * @override
	 * @return bool
	 */
	public function isInitializeNeeded() {
		return true;
	}

	/**
	 * Метод обозначен как protected (а не private),
	 * потому что его использует класс-потомок:
	 * @see Df_Alfabank_Model_Request_Payment::getPaymentPageUrl().
	 * В таком использовании нет ничего плохого:
	 * просто сам модуль Альфа-Банк обладает индивидуальностью:
	 * платёжный шлюз Альфа-Банка не нуждается в получении параметров
	 * при перенаправлении на него покупателя.
	 * Вместо этого модуль Альфа-Банк передаёт эти параметры предварительным запросом,
	 * и платёжный шлюз возвращает модулю уникальный веб-адрес
	 * (@see Df_Alfabank_Model_Request_Payment::getPaymentPageUrl()),
	 * на который модуль перенаправляет покупателя без параметров.
	 * @return Df_Payment_Model_Request_Payment
	 */
	protected function getRequestPayment() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Payment_Model_Request_Payment $result */
			$this->{__METHOD__} =
				df_model(
					$this->getRequestPaymentClass()
					,array(Df_Payment_Model_Request_Payment::P__ORDER => $this->loadOrder())
				)
			;
			df_assert($this->{__METHOD__} instanceof Df_Payment_Model_Request_Payment);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @return string
	 * @throws Df_Core_Exception_Client
	 */
	protected function getRequestPaymentClass() {
		return
			Df_Core_Model_ClassManager::s()->getResourceClass(
				$caller = $this
				,$resourceSuffix = 'Model_Request_Payment'
				,$defaultResult = null
			)
		;
	}

	/** @return Df_Sales_Model_Order */
	private function loadOrder() {
		/** @var Df_Sales_Model_Order $result */
		$result = Df_Sales_Model_Order::i();
		/** @var string $orderIncrementId */
		$orderIncrementId =
			rm_session_checkout()->getDataUsingMethod(
				Df_Checkout_Const::SESSION_PARAM__LAST_REAL_ORDER_ID
			)
		;
		if ($orderIncrementId) {
			$result->loadByIncrementId($orderIncrementId);
		}
		df_assert(!is_null($result->getId()));
		return $result;
	}

	const _CLASS = __CLASS__;
	const REQUEST_PARAM__ORDER_INCREMENT_ID = 'magentoOrderIncrementId';
	const RM__REDIRECT_CONTROLLER_SHORT_NAME = 'redirect';
	const RM__ROUTER_NAME = 'df-payment';
}