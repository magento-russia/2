<?php
class Df_YandexMarket_Model_Settings_Shop extends Df_YandexMarket_Model_Settings_Yml {
	/** @return string */
	public function getAgency() {return $this->getString('agency');}
	/** @return string */
	public function getNameForAdministration() {return $this->getString('name_for_administration');}
	/** @return string */
	public function getNameForClients() {return $this->getString('name_for_clients');}
	/** @return string */
	public function getSupportEmail() {return $this->getString('support_email');}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_yandex_market/shop/';}
	/** @return Df_YandexMarket_Model_Settings_Shop */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}