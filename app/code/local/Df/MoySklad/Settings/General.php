<?php
// 2016-10-09
class Df_MoySklad_Settings_General extends Df_Core_Model_Settings {
	/** @return boolean */
	public function enabled() {return $this->getYesNo(__FUNCTION__);}
	/** @return string */
	public function login() {return $this->v(__FUNCTION__);}
	/** @return string */
	public function password() {return $this->getPassword(__FUNCTION__);}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_moysklad/general/';}

	/** @return Df_MoySklad_Settings_General */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}