<?php
class Df_Core_Model_Css_Selector extends Df_Core_Model {
	/**
	 * @used-by Df_Core_Model_Css render()
	 * @return string
	 */
	public function render() {
		return $this->getSelector() . " {\n" . df_tab_multiline($this->getRulesAsText()) . "\n}";
	}

	/** @return string */
	private function getRulesAsText() {return $this->getRuleSet()->getText($inline = false);}

	/** @return Df_Core_Model_Css_Rule_Set */
	private function getRuleSet() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Core_Model_Css_Rule_Set $result */
			$result = $this->cfg(self::$P__RULE_SET);
			$this->{__METHOD__} = $result ? $result : Df_Core_Model_Css_Rule_Set::i();
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getSelector() {
		/** @var string $result */
		$result = $this->cfg(self::$P__SELECTOR);
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
			->_prop(self::$P__RULE_SET, Df_Core_Model_Css_Rule_Set::_C, false)
			->_prop(self::$P__SELECTOR, DF_V_STRING)
		;
	}
	/** @used-by Df_Core_Model_Css::itemClass() */
	const _C = __CLASS__;
	/** @var string */
	private static $P__RULE_SET = 'rule_set';
	/** @var string */
	private static $P__SELECTOR = 'selector';
	/**
	 * @used-by Df_Core_Model_Css::addSelector()
	 * @param string|string[] $selector
	 * @param Df_Core_Model_Css_Rule_Set $ruleSet
	 * @return Df_Core_Model_Css_Selector
	 */
	public static function i($selector, Df_Core_Model_Css_Rule_Set $ruleSet) {
		if (is_array($selector)) {
			$selector = implode("\n,", $selector);
		}
		return new self(array(self::$P__RULE_SET => $ruleSet, self::$P__SELECTOR => $selector));
	}
}