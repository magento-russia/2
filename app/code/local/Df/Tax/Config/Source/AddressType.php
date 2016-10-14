<?php
/**
 * В отличие от стандартного класса @see Mage_Adminhtml_Model_System_Config_Source_Tax_Basedon
 * данный класс использует для перевода модуль Mage_Tax, а не Mage_Adminhtml
 */
class Df_Tax_Config_Source_AddressType {
	/** @return array(array(string => string)) */
	public function toOptionArray() {
		return df_map_to_options(array(
			'shipping' => 'Shipping Address'
			, 'billing' => 'Billing Address'
			, 'origin' => 'Shipping Origin'
		), 'Mage_Tax');
	}
}