<?php
class Df_Tax_Model_Config_Source_AddressType extends Df_Core_Model {
	/** @return array */
	public function toOptionArray() {
		return array(
			/**
			 * В отличие от стандартного класса Mage_Adminhtml_Model_System_Config_Source_Tax_Basedon
			 * данный класс использует для перевода модуль Tax, а не Adminhtml
			 */
			array(
				'value' => 'shipping'
				,'label' => df_mage()->taxHelper()->__('Shipping Address'))
				,array(
					'value' => 'billing'
					,'label' => df_mage()->taxHelper()->__('Billing Address')
				)
				,array(
					'value' => 'origin'
					,'label' => df_mage()->taxHelper()->__('Shipping Origin')
				)
			)
		;
	}
}