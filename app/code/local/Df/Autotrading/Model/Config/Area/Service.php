<?php
class Df_Autotrading_Model_Config_Area_Service extends Df_Shipping_Model_Config_Area_Service {
	/** @return bool */
	public function checkCargoOnReceipt() {return $this->getVarFlag('check_cargo_on_receipt');}
	/** @return bool */
	public function needBagPacking() {return $this->getVarFlag('need_bag_packing');}
	/** @return bool */
	public function needBox() {return $this->getVarFlag('need_box');}
	/** @return bool */
	public function needCollapsiblePalletBox() {return $this->getVarFlag('need_collapsible_pallet_box');}
	/** @return bool */
	public function needOpenSlatCrate() {return $this->getVarFlag('need_open_slat_crate');}
	/** @return bool */
	public function needTaping() {return $this->getVarFlag('need_taping');}
	/** @return bool */
	public function needTapingAdvanced() {return $this->getVarFlag('need_taping_advanced');}
	/** @return bool */
	public function needPalletPacking() {return $this->getVarFlag('need_pallet_packing');}
	/** @return bool */
	public function needPlywoodBox() {return $this->getVarFlag('need_plywood_box');}
	/** @return bool */
	public function notifySenderAboutDelivery() {return $this->getVarFlag('notify_sender_about_delivery');}
	const _CLASS = __CLASS__;
}