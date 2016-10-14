<?php
class Df_Page_JQueryInjecter_Local extends Df_Page_JQueryInjecter {
	/**
	 * @override
	 * @see Df_Page_JQueryInjecter::_process()
	 * @used-by Df_Page_JQueryInjecter::process()
	 * @param string $format
	 * @param mixed[] $staticItems
	 * @return string
	 */
	protected function _process($format, array &$staticItems) {
		/** @var array(string => string) $row */
		$row = dfa($staticItems, null);
		dfa_unshift_assoc($row, $this->getPathMigrate(), $this->getPathMigrate());
		dfa_unshift_assoc($row, $this->getPath(), $this->getPath());
		$staticItems[null] = $row;
		return '';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getConfigSuffix() {return 'local';}
}