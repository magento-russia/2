<?php
class Df_Admin_Model_Config_Form_Element_Text extends Varien_Data_Form_Element_Text {
	/**
	 * @override
	 * @param array string[]
	 * @param string $valueSeparator
	 * @param string $fieldSeparator
	 * @param string $quote
	 * @return string
	 */
	public function serialize($attributes = array(), $valueSeparator='=', $fieldSeparator=' ', $quote='"') {
		if (!$this->needAutocomplete()) {
			$this->setData('autocomplete', 'off');
			$attributes[]= 'autocomplete';
		}
     	return parent::serialize($attributes, $valueSeparator, $fieldSeparator, $quote);
 	}

	/** @return Varien_Simplexml_Element */
	private function getFieldConfig() {
		return $this->_getData('field_config');
	}
	
	/** @return bool */
	private function needAutocomplete() {
		if (!isset($this->{__METHOD__})) {
			/** @var bool $result */
			$resultAsMixed = rm_simple_xml_a($this->getFieldConfig(), 'rm_autocomplete');
			/** @var bool $result */
			$this->{__METHOD__} =
				is_null($resultAsMixed)
				? true
				: df_a(
					array(
						'on' => true
						,'off' => false
					)
					,$resultAsMixed
					,rm_bool($resultAsMixed)
				)
			;
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
}