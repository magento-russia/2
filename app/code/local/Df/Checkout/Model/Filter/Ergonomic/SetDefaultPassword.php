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
		/** @var Df_Checkout_Model_Settings_Field_Applicability $settings */
		$settings = df_cfg()->checkout()->applicabilityBilling();
		if (
				rm_checkout_ergonomic()
			&&
				// Убеждаемся, что покупатель неавторизован
				!rm_customer_logged_in()
			&&
				// Убеждаемся, что поля «Пароль» и «Пароль повторно» необязательны для заполнения
				!$settings->isRequired(Df_Checkout_Const_Field::CUSTOMER_PASSWORD)
			&&
				!$settings->isRequired(Df_Checkout_Const_Field::CONFIRM_PASSWORD)
			&&
				// Убеждаемся, что поля «Пароль» и «Пароль повторно» не заполнены
				!dfa($value, Df_Checkout_Const_Field::CUSTOMER_PASSWORD)
			&&
				!dfa($value, Df_Checkout_Const_Field::CONFIRM_PASSWORD)
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

	const _C = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Checkout_Model_Filter_Ergonomic_SetDefaultPassword
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}