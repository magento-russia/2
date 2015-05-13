<?php
class Df_Zf_LibLoader extends Df_Core_LibLoader_Abstract {
	/**
	 * @override
	 * @return string[]
	 */
	protected function getScriptsToInclude() {return array('fp');}

	/** @return Df_Zf_LibLoader */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}