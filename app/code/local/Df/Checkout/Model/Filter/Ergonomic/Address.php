<?php
class Df_Checkout_Model_Filter_Ergonomic_Address
	extends Df_Core_Model
	implements Zend_Filter_Interface {
	/**
	 * @override
	 * @param array $value
	 * @return array
	 */
	public function filter($value) {
		/** @var array $result */
		$result = array();
		foreach ($value as $id => $address) {
			/** @var int $id */
			/** @var Mage_Customer_Model_Address $address */
			$address->setData('address_type', $this->getAddressType());
			if (true === $address->validate()) {
				$result[$id] = $address;
			}
		}
		return $result;
	}

	/** @return string */
	private function getAddressType() {return $this->cfg(self::P__ADDRESS_TYPE);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__ADDRESS_TYPE, self::V_STRING_NE);
	}

	const _CLASS = __CLASS__;
	const P__ADDRESS_TYPE = 'address_type';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Checkout_Model_Filter_Ergonomic_Address
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}