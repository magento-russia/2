<?php
namespace Df\YandexMarket\Settings;
class Api extends \Df_Core_Model_Settings {
	/** @return string */
	public function appId() {return $this->v('application_id');}
	/** @return string */
	public function getApplicationPassword() {return $this->v('application_password');}
	/** @return string */
	public function getConfirmationCode() {return $this->v('confirmation_code');}
	/** @return string */
	public function getToken() {return $this->v('token');}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_yandex_market/api/';}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}