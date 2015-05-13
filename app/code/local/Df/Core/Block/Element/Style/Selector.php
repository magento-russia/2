<?php
class Df_Core_Block_Element_Style_Selector extends Df_Core_Block_Abstract {
	/**
	 * @override
	 * @return string
	 */
	protected function _toHtml() {
		return $this->getSelector() . " {\n" . df_tab_multiline($this->getRulesAsText()) . "\n}";
	}

	/**
	 * @override
	 * @return bool
	 */
	protected function needCaching() {return true;}

	/**
	 * @override
	 * @return bool
	 */
	protected function needCachingPerRequestAction() {return true;}

	/** @return string */
	private function getRulesAsText() {
		return df_trim(implode($this->getRuleSet()->walk('getText', array($inline = false))));
	}

	/** @return Df_Core_Model_Output_Css_Rule_Set */
	private function getRuleSet() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Core_Model_Output_Css_Rule_Set $result */
			$result = $this->cfg(self::P__RULE_SET);
			if (!$result) {
				$result = Df_Core_Model_Output_Css_Rule_Set::i();
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getSelector() {
		/** @var string $result */
		$result = $this->cfg(self::P__SELECTOR);
		if (!$result) {
			df_notify('Требуется селектор');
			/**
			 * Иначе df_result_string приведёт к сбою браузера:
			 *
			 * Content Encoding Error
			 * The page you are trying to view cannot be shown
			 * because it uses an invalid or unsupported form of compression.
			 * Please contact the website owners to inform them of this problem.
			 */
			$result = '';
		}
		return $result;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__RULE_SET, Df_Core_Model_Output_Css_Rule_Set::_CLASS, false)
			->_prop(self::P__SELECTOR, self::V_STRING)
		;
	}
	const P__RULE_SET = 'rule_set';
	const P__SELECTOR = 'selector';
	/**
	 * @param string $selector
	 * @param Df_Core_Model_Output_Css_Rule_Set $ruleSet
	 * @param string|null $cacheKeySuffix [optional]
	 * @return Df_Core_Block_Element_Style_Selector
	 */
	public static function i(
		$selector, Df_Core_Model_Output_Css_Rule_Set $ruleSet, $cacheKeySuffix = null
	) {
		return df_block(__CLASS__, null, array(
			self::P__RULE_SET => $ruleSet
			, self::P__SELECTOR => $selector
			/**
			 * Обратите внимание, что полагаться на значение по умолчанию ($this->getSelector())
			 * в качестве идентификатора для кэширования не всегда правильно.
			 * Если необходимо иметь несколько разных правил CSS с одинаковым селектором,
			 * то указывайте идентификатор для кэширования явно!
			 */
			, self::P__CACHE_KEY_SUFFIX => $cacheKeySuffix ? $cacheKeySuffix : $selector
		));
	}
}