<?php
class Df_Checkout_Block_Frontend_Ergonomic_Address_Field_Dropdown
	extends Df_Checkout_Block_Frontend_Ergonomic_Address_Field {
	/**
	 * @override
	 * @return string
	 */
	public function getType() {
		return rm_sprintf('%s_id', parent::getType());
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getConfigShortKey() {
		return str_replace('_id', '', $this->getType());
	}

	const _CLASS = __CLASS__;
	/**
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Checkout_Block_Frontend_Ergonomic_Address_Field_Dropdown
	 */
	public static function i($parameters) {return df_block(__CLASS__, null, $parameters);}
}