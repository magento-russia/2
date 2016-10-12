<?php
/**
 * Допускает товар системного типа Configurable
 */
class Df_Catalog_Model_Validate_Product_Configurable
	extends Df_Core_Model
	implements Zend_Validate_Interface {
	/**
	 * @override
	 * @return array
	 * @deprecated Since 1.5.0
	 */
	public function getErrors() {
		return array();
	}

	/**
	 * @override
	 * @return array
	 */
	public function getMessages() {
		return array();
	}

	/**
	 * @override
	 * @param Mage_Catalog_Model_Product|mixed $value
	 * @return boolean
	 * @throws Zend_Validate_Exception If validation of $value is impossible
	 */
	public function isValid($value) {
		return
				($value instanceof Mage_Catalog_Model_Product)
			&&
				(
						Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE
					===
						$value->getData(Df_Catalog_Model_Product::P__TYPE_ID)
				)
		;
	}
	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Catalog_Model_Validate_Product_Configurable
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}