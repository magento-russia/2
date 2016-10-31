<?php
namespace Df\YandexMarket\Settings;
class Shop extends Yml {
	/** @return string */
	public function getAgency() {return $this->v('agency');}
	/** @return string */
	public function getNameForAdministration() {return $this->v('name_for_administration');}
	/** @return string */
	public function getNameForClients() {return $this->v('name_for_clients');}
	/** @return string */
	public function getSupportEmail() {return $this->v('support_email');}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_yandex_market/shop/';}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}