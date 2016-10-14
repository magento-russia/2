<?php
class Df_Checkout_Model_Config_Query_Ergonomic_Address_Fields extends Df_Core_Model {
	/** @return Mage_Core_Model_Config_Element */
	public function getNode() {
		if (!isset($this->{__METHOD__})) {
			// Обязательно клонируем объект,
			// потому что Magento кэширует настроечные узлы
			$this->{__METHOD__} = clone rm_config_node($this->getPathByAddressType('default'));
			$this->{__METHOD__}->extend(
				rm_config_node($this->getPathByAddressType($this->getAddressType()))
				,$overwrite = true
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $addressType
	 * @return Mage_Core_Model_Config_Element
	 */
	private function getNodeByAddressType($addressType) {
		return df_cc_path('df/checkout/address', $addressType, 'fields');
	}

	/**
	 * @param string $addressType
	 * @return string
	 */
	private function getPathByAddressType($addressType) {
		return df_cc_path('df/checkout/address', $addressType, 'fields');
	}

	/** @return string */
	private function getAddressType() {return $this->cfg(self::P__ADDRESS_TYPE);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__ADDRESS_TYPE, DF_V_STRING_NE);
	}
	const _C = __CLASS__;
	const P__ADDRESS_TYPE = 'address_type';
	/**
	 * @static
	 * @param string $type
	 * @return Df_Checkout_Model_Config_Query_Ergonomic_Address_Fields
	 */
	public static function i($type) {return new self(array(self::P__ADDRESS_TYPE => $type));}
}