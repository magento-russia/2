<?php
class Df_Checkout_Block_Frontend_Ergonomic_Address_Field_Country
	extends Df_Checkout_Block_Frontend_Ergonomic_Address_Field_Dropdown {
	/** @return string */
	public function getDropdownAsHtml() {
		return rm_html_select(rm_countries_options(), $this->getValue(), $this->getAttributes());
	}

	/**
	 * Возвращает 2-буквенный код страны по стандарту ISO 3166-1 alpha-2.
	 * https://ru.wikipedia.org/wiki/ISO_3166-1
	 * @override
	 * @return string
	 */
	public function getValue() {
		/** @var string $result */
		$result = $this->getAddress()->getAddress()->getCountryId();
		return $result ? $result : Df_Core_Helper_DataM::s()->getDefaultCountry();
	}

	/**
	 * @override
	 * @return string|null
	 */
	protected function defaultTemplate() {return 'df/checkout/ergonomic/address/field/country.phtml';}

	/** @return array(string => string) */
	private function getAttributes() {
		/** @var array(string => string) $result */
		$result = array(
			'name' => $this->getDomName()
			,'id' => $this->getDomId()
			,'title' => $this->getLabel()
			,'class' => $this->getCssClassesAsText()
		);
		if ($this->getAddress()->isShipping()) {
			$result['onchange'] = 'shipping.setSameAsBilling(false);';
		}
		return $result;
	}
}