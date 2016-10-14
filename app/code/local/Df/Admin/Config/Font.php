<?php
class Df_Admin_Config_Font extends Df_Admin_Config_Extractor {
	/**
	 * @used-by applyLetterCaseToForm()
	 * @used-by Df_Adminhtml_Observer::adminhtml_block_html_before()
	 * @used-by Df_Adminhtml_Observer::core_block_abstract_to_html_before()
	 * @used-by Df_Core_Block_FormattedText::getPreprocessedText()
	 * @param string $text
	 * @return string
	 */
	public function applyLetterCase($text) {
		return Df_Admin_Config_Source_LetterCase::apply($text, $this->lc());
	}

	/**
	 * 2015-02-12
	 * @used-by Df_Adminhtml_Observer::adminhtml_block_html_before()
	 * Обратите внимание, что параметр $element может быть как формой, так и элементом формы,
	 * потому что класс @see Varien_Data_Form_Element_Abstract
	 * является наследником класса @see Varien_Data_Form_Abstract
	 * @param Varien_Data_Form_Abstract $element
	 * @return void
	 */
	public function applyLetterCaseToForm(Varien_Data_Form_Abstract $element) {
		$element['label'] = $this->applyLetterCase((string)$element['label']);
		foreach ($element->getElements() as $child) {
			/** @var Varien_Data_Form_Abstract $child */
			$this->applyLetterCaseToForm($child);
		}
	}

	/**
	 * @used-by Df_Catalog_Block_Frontend_Product_View_Sku::getFormattedLabel()
	 * @used-by Df_Catalog_Block_Frontend_Product_View_Sku::getFormattedValue()
	 * @param string $text
	 * @return string
	 */
	public function applyTo($text) {return Df_Core_Block_FormattedText::render($this, $text, true);}

	/**
	 * @used-by Df_Tweaks_Block_Frontend_Style::adjustLetterCase()
	 * @return string
	 */
	public function getLetterCaseCss() {return Df_Admin_Config_Source_LetterCase::css($this->lc());}

	/** @return bool */
	public function isDefault() {return Df_Admin_Config_Source_LetterCase::isDefault($this->lc());}

	/**
	 * @used-by Df_Tweaks_Block_Frontend_Style::adjustLetterCase()
	 * @return bool
	 */
	public function isUcFirst() {return Df_Admin_Config_Source_LetterCase::isUcFirst($this->lc());}

	/**
	 * @used-by Df_Core_Block_FormattedText::getCssRules()
	 * @used-by Df_Core_Block_FormattedText::getPreprocessedText()
	 * @return boolean
	 */
	public function needSetup() {return $this->getYesNo('setup');}

	/** @return boolean */
	public function useBold() {return $this->getYesNo('emphase__bold');}

	/** @return boolean */
	public function useItalic() {return $this->getYesNo('emphase__italic');}

	/** @return boolean */
	public function useUnderline() {return $this->getYesNo('emphase__underline');}

	/**
	 * @override
	 * @return string
	 */
	protected function getEntityName() {return 'font';}

	/** @return string */
	private function lc() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getValue(
				'letter_case', Df_Admin_Config_Source_LetterCase::_DEFAULT
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @static
	 * @param string $groupPath
	 * @param string $keyPrefix [optional]
	 * @param Df_Core_Model_StoreM|int|string|bool|null [optional]
	 * @return Df_Admin_Config_Font
	 */
	public static function i($groupPath, $keyPrefix = '', $store = null) {
		return self::ic(__CLASS__, $groupPath, $keyPrefix, $store);
	}
}