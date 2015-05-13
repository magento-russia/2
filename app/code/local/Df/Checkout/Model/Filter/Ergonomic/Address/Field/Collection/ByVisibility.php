<?php
class Df_Checkout_Model_Filter_Ergonomic_Address_Field_Collection_ByVisibility
	extends Df_Core_Model_Filter_Collection {
	/**
	 * Создает коллецию - результат фильтрации.
	 * Потомки могут перекрытием этого метода создать коллекцию своего класса.
	 * Метод должен возвращать объект класса Df_Varien_Data_Collection или его потомков
	 * @override
	 * @return Df_Checkout_Model_Collection_Ergonomic_Address_Field
	 */
	protected function createResultCollection() {
		return Df_Checkout_Model_Collection_Ergonomic_Address_Field::i();
	}

	/**
	 * @override
	 * @return Zend_Validate_Interface
	 */
	protected function createValidator() {
		return Df_Checkout_Model_Validator_Ergonomic_Address_Field_Visible::i();
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Checkout_Model_Filter_Ergonomic_Address_Field_Collection_ByVisibility
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}