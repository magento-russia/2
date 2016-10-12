<?php
/**
 * Допускает товар с заданной степенью видимости
 */
class Df_Catalog_Model_Validate_Product_Visibility
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
				in_array(
					$value[Df_Catalog_Model_Product::P__VISIBILITY]
					,$this->getValidVisibilityStates()
				)
		;
	}

	/** @return array */
	private function getValidVisibilityStates() {
		return $this->cfg(self::VALID_VISIBILITY_STATES);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::VALID_VISIBILITY_STATES, self::V_ARRAY);
	}
	const _CLASS = __CLASS__;
	const VALID_VISIBILITY_STATES = 'validVisibilityStates';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Catalog_Model_Validate_Product_Visibility
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}