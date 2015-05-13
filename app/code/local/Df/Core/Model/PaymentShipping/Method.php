<?php
interface Df_Core_Model_PaymentShipping_Method {
	/**
	 * @param string $key
	 * @param bool $canBeTest[optional]
	 * @param string $defaultValue[optional]
	 * @return string
	 */
	public function getConst($key, $canBeTest = true, $defaultValue = '');
	/**
	 * @param string $key
	 * @param bool $canBeTest[optional]
	 * @param string $defaultValue[optional]
	 * @return string
	 */
	public function getConstUrl($key, $canBeTest = true, $defaultValue = '');
	/**
	 * @param int|string|null|Mage_Core_Model_Store $storeId[optional]
	 * @return Df_Core_Model_PaymentShipping_Config_Facade
	 */
	public function getRmConfig($storeId = null);
	/**
	 * Возвращает идентификатор способа доставки внутри Российской сборки
	 * (без приставки «df-»)
	 * Этот метод публичен, потому что использутся классами:
	 * @see Df_Core_Model_PaymentShipping_ConfigManager_Const
	 * @see Df_Core_Model_PaymentShipping_ConfigManager_Var
	 * @return string
	 */
	public function getRmId();
	/** @return string */
	public function getTitle();
}