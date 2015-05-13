<?php
class Df_Adminhtml_Model_Translator_Config extends Df_Core_Model_Abstract {
	/**
	 * @param Mage_Core_Model_Config_Element $section
	 * @return string
	 */
	public function getHelperModuleMf(Mage_Core_Model_Config_Element $section) {
		return df_a($this->getMap(), $section->getName(), 'adminhtml');
	}

	/** @return array(string => string) */
	private function getMap() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Core_Model_Config_Element|bool $node */
			$node = Mage::getConfig()->getNode('adminhtml/translate/sections');
			$this->{__METHOD__} = !$node ? array() : $node->asCanonicalArray();
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Adminhtml_Model_Translator_Config */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}