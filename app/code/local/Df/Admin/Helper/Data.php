<?php
class Df_Admin_Helper_Data extends Mage_Adminhtml_Helper_Data {
	/** @return string */
	public function getAdminUrl() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Core_Model_Config_Element $route */
			$route =
				Mage::getConfig()->getNode(
					rm_bool(Mage::getConfig()->getNode(self::XML_PATH_USE_CUSTOM_ADMIN_PATH))
					? self::XML_PATH_CUSTOM_ADMIN_PATH
					: self::XML_PATH_ADMINHTML_ROUTER_FRONTNAME
				)
			;
			// Обратите внимание, что $route неявно преобразуется в строку
			$this->{__METHOD__} = df_concat_url(Mage::getBaseUrl(), $route);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Admin_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}