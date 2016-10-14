<?php
class Df_Checkout_Model_Filter_Ergonomic_Address
	extends Df_Core_Model
	implements Zend_Filter_Interface {
	/**
	 * @override
	 * @param array(int => Df_Customer_Model_Address) $value
	 * @return array(int => Df_Customer_Model_Address)
	 */
	public function filter($value) {
		/** @var array(int => Df_Customer_Model_Address) $result */
		$result = array();
		foreach ($value as $id => $address) {
			/** @var int $id */
			/** @var Df_Customer_Model_Address $address */
			$address->setData('address_type', $this->getAddressType());
			if (true === $address->validate()) {
				$result[$id] = $address;
			}
		}
		return $result;
	}

	/** @return string */
	private function getAddressType() {return $this->cfg(self::$P__ADDRESS_TYPE);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__ADDRESS_TYPE, RM_V_STRING_NE);
	}
	const _C = __CLASS__;
	/** @var string */
	private static $P__ADDRESS_TYPE = 'address_type';
	/**
	 * @static
	 * @param string $addressType
	 * @return Df_Checkout_Model_Filter_Ergonomic_Address
	 */
	public static function i($addressType) {
		return new self(array(self::$P__ADDRESS_TYPE => $addressType));
	}
}