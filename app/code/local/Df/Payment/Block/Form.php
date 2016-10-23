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
		return $this->method()->canCreateBillingAgreement();
	}

	/**
	 * @override
	 * @return string
	 */
	public function getArea() {return Df_Core_Const_Design_Area::FRONTEND;}

	/** @return string */
	public function getDescription() {return $this->method()->configF()->getDescription();}

	/**
	 * заимствовано из Mage_Payment_Block_Form
	 * @see Mage_Payment_Block_Form::getInfoData
	 * @param string $field
	 * @return string
	 */
	public function getInfoData($field) {return
		df_e($this->method()->getInfoInstance()->getData($field))
	;}

	/**
	 * заимствовано из Mage_Payment_Block_Form
	 * @see Mage_Payment_Block_Form::getMethod
	 * @return Df_Payment_Method
	 */
	public function method() {
		/** @var Df_Payment_Method $result */
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
	public function getMethodCode() {return $this->method()->getCode();}

	/** @return bool */
	public function isTestMode() {return $this->method()->isTestMode();}

	/**
	 * @override
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/payment/form.phtml';}

	/** @used-by Df_Payment_Method::getFormBlockType() */

}