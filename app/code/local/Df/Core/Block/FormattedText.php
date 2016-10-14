<?php
/** @used-by Df_Admin_Config_Font */
class Df_Core_Block_FormattedText extends Df_Core_Block_Template_NoCache {
	/**
	 * @override
	 * @see Mage_Core_Block_Template::getArea()
	 * @return string
	 */
	public function getArea() {return Df_Core_Const_Design_Area::FRONTEND;}

	/**
	 * @used-by df/core/formattedText.phtml
	 * @return string
	 */
	protected function getCssRulesAsText() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = implode(' ', $this->getCssRules());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by df/core/formattedText.phtml
	 * @return string
	 */
	protected function getDomElementId() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $id */
			$id = $this->cfg(self::$P__DOM_ELEMENT_ID);
			$this->{__METHOD__} = $id ? $id : implode('-', array('rm', df_uid(5)));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by df/core/formattedText.phtml
	 * @return string
	 */
	protected function getPreprocessedText() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = $this->font()->applyLetterCase($this->getRawText());
			if ($this->font()->needSetup()) {
				if ($this->font()->useBold()) {
					$result = rm_tag('strong', array(), $result);
				}
				if ($this->font()->useItalic()) {
					$result = rm_tag('em', array(), $result);
				}
				$result = rm_tag('span', $this->getSpanAttributes(), $result);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by df/core/formattedText.phtml
	 * @return bool
	 */
	protected function isInline() {return $this->cfg(self::$P__INLINE);}

	/** @return string[] */
	private function getCssRules() {
		if (!isset($this->{__METHOD__})) {
			/** @var string[] $result */
			$result = array();
			if ($this->font()->needSetup() && $this->font()->useUnderline()) {
				$result[]= Df_Core_Model_Css_Rule::compose(
					'text-decoration', 'underline', null, !$this->isInline()
				);
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
			if ($this->isInline() && $this->getCssRules()) {
				$result['style'] = $this->getCssRulesAsText();
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/core/formattedText.phtml';}

	/**
	 * @override
	 * @return bool
	 */
	protected function needToShow() {return !!df_trim($this->getRawText());}

	/** @return Df_Admin_Config_Font */
	private function font() {return $this->cfg(self::$P__FONT);}

	/** @return string */
	private function getRawText() {return $this->cfg(self::$P__RAW_TEXT);}

	/** @var string */
	private static $P__DOM_ELEMENT_ID = 'dom_element_id';
	/** @var string */
	private static $P__FONT = 'font';
	/** @var string */
	private static $P__INLINE = 'inline';
	/** @var string */
	private static $P__RAW_TEXT = 'raw_text';

	/**
	 * @used-by Df_Admin_Config_Font::format()
	 * @param Df_Admin_Config_Font $font
	 * @param string $rawText
	 * @param bool $inline
	 * @param string|null $domElementId [optional]
	 * @return string
	 */
	public static function render(
		Df_Admin_Config_Font $font, $rawText, $inline, $domElementId = null
	) {
		return df_trim(rm_render(new self(array(
			Df_Core_Block_FormattedText::$P__FONT => $font
			,Df_Core_Block_FormattedText::$P__RAW_TEXT => $rawText
			,Df_Core_Block_FormattedText::$P__DOM_ELEMENT_ID => $domElementId
			,Df_Core_Block_FormattedText::$P__INLINE => $inline
		))));
	}
}