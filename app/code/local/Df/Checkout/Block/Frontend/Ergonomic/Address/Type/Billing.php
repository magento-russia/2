<?php
class Df_Checkout_Block_Frontend_Ergonomic_Address_Type_Billing
	extends Mage_Checkout_Block_Onepage_Billing {
	/** метод @see Mage_Checkout_Block_Onepage_Billing::__() перекрывать не нужно */
	/**
	 * @override
	 * @return bool
	 */
	public function customerHasAddresses() {return $this->getHtmlSelect()->hasAddresses();}

	/**
	 * @override
	 * @param string $type
	 * @return string
	 */
	public function getAddressesHtmlSelect($type) {
		df_assert_eq('billing', $type);
		return $this->getHtmlSelect()->toHtml();
	}

	/** @return Df_Checkout_Block_Frontend_Ergonomic_Address_HtmlSelect */
	private function getHtmlSelect() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Checkout_Block_Frontend_Ergonomic_Address_HtmlSelect::i(
				$this, 'billing'
			);
		}
		return $this->{__METHOD__};
	}
}