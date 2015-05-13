<?php
class Df_Page_Model_Html_Head_JQuery_Local extends Df_Page_Model_Html_Head_JQuery_Abstract {
	/**
	 * @override
	 * @return string
	 */
	protected function getConfigSuffix() {return 'local';}

	/**
	 * @override
	 * @param string $format
	 * @param mixed[] $staticItems
	 * @return string
	 */
	protected function processInternal($format, array &$staticItems) {
		/** @var array(string => string) $row */
		$row = df_a($staticItems, null);
		df_array_unshift_assoc($row, $this->getPathMigrate(), $this->getPathMigrate());
		df_array_unshift_assoc($row, $this->getPath(), $this->getPath());
		$staticItems[null] = $row;
		return '';
	}

	/** @return Df_Page_Model_Html_Head_JQuery_Local */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}