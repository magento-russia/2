<?php
class Df_Checkout_Block_Frontend_Ergonomic_Address_Row extends Df_Core_Block_Abstract_NoCache {
	/** @return Df_Checkout_Model_Collection_Ergonomic_Address_Field */
	public function getFields() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Checkout_Model_Collection_Ergonomic_Address_Field::i();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @see Mage_Core_Block_Abstract::_toHtml()
	 * @used-by Mage_Core_Block_Abstract::toHtml()
	 * @return string
	 */
	protected function _toHtml() {
		return df_tag('li', array('class' => $this->getCssClassesAsText()), df_cc_n(array_map(
			/** @uses wrapField() */
			array($this, 'wrapField')
			/** @uses Df_Checkout_Block_Frontend_Ergonomic_Address_Field::toHtml() */
			,$fieldAsHtml = $this->getFields()->walk('toHtml')
			/** @uses Df_Checkout_Block_Frontend_Ergonomic_Address_Field::getType() */
			,$fieldType = $this->getFields()->walk('getType')
		)));
	}

	/**
	 * @override
	 * @see Df_Core_Block_Abstract::needToShow()
	 * @used-by Df_Core_Block_Abstract::_loadCache()
	 * @used-by Df_Core_Block_Abstract::getCacheKey()
	 * @return bool
	 */
	protected function needToShow() {return $this->getFields()->hasItems();}

	/** @return string */
	private function getCssClassesAsText() {return !$this->hasSingleField() ? 'fields' : 'wide';}

	/** @return bool */
	private function hasSingleField() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = (1 === $this->getFields()->count());
		}
		return $this->{__METHOD__};
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @used-by _toHtml()
	 * http://php.net/manual/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * http://3v4l.org/OipEQ
	 * @param string $fieldAsHtml
	 * @param string $fieldType
	 * @return string
	 */
	private function wrapField($fieldAsHtml, $fieldType) {return
		df_tag('div', ['class' => df_cc_s('field', 'df-field-' . $fieldType)], $fieldAsHtml)
	;}
}