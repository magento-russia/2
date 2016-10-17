<?php
class Df_YandexMarket_Settings_General extends Df_YandexMarket_Settings_Yml {
	/** @return string */
	public function getCurrency() {return Df_Directory_Model_Currency::ld($this->getCurrencyCode());}
	/** @return string */
	public function getCurrencyCode() {return $this->getString('currency');}
	/** @return int */
	public function getLocalDeliveryCost() {return $this->getNatural0('local_delivery_cost');}
	/** @return string */
	public function getNotificationEmail() {return $this->getStringNullable('notification_email');}
	/** @return string */
	public function getSalesNotes() {return $this->getStringNullable('sales_notes');}
	/** @return boolean */
	public function hasPointsOfSale() {return $this->getYesNo('has_points_of_sale');}
	/** @return boolean */
	public function isEnabled() {return $this->getYesNo('enabled');}
	/** @return boolean */
	public function isPickupAvailable() {return $this->getYesNo('pickup');}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_yandex_market/general/';}
	/** @return Df_YandexMarket_Settings_General */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}