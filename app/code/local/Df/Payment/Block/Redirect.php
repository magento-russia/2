<?php
class Df_Payment_Block_Redirect extends Mage_Page_Block_Redirect {
	/**
	 * @override
	 * @return array(string => string|int)
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
			$result = $this->getForm()->toHtml() . sprintf(
				"<script type='text/javascript'>document.getElementById('%s').submit();</script>"
				,$this->getFormId()
			);
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e, true);
		}
		return $result;
	}

	/**
	 * @override
	 * @return string
	 */
	public function getMethod() {
		return $this->getPaymentMethod()->const_('request/method', false, Zend_Form::METHOD_POST);
	}

	/**
	 * @override
	 * @see Mage_Page_Block_Redirect::getTargetURL()
	 * @used-by app/design/frontend/base/default/template/page/redirect.phtml
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
	private function order() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_last_order(false);
			if (!$this->{__METHOD__}) {
				df_error('Пожалуйста, попробуйте оформить Ваш заказ повторно или оформите заказ по телефону.');
			}
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Payment_Model_Method_WithRedirect */
	private function getPaymentMethod() {return $this->order()->getPayment()->getMethodInstance();}
}