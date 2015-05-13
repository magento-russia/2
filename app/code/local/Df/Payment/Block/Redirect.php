<?php
class Df_Payment_Block_Redirect extends Mage_Page_Block_Redirect {
	/**
	 * @override
	 * @return array
	 */
	public function getFormFields() {return $this->getPaymentMethod()->getPaymentPageParams();}

	/**
	 * @override
	 * @return string
	 */
	public function getFormId() {return get_class($this);}

	/**
	 * @override
	 * @return string
	 * @throws Exception
	 */
	public function getHtmlFormRedirect() {
		/** @var string $result */
		try {
			$result = strtr(
				'{form}<script type="text/javascript">document.getElementById("{id}").submit();</script>'
				,array(
					'{form}' => $this->getForm()->toHtml()
					,'{id}' => $this->getFormId()
				)
			);
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e, true);
		}
		return $result;
	}

	/**
	 * @override
	 * @return string
	 */
	public function getMethod() {
		return $this->getPaymentMethod()->getConst('request/method', false, Zend_Form::METHOD_POST);
	}

	/**
	 * @override
	 * @return string
	 */
	public function getTargetURL() {return $this->getPaymentMethod()->getPaymentPageUrl();}

	/** @return Df_Varien_Data_Form */
	private function getForm() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Varien_Data_Form $result */
			$result = new Df_Varien_Data_Form();
			$result->setId($this->getFormId());
			$result
				->setAction($this->getTargetURL())
				->setName($this->getFormId())
				->setMethod($this->getMethod())
				->setUseContainer(true)
				->addHiddenFields($this->_getFormFields())
				->addAdditionalHtmlAttribute('accept-charset', 'UTF-8')
			;
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Sales_Model_Order */
	private function getOrder() {
		if (!isset($this->{__METHOD__})) {
			/** @var mixed $orderIncrementId */
			$orderIncrementId =
				rm_session_checkout()->getData(Df_Checkout_Const::SESSION_PARAM__LAST_REAL_ORDER_ID)
			;
			if (!$orderIncrementId) {
				df_error('Пожалуйста, попробуйте оформить Ваш заказ повторно или оформите заказ по телефону.');
			}
			/** @var Df_Sales_Model_Order $result */
			$result = Df_Sales_Model_Order::i();
			$result->loadByIncrementId($orderIncrementId);
			if (!$result->getId()) {
				df_error('Пожалуйста, попробуйте оформить Ваш заказ повторно или оформите заказ по телефону.');
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Payment_Model_Method_WithRedirect */
	private function getPaymentMethod() {return $this->getOrder()->getPayment()->getMethodInstance();}

	const _CLASS = __CLASS__;
}