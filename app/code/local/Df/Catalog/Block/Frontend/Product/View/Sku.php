<?php
class Df_Catalog_Block_Frontend_Product_View_Sku extends Df_Core_Block_Template {
	/** @return string */
	public function getOutput() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				rm_concat_clean(': ', $this->getFormattedLabel(), $this->getFormattedValue())
			;
		}
		return $this->{__METHOD__};
	}
	
	/** @return string */
	public function getSku() {return $this->getProduct()->getSku();}

	/**
	 * @override
	 * @see Df_Core_Block_Template::cacheKeySuffix()
	 * @used-by Df_Core_Block_Template::getCacheKeyInfo()
	 * @return string|string[]
	 */
	protected function cacheKeySuffix() {return $this->getProduct()->getId();}

	/**
	 * @override
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/catalog/product/view/sku.phtml';}

	/**
	 * @override
	 * @return bool
	 */
	protected function needToShow() {
		return df_module_enabled(Df_Core_Module::TWEAKS) && $this->settings()->isEnabled();
	}

	/** @return string */
	private function getFormattedLabel() {
		return
			!$this->settings()->isLabelEnabled()
			? ''
			: $this->settings()->getLabelFont()->applyTo(df_mage()->catalogHelper()->__('Sku'))
		;
	}

	/** @return string */
	private function getFormattedValue() {
		return $this->settings()->getSkuFont()->applyTo($this->getSku());
	}

	/** @return Df_Catalog_Model_Product */
	private function getProduct() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Mage::registry('product');
			df_assert($this->{__METHOD__} instanceof Df_Catalog_Model_Product);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Tweaks_Model_Settings_Catalog_Product_View_Sku */
	private function settings() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_cfg()->tweaks()->catalog()->product()->view()->sku();
		}
		return $this->{__METHOD__};
	}
}