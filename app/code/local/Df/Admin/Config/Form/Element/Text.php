<?php
class Df_Admin_Config_Form_Element_Text extends Varien_Data_Form_Element_Text {
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
	private function getFieldConfig() {return $this->_getData('field_config');}
	
	/** @return bool */
	private function needAutocomplete() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_leaf_b($this->getFieldConfig()->{'rm_autocomplete'}, true);
		}
		return $this->{__METHOD__};
	}
}