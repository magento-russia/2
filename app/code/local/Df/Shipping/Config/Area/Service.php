<?php
namespace Df\Shipping\Config\Area;
class Service extends \Df\Shipping\Config\Area {
	/** @return bool */
	public function enableSmsNotification() {return $this->getVarFlag('enable_sms_notification');}

	/** @return bool */
	public function makeAccompanyingForms() {return $this->getVarFlag('make_accompanying_forms');}

	/** @return bool */
	public function needAcceptCashOnDelivery() {return $this->getVarFlag('need_accept_cash_on_delivery');}

	/** @return bool */
	public function needDeliverCargoToTheBuyerHome() {
		return $this->getVarFlag('need_deliver_cargo_to_the_buyer_home');
	}

	/** @return bool */
	public function needGetCargoFromTheShopStore() {
		return $this->getVarFlag('need_get_cargo_from_the_shop_store');
	}

	/** @return bool */
	public function needPacking() {return $this->getVarFlag('need_packing');}

	/**
	 * @override
	 * @return string
	 */
	protected function getAreaPrefix() {return 'service';}
}