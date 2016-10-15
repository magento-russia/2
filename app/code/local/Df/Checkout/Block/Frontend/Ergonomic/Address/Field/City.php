<?php
class Df_Checkout_Block_Frontend_Ergonomic_Address_Field_City
	extends Df_Checkout_Block_Frontend_Ergonomic_Address_Field_Text {
	/**
	 * @override
	 * @return string|null
	 */
	public function getValue() {
		/** @var string|null $result */
		$result = parent::getValue();
		return $result ? $result :df_visitor_location()->getCity();
	}
}