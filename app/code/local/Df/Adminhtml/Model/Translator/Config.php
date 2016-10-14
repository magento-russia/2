<?php
class Df_Adminhtml_Model_Translator_Config extends Df_Core_Model {
	/**
	 * @param string $sectionName
	 * @return string|null
	 */
	public function getHelperModuleMf($sectionName) {return df_a($this->getMap(), $sectionName);}

	/** @return array(string => string) */
	private function getMap() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_config_a('adminhtml/translate/sections');
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Adminhtml_Model_Translator_Config */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}