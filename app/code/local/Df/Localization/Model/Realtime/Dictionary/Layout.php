<?php
class Df_Localization_Model_Realtime_Dictionary_Layout
	extends Df_Core_Model_SimpleXml_Parser_Entity {

	/** @return Df_Localization_Model_Realtime_Dictionary_ModulePart_Terms */
	public function getTerms() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Localization_Model_Realtime_Dictionary_ModulePart_Terms::i($this->e())
			;
		}
		return $this->{__METHOD__};
	}
	const _CLASS = __CLASS__;

	/**
	 * @static
	 * @param Df_Varien_Simplexml_Element|string $simpleXml
	 * @return Df_Localization_Model_Realtime_Dictionary_Layout
	 */
	public static function i($simpleXml) {return self::_c($simpleXml, __CLASS__);}
}