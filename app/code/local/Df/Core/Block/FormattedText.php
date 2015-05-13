<?php
class Df_Core_Block_FormattedText extends Df_Core_Block_Template_NoCache {
	/**
	 * @override
	 * @return string
	 */
	public function getArea() {return Df_Core_Const_Design_Area::FRONTEND;}

	/** @return string */
	public function getCssRulesAsText() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = implode(' ', $this->getCssRules());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getDomElementId() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $id */
			$id = $this->cfg(self::P__DOM_ELEMENT_ID);
			$this->{__METHOD__} = $id ? $id : implode('-', array('rm', rm_uniqid(5)));
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getPreprocessedText() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result =
				df_text()->formatCase(
					$this->getRawText()
					,$this->getFontConfig()->getLetterCase()
				)
			;
			if ($this->getFontConfig()->needSetup()) {
				if ($this->getFontConfig()->useBold()) {
					$result = rm_tag('strong', array(), $result);
				}
				if ($this->getFontConfig()->useItalic()) {
					$result = rm_tag('em', array(), $result);
				}
				$result = rm_tag('span', $this->getSpanAttributes(), $result);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function useInlineCssRules() {return $this->cfg(self::P__USE_INLINE_CSS_RULES, false);}

	/** @return string[] */
	private function getCssRules() {
		if (!isset($this->{__METHOD__})) {
			/** @var string[] $result */
			$result = array();
			if ($this->getFontConfig()->needSetup()) {
				if ($this->getFontConfig()->useUnderline()) {
					$result[]=
						Df_Core_Model_Output_Css_Rule::compose(
							'text-decoration'
							,'underline'
							,null
							,!$this->useInlineCssRules()
						)
					;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string[] */
	private function getSpanAttributes() {
		if (!isset($this->{__METHOD__})) {
			/** @var string[] $result */
			$result = array('id' => $this->getDomElementId());
			if ($this->useInlineCssRules() && $this->getCssRules()) {
				$result['style'] = $this->getCssRulesAsText();
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}
	/**
	 * @override
	 * @return string|null
	 */
	protected function getDefaultTemplate() {return 'df/core/formattedText.phtml';}
	/**
	 * @override
	 * @return bool
	 */
	protected function needToShow() {return !!df_trim($this->getRawText());}

	/** @return Df_Admin_Model_Config_Extractor_Font */
	private function getFontConfig() {return $this->cfg(self::P__FONT_CONFIG);}

	/** @return string */
	private function getRawText() {return $this->cfg(self::P__RAW_TEXT);
	}
	const DEFAULT_TEMPLATE = 'df/core/formattedText.phtml';
	const P__DOM_ELEMENT_ID = 'dom_element_id';
	const P__FONT_CONFIG = 'font_config';
	const P__RAW_TEXT = 'raw_text';
	const P__USE_INLINE_CSS_RULES = 'use_inline_css_rules';
}