<?php
class Df_Core_Model_Css extends Df_Varien_Data_Collection {
	/**
	 * @used-by Df_Tweaks_Block_Frontend_Style::adjustReviewsAndRatings()
	 * @used-by Df_Tweaks_Block_Frontend_Style::css()
	 * @param string|string[] $selector
	 * @return void
	 */
	public function addHider($selector) {$this->addSelectorSimple($selector, 'display', 'none');}

	/**
	 * 2015-03-12
	 * После добавления метода @see addSelectorSimple()
	 * метод @see addSelector() перестал где-либо использоваться в текущем коде.
	 * Но метод достаточно разумен и оставлен на будущее.
	 * @param string|string[] $selector
	 * @param Df_Core_Model_Css_Rule_Set $ruleSet
	 * @return void
	 */
	public function addSelector($selector, Df_Core_Model_Css_Rule_Set $ruleSet) {
		$this->addItem(Df_Core_Model_Css_Selector::i($selector, $ruleSet));
	}

	/**
	 * @used-by addHider()
	 * @used-by Df_Tweaks_Block_Frontend_Style::adjustLetterCase()
	 * @param string|string[] $selector
	 * @param string $name
	 * @param string $value
	 * @param string|null $units [optional]
	 * @return void
	 */
	public function addSelectorSimple($selector, $name, $value, $units = null) {
		$this->addSelector($selector, Df_Core_Model_Css_Rule_Set::i($name, $value, $units));
	}

	/**
	 * @used-by Df_Tweaks_Block_Frontend_Style::_toHtml()
	 * @return string
	 */
	public function render() {
		return
			!$this->hasItems()
			? '' :
			df_tag('style', array('type' => 'text/css'), df_tab_multiline(
				implode("\n\n", df_trim($this->walk('render')))
			))
		;
	}

	/**
	 * @override
	 * @see Df_Varien_Data_Collection::itemClass()
	 * @used-by Df_Varien_Data_Collection::addItem()
	 * @return string
	 */
	protected function itemClass() {return Df_Core_Model_Css_Selector::_C;}

	/**
	 * @used-by Df_Tweaks_Block_Frontend_Style::getStyle()
	 * @return Df_Core_Model_Css
	 */
	public static function i() {return new self;}
}