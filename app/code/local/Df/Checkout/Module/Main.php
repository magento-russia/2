<?php
namespace Df\Checkout\Module;
/** @method \Df_Core_Model_StoreM|int|string|bool|null getStore() */
interface Main {
	/**
	 * @see \Df\Payment\Method::config()
	 * @see \Df\Shipping\Carrier::config()
	 * @return Config\Facade
	 */
	public function config();

	/**
	 * @used-by \Df\Checkout\Module\Bridge::convention()
	 * @used-by \Df\Checkout\Module\Config\Manager::s()
	 * @used-by \Df\Checkout\Module\Config\Area_No::s()
	 * @see \Df\Payment\Method::getCheckoutModuleType()
	 * @see \Df\Shipping\Carrier::getCheckoutModuleType()
	 * @return string
	 */
	public function getCheckoutModuleType();

	/**
	 * @used-by \Df\Checkout\Module\Config\Manager::getTemplates()
	 * @see \Df\Payment\Method::getConfigTemplates()
	 * @see \Df\Shipping\Carrier::getConfigTemplates()
	 * @return array(string => string)
	 */
	public function getConfigTemplates();

	/**
	 * @used-by \Df\Checkout\Module\Config\Manager::adaptKey()
	 * @used-by \Df\Payment\Config\ManagerBase::adaptKey()
	 * @used-by \Df\Payment\Method::isActive()
	 * @used-by \Df\Shipping\Carrier::isAvailable()
	 * @used-by \Df\Shipping\Carrier::getAllowedMethodsAsArray()
	 * @see \Df\Payment\Method::getRmId()
	 * @see \Df\Shipping\Carrier::getRmId()
	 * @return string
	 */
	public function getRmId();

	/**
	 * @see \Df\Payment\Method::getTitle()
	 * @see \Df\Shipping\Carrier::getTitle()
	 * @return string
	 */
	public function getTitle();
}