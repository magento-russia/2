<?php
class Df_Page_JQueryInjecter_Google extends Df_Page_JQueryInjecter {
	/**
	 * @override
	 * @see Df_Page_JQueryInjecter::_process()
	 * @used-by Df_Page_JQueryInjecter::process()
	 * @param string $format
	 * @param mixed[] $staticItems
	 * @return string
	 */
	protected function _process($format, array &$staticItems) {
		return df_cc_n(
			Df_Core_Model_Format_Html_Tag::scriptExternal($this->getPath())
			,Df_Core_Model_Format_Html_Tag::scriptLocal(
				// Инициализируем переменную $j для совместимости с Magento CE 1.9 (там так делается)
				'var $j = jQuery.noConflict(); jQuery.migrateMute = true;'
			)
			,Df_Core_Model_Format_Html_Tag::scriptExternal($this->getPathMigrate())
			,''
		);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getConfigSuffix() {return 'cdn';}
}