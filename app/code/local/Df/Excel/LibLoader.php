<?php
class Df_Excel_LibLoader extends Df_Core_LibLoader_Abstract {
	/**
	 * @override
	 * @return string[]
	 */
	protected function getScriptsToInclude() {return array('PHPExcel');}

	/** @return Df_Excel_LibLoader */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}
