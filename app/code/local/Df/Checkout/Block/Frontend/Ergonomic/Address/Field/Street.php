<?php
class Df_Checkout_Block_Frontend_Ergonomic_Address_Field_Street
	extends Df_Checkout_Block_Frontend_Ergonomic_Address_Field_Text {
	/**
	 * @override
	 * @return string
	 */
	public function getDomId() {return $this->getDomIdForStreetLine(1);}
	/**
	 * @param int $lineOrdering
	 * @return string
	 */
	public function getDomIdForStreetLine($lineOrdering) {return parent::getDomId() . $lineOrdering;}
	/** @return string */
	public function getDomName() {return parent::getDomName() . '[]';}
	/** @return int */
	public function getLinesCount() {return df_mage()->customer()->addressHelper()->getStreetLines();}
	/**
	 * @override
	 * @return string
	 */
	public function getValue() {return $this->getValueForStreetLine(1);}
	/**
	 * @param int $lineOrdering
	 * @return string|null
	 */
	public function getValueForStreetLine($lineOrdering) {
		return $this->getAddress()->getAddress()->getStreet($lineOrdering);
	}
	/**
	 * @override
	 * @return string|null
	 */
	protected function getDefaultTemplate() {return 'df/checkout/ergonomic/address/field/street.phtml';}

	const _CLASS = __CLASS__;
}