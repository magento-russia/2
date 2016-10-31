<?php
namespace Df\YandexMarket\Settings;
class General extends Yml {
	/** @return string */
	public function getCurrency() {return df_currency($this->getCurrencyCode());}
	/** @return string */
	public function getCurrencyCode() {return $this->v('currency');}
	/** @return int */
	public function getLocalDeliveryCost() {return $this->nat0('local_delivery_cost');}
	/** @return string */
	public function getNotificationEmail() {return $this->v('notification_email');}
	/** @return string */
	public function getSalesNotes() {return $this->v('sales_notes');}
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
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}