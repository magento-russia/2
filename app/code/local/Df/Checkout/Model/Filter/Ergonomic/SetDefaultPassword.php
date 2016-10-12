<?php
/**
 * Модуль «Удобное оформление заказа».
 * При отключении администратором видимости или обязательности заполнения
 * полей «Пароль» и «Пароль повторно»
 * система должна сама выбирать для покупателя пароль и отсылать его покупателю.
 */
class Df_Checkout_Model_Filter_Ergonomic_SetDefaultPassword
	extends Df_Core_Model
	implements Zend_Filter_Interface {
	/**
	 * @param array $value
	 * @return array
	 */
	public function filter($value) {
		df_param_array($value, 0);
		if (
				df_cfg()->checkout()->_interface()->needShowAllStepsAtOnce()
			&&
				// Убеждаемся, что покупатель неавторизован
				!df_mage()->customer()->isLoggedIn()
			&&
				// Убеждаемся, что поля «Пароль» и «Пароль повторно» необязательны для заполнения
				(
						df_cfg()->checkout()->applicabilityBilling()->customer_password()
					!==
						Df_Checkout_Model_Config_Source_Field_Applicability::VALUE__REQUIRED
				)
			&&
				(
						df_cfg()->checkout()->applicabilityBilling()->confirm_password()
					!==
						Df_Checkout_Model_Config_Source_Field_Applicability::VALUE__REQUIRED
				)
			&&
				// Убеждаемся, что поля «Пароль» и «Пароль повторно» не заполнены
				!df_a($value, Df_Checkout_Const_Field::CUSTOMER_PASSWORD)
			&&
				!df_a($value, Df_Checkout_Const_Field::CONFIRM_PASSWORD)
		) {
			$value[Df_Checkout_Const_Field::CUSTOMER_PASSWORD] = $this->getGeneratedPassword();
			$value[Df_Checkout_Const_Field::CONFIRM_PASSWORD] = $this->getGeneratedPassword();
			rm_session_customer()->setData(
				Df_Customer_Const_Session::GENERATED_PASSWORD, $this->getGeneratedPassword()
			);
		}
		return $value;
	}

	/** @return string */
	private function getGeneratedPassword() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Customer_Model_Customer::i()->generatePassword();
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Checkout_Model_Filter_Ergonomic_SetDefaultPassword
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}