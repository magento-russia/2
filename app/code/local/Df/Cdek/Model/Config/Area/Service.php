<?php
class Df_Cdek_Model_Config_Area_Service extends Df_Shipping_Model_Config_Area_Service {
	/** @return string */
	public function getShopId() {
		/** @var string $result */
		$result =
			$this->getVar(
				self::KEY__VAR__SHOP_ID
				,/**
				 * Поле — необязательно для заполнения
				 */
				''
			)
		;
		df_result_string($result);
		return $result;
	}

	/** @return string */
	public function getShopPassword() {
		/** @var string|null $encryptedValue */
		$encryptedValue = $this->getVar(self::KEY__VAR__SHOP_PASSWORD);
		return !$encryptedValue ? '' : $this->decrypt($encryptedValue);
	}

	const _CLASS = __CLASS__;
	const KEY__VAR__SHOP_ID = 'shop_id';
	const KEY__VAR__SHOP_PASSWORD = 'shop_password';
}