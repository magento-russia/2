<?php
class Df_Checkout_Model_Settings_Other extends Df_Core_Model_Settings {
	/** @return string */
	public function getAlphabet() {return $this->v('alphabet');}
	/** @return string */
	public function getColorFailure() {return $this->v('color__failure');}
	/** @return boolean */
	public function canGetAddressFromYandexMarket() {
		return $this->getYesNo('can_get_address_from_yandex_market');
	}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_checkout/other/';}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}