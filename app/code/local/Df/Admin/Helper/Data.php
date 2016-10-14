<?php
class Df_Admin_Helper_Data extends Mage_Adminhtml_Helper_Data {
	/** @return string */
	public function getAdminUrl() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_concat_url(Mage::getBaseUrl(), rm_leaf_sne(rm_config_node(
				rm_leaf_b(rm_config_node(self::XML_PATH_USE_CUSTOM_ADMIN_PATH))
				? self::XML_PATH_CUSTOM_ADMIN_PATH
				: self::XML_PATH_ADMINHTML_ROUTER_FRONTNAME
			)));
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Admin_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}