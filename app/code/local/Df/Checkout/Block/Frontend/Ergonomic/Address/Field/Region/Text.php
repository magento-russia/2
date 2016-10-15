<?php
class Df_Checkout_Block_Frontend_Ergonomic_Address_Field_Region_Text
	extends Df_Checkout_Block_Frontend_Ergonomic_Address_Field_Text {
	/**
	 * @override
	 * @return string|null
	 */
	public function getValue() {
		/** @var string|null $result */
		$result = parent::getValue();
		return $result ? $result :df_visitor_location()->getRegionName();
	}

	/**
	 * @override
	 * @see Df_Checkout_Block_Frontend_Ergonomic_Address_Field::getCssClasses()
	 * @used-by Df_Checkout_Block_Frontend_Ergonomic_Address_Field::getCssClassesAsText()
	 * @return string[]
	 */
	protected function getCssClasses() {
		return array_merge(array('rm.validate.region.text'), parent::getCssClasses());
	}
}