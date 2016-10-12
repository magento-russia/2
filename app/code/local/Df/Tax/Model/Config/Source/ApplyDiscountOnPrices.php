<?php
class Df_Tax_Model_Config_Source_ApplyDiscountOnPrices extends Df_Core_Model {
	/** @return array */
	public function toOptionArray() {
		return
			array(
				array(
					'value' => 0
					,'label' => df_mage()->taxHelper()->__('Before Tax')
				)
				,array(
					'value' => 1
					,'label' => df_mage()->taxHelper()->__('After Tax')
				)
			)
		;
	}
}