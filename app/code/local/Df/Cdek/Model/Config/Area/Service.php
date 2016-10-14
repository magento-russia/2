<?php
class Df_Cdek_Model_Config_Area_Service extends Df_Shipping_Config_Area_Service {
	/**
	 * Поле необязательно для заполнения.
	 * @return string
	 */
	public function getShopId() {return $this->getVar('shop_id', '');}

	/** @return string */
	public function getShopPassword() {
		/** @var string|null $encryptedValue */
		$encryptedValue = $this->getVar('shop_password');
		return !$encryptedValue ? '' : rm_decrypt($encryptedValue);
	}

	const _C = __CLASS__;
}