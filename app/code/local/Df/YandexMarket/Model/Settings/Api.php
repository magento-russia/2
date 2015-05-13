<?php
class Df_YandexMarket_Model_Settings_Api extends Df_Core_Model_Settings {
	/** @return string */
	public function getApplicationId() {return $this->getStringNullable('application_id');}
	/** @return string */
	public function getApplicationPassword() {return $this->getStringNullable('application_password');}
	/** @return string */
	public function getConfirmationCode() {return $this->getStringNullable('confirmation_code');}
	/** @return string */
	public function getToken() {return $this->getStringNullable('token');}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_yandex_market/api/';}
	/** @return Df_YandexMarket_Model_Settings_Api */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}