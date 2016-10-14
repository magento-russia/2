<?php
class Df_Banner_Block_Banner extends Df_Core_Block_Template {
	/** @return Df_Banner_Model_Banner */
	public function getBanner() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Banner_Model_Banner::ld($this->getBannerId(), 'identifier');
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getBannerId() {
		/** @var string $result */
		$result = $this->cfg('banner_id');
		if (is_null($result)) {
			df_error('Укажите идентификатор рекламного щита');
		}
		return $result;
	}

	/**
	 * @param Df_Banner_Model_Banneritem $bannerItem
	 * @return string
	 */
	public function getBannerItemImageUrl(Df_Banner_Model_Banneritem $bannerItem) {
		return
			is_null($bannerItem->getImageFileName())
			? $bannerItem->getImageUrl()
			: df_concat(
				Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)
				,$bannerItem->getImageFileName()
			)
		;
	}

	/** @return Df_Banner_Model_Resource_Banneritem_Collection */
	public function getBannerItems() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Banner_Model_Resource_Banneritem_Collection $result */
			$result = Df_Banner_Model_Banneritem::c();
			$result->addFieldToFilter('status', true);
			$result->addFieldToFilter('banner_id', $this->getBanner()->getId());
			$result->setOrder('banner_order', 'ASC');
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function isVisible() {
		return !is_null($this->getBanner()) && $this->getBanner()->isEnabled();
	}

	/**
	 * @override
	 * @see Df_Core_Block_Template::cacheKeySuffix()
	 * @used-by Df_Core_Block_Template::getCacheKeyInfo()
	 * @return string|string[]
	 */
	protected function cacheKeySuffix() {return $this->getBannerId();}

	/**
	 * @override
	 * @return bool
	 */
	protected function needToShow() {
		return
				parent::needToShow()
			&&
				df_cfg()->promotion()->banners()->getEnabled()
			&&
				$this->getBanner()->isEnabled()
			&&
				$this->getBannerItems()->count()
		;
	}
}