<?php
class Df_Tweaks_Model_Settings_Catalog_Product_View_Sku extends Df_Core_Model_Settings {
	/** @return Df_Admin_Config_Font */
	public function getLabelFont() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Admin_Config_Font::i(
				self::CONFIG_GROUP_PATH, self::CONFIG_KEY_PREFIX__LABEL
			);
		}
		return $this->{__METHOD__};
	}
	/** @return Df_Admin_Config_Font */
	public function getSkuFont() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Admin_Config_Font::i(
				self::CONFIG_GROUP_PATH, self::CONFIG_KEY_PREFIX__SKU
			);
		}
		return $this->{__METHOD__};
	}

	/** @return boolean */
	public function isEnabled() {return $this->getYesNo('enabled');}
	/** @return boolean */
	public function isLabelEnabled() {return $this->getYesNo('show_label');}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_tweaks/catalog_product_view_sku/';}
	const CONFIG_GROUP_PATH = 'df_tweaks/catalog_product_view_sku';
	const CONFIG_KEY_PREFIX__LABEL = 'label';
	const CONFIG_KEY_PREFIX__SKU = 'sku';
	/** @return Df_Tweaks_Model_Settings_Catalog_Product_View_Sku */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}