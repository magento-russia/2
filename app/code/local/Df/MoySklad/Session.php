<?php
namespace Df\MoySklad;
class Session extends \Df_Core_Model_Session_Custom_Primary {
	/**
	 * @override
	 * @return string
	 */
	protected function getSessionIdCustom() {return df_request('df-session');}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}