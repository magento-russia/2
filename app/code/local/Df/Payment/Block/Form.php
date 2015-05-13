<?php
/**
 * Обратите внимание, что класс Df_Payment_Block_Form не унаследован от Mage_Payment_Block_Form,
 * но реализует полностью его интерфейс.
 * Намеренно наследуемся от Df_Core_Block_Template,
 * чтобы пользоваться всеми возможностями этого класса.
 */
class Df_Payment_Block_Form extends Df_Core_Block_Template_NoCache {
	/**
	 * заимствовано из Mage_Payment_Block_Form
	 * @see Mage_Payment_Block_Form::canCreateBillingAgreement
	 * @return bool
	 */
	public function canCreateBillingAgreement() {
		return $this->getMethod()->canCreateBillingAgreement();
	}

	/**
	 * @override
	 * @return string
	 */
	public function getArea() {return Df_Core_Const_Design_Area::FRONTEND;}

	/** @return string */
	public function getDescription() {
		return $this->getMethod()->getRmConfig()->frontend()->getDescription();
	}

	/**
	 * заимствовано из Mage_Payment_Block_Form
	 * @see Mage_Payment_Block_Form::getInfoData
	 * @param string $field
	 * @return string
	 */
	public function getInfoData($field) {
		return df_text()->escapeHtml($this->getMethod()->getInfoInstance()->getData($field));
	}

	/**
	 * заимствовано из Mage_Payment_Block_Form
	 * @see Mage_Payment_Block_Form::getMethod
	 * @return Df_Payment_Model_Method_Base
	 */
	public function getMethod() {
		/** @var Df_Payment_Model_Method_Base $result */
		$result = $this->getData('method');
		if (!($result instanceof Mage_Payment_Model_Method_Abstract)) {
			Mage::throwException($this->__('Cannot retrieve the payment method model object.'));
  		}
  		return $result;
	}

	/**
	 * заимствовано из Mage_Payment_Block_Form
	 * @see Mage_Payment_Block_Form::getMethodCode
	 * @return string
	 */
	public function getMethodCode() {return $this->getMethod()->getCode();}

	/** @return bool */
	public function isTestMode() {return $this->getMethod()->getRmConfig()->service()->isTestMode();}

	/**
	 * @override
	 * @return string
	 */
	protected function getDefaultTemplate() {return 'df/payment/form.phtml';}

	/** @return Mage_Sales_Model_Quote */
	protected function getQuote() {return rm_session_checkout()->getQuote();}

	const _CLASS = __CLASS__;
}