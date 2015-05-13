<?php
/**
 * Оставляет в коллекции только товары с заданной степенью видимости
 */
class Df_Catalog_Model_Filter_Product_Collection_Visibility
	extends Df_Core_Model_Filter_Collection {
	/**
	 * @override
	 * @return Df_Catalog_Model_Validate_Product_Visibility
	 */
	protected function createValidator() {
		return
			Df_Catalog_Model_Validate_Product_Visibility::i(
				array(
					Df_Catalog_Model_Validate_Product_Visibility::VALID_VISIBILITY_STATES =>
						$this->getValidVisibilityStates()
				)
			)
		;
	}

	/** @return array */
	private function getValidVisibilityStates() {
		return $this->cfg(Df_Catalog_Model_Validate_Product_Visibility::VALID_VISIBILITY_STATES);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(
			Df_Catalog_Model_Validate_Product_Visibility::VALID_VISIBILITY_STATES, self::V_ARRAY
		);
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Catalog_Model_Filter_Product_Collection_Visibility
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}