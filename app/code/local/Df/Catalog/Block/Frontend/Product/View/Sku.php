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
	 * @return string|string[]
	 */
	protected function getCacheKeyParamsAdditional() {return $this->getProduct()->getId();}

	/**
	 * @override
	 * @return string|null
	 */
	protected function getDefaultTemplate() {return 'df/catalog/product/view/sku.phtml';}

	/**
	 * @override
	 * @return bool
	 */
	protected function needToShow() {
		return
				df_enabled(Df_Core_Feature::TWEAKS)
			&&
				df_module_enabled(Df_Core_Module::TWEAKS)
			&&
				$this->getSettings()->isEnabled()
		;
	}

	/** @return string */
	private function getFormattedLabel() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				!$this->getSettings()->isLabelEnabled()
				? ''
				: $this->getSettings()->getLabelFont()->format(
					df_mage()->catalogHelper()->__('Sku')
					,'rm-product-' . $this->getProduct()->getId() . '-sku-label'
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getFormattedValue() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->getSettings()->getSkuFont()->format(
					$this->getSku()
					, 'rm-product-' . $this->getProduct()->getId() . '-sku-value'
				)
			;
		}
		return $this->{__METHOD__};
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
	private function getSettings() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_cfg()->tweaks()->catalog()->product()->view()->sku();
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
}