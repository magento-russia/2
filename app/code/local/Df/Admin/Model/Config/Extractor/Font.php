<?php
class Df_Admin_Model_Config_Extractor_Font extends Df_Admin_Model_Config_Extractor {
	/**
	 * @param string $text
	 * @param string|null $domElementId[optional]
	 * @return string
	 */
	public function format($text, $domElementId = null) {
		df_param_string($text, 0);
		$domElementId = df_nts($domElementId);
		df_param_string($domElementId, 1);
		/** @var string $result */
		$result =
			df_trim(
				df_block_render(
					new Df_Core_Block_FormattedText(
						array(
							Df_Core_Block_FormattedText::P__FONT_CONFIG => $this
							,Df_Core_Block_FormattedText::P__RAW_TEXT => $text
							,Df_Core_Block_FormattedText::P__DOM_ELEMENT_ID => $domElementId
							,Df_Core_Block_FormattedText::P__USE_INLINE_CSS_RULES => true
						)
					)
				)
			)
		;
		df_result_string_not_empty($result);
		return $result;
	}

	/** @return string */
	public function getLetterCase() {
		/** @var string $result */
		$result = $this->getValue(self::CONFIG_KEY__LETTER_CASE);
		if (!$result) {
			$result = Df_Admin_Model_Config_Source_Format_Text_LetterCase::_DEFAULT;
		}
		df_result_string_not_empty($result);
		return $result;
	}

	/** @return Df_Core_Model_Output_Css_Rule */
	public function getLetterCaseAsCssRule() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Core_Model_Output_Css_Rule::i(
					'text-transform'
					,Df_Admin_Model_Config_Source_Format_Text_LetterCase::convertToCss(
						$this->getLetterCase()
					)
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return boolean */
	public function needSetup() {return $this->getYesNo(self::CONFIG_KEY__SETUP);}

	/** @return boolean */
	public function useBold() {return $this->getYesNo(self::CONFIG_KEY__BOLD);}

	/** @return boolean */
	public function useItalic() {return $this->getYesNo(self::CONFIG_KEY__ITALIC);}

	/** @return boolean */
	public function useUnderline() {return $this->getYesNo(self::CONFIG_KEY__UNDERLINE);}

	/**
	 * @override
	 * @return string
	 */
	protected function getEntityName() {return self::CONFIG_ENTITY_NAME;}

	const _CLASS = __CLASS__;
	const CONFIG_ENTITY_NAME = 'font';
	const CONFIG_KEY__SETUP = 'setup';
	const CONFIG_KEY__LETTER_CASE = 'letter_case';
	const CONFIG_KEY__BOLD = 'emphase__bold';
	const CONFIG_KEY__ITALIC = 'emphase__italic';
	const CONFIG_KEY__UNDERLINE = 'emphase__underline';

	/**
	 * @static
	 * @param string $configGroupPath
	 * @param string $configKeyPrefix [optional]
	 * @param Mage_Core_Model_Store|null $store[optional]
	 * @return Df_Admin_Model_Config_Extractor_Font
	 */
	public static function i($configGroupPath, $configKeyPrefix = '', $store = null) {
		return new self(array(
			self::P__CONFIG_GROUP_PATH => $configGroupPath
			, self::P__CONFIG_KEY_PREFIX => $configKeyPrefix
			, self::P__STORE => $store
		));
	}
}