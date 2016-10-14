<?php
/**
 * @method Df_Core_Model_StoreM|int|string|bool|null getStore()
 */
interface Df_Checkout_Module_Main {
	/**
	 * @see Df_Payment_Model_Method::config()
	 * @see Df_Shipping_Carrier::config()
	 * @return Df_Checkout_Module_Config_Facade
	 */
	public function config();

	/**
	 * @used-by Df_Checkout_Module_Bridge::convention()
	 * @used-by Df_Checkout_Module_Config_Manager::s()
	 * @used-by Df_Checkout_Module_Config_Area_No::s()
	 * @see Df_Payment_Model_Method::getCheckoutModuleType()
	 * @see Df_Shipping_Carrier::getCheckoutModuleType()
	 * @return string
	 */
	public function getCheckoutModuleType();

	/**
	 * @used-by Df_Checkout_Module_Config_Manager::getTemplates()
	 * @see Df_Payment_Model_Method::getConfigTemplates()
	 * @see Df_Shipping_Carrier::getConfigTemplates()
	 * @return array(string => string)
	 */
	public function getConfigTemplates();

	/**
	 * @used-by Df_Checkout_Module_Config_Manager::adaptKey()
	 * @used-by Df_Payment_Config_ManagerBase::adaptKey()
	 * @used-by Df_Payment_Model_Method::isActive()
	 * @used-by Df_Shipping_Carrier::isAvailable()
	 * @used-by Df_Shipping_Carrier::getAllowedMethodsAsArray()
	 * @see Df_Payment_Model_Method::getRmId()
	 * @see Df_Shipping_Carrier::getRmId()
	 * @return string
	 */
	public function getRmId();

	/**
	 * @see Df_Payment_Model_Method::getTitle()
	 * @see Df_Shipping_Carrier::getTitle()
	 * @return string
	 */
	public function getTitle();

	/**
	 * Нельзя называть эту константу «_CLASS»,
	 * потому что иначе мы получим сбой для классов, реализующих этот интерфейс:
	 * «Cannot inherit previously-inherited or override constant _CLASS
	 * from interface Df_Checkout_Module_Main»
	 * @used-by Df_Checkout_Module_Config_Facade::_construct()
	 */
	const _INTERFACE = __CLASS__;
}