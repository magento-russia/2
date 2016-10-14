<?php
class Df_Admin_Config_DynamicTable_Column_Select
	extends Df_Admin_Config_DynamicTable_Column {
	/**
	 * @used-by Df_Admin_Block_Column_Select::renderTemplateHtml()
	 * @return array(array(string => string))
	 */
	public function getOptions() {return $this->cfg(self::$P__OPTIONS);}

	/**
	 * @override
	 * @return string
	 */
	protected function getRendererClass() {return Df_Admin_Block_Column_Select::_C;}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__OPTIONS, DF_V_ARRAY);
	}
	/** @var string */
	private static $P__OPTIONS = 'options';
	/** @used-by Df_Admin_Block_Column_Select::_construct() */
	const _C = __CLASS__;
	/**
	 * @param string $name
	 * @param string $label
	 * @param array(array(string => string)) $options
	 * @param array(string => string) $htmlAttributes [optional]
	 * @param array(string => string) $renderOptions [optional]
	 * @return Df_Admin_Config_DynamicTable_Column
	 */
	public static function i(
		$name, $label, array $options, array $htmlAttributes = array(), array $renderOptions = array()
	) {
		df_param_string_not_empty($name, 0);
		df_param_string_not_empty($label, 1);
		return new self(array(
			self::$P__NAME => $name
			, self::$P__LABEL => $label
			, self::$P__OPTIONS => $options
			, self::$P__HTML_ATTRIBUTES => $htmlAttributes
			, self::$P__RENDER_OPTIONS => $renderOptions
		));
	}
}