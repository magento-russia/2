<?php
class Df_Pel_LibLoader extends Df_Core_LibLoader_Abstract {
	/**
	 * @override
	 * @throws Exception
	 * @return Df_Pel_LibLoader
	 */
	public function __construct() {
		parent::__construct();
		if (0 !== intval(ini_get('mbstring.func_overload'))) {
			throw new Exception('Df_Pel: you must disable mbstring.func_overload!');
		}
		return $this;
	}

	/**
	 * @override
	 * @return int
	 */
	protected function getIncompatibleErrorLevels() {
		if (!defined('E_DEPRECATED')) {
			define('E_DEPRECATED', 8192);
		}
		return E_STRICT | E_NOTICE | E_WARNING | E_DEPRECATED;
	}

	/** @return Df_Pel_LibLoader */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}