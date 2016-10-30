<?php
class Df_PromoGift_Block_Chooser extends Df_Core_Block_Template_NoCache {
	/** @return Df_PromoGift_Model_PromoAction_Collection */
	public function getApplicablePromoActions() {return df_h()->promoGift()->getApplicablePromoActions();}

	/**
	 * @override
	 * @return bool
	 */
	protected function needToShow() {
		return
			parent::needToShow()
			&& df_cfgr()->promotion()->gifts()->getEnabled()
			&& $this->isEnabledForCurrentPageType()
			&& $this->hasDataToShow()
		;
	}

	/** @return bool */
	private function hasDataToShow() {return $this->getApplicablePromoActions()->hasItems();}

	/** @return bool */
	private function isEnabledForCurrentPageType() {
		if (!isset($this->{__METHOD__})) {
			/** @var bool $result */
			$result =
					(
							df_handle(Df_Core_Model_Layout_Handle::CATALOG_CATEGORY_VIEW)
						&&
							df_cfgr()->promotion()->gifts()->needShowChooserOnProductListPage()
					)
				||
					(
							df_handle(Df_Core_Model_Layout_Handle::CHECKOUT_CART_INDEX)
						&&
							df_cfgr()->promotion()->gifts()->needShowChooserOnCartPage()
					)
				||
					(
							df_handle(Df_Core_Model_Layout_Handle::CMS_PAGE)
						&&
							!df_handle(Df_Core_Model_Layout_Handle::CMS_INDEX_INDEX)
						&&
							df_cfgr()->promotion()->gifts()->needShowChooserOnCmsPage()
					)
				||
					(
							df_handle(Df_Core_Model_Layout_Handle::CMS_INDEX_INDEX)
						&&
							df_cfgr()->promotion()->gifts()->needShowChooserOnFrontPage()
					)
			;
			if (df_handle(Df_Core_Model_Layout_Handle::CATALOG_PRODUCT_VIEW)) {
				/** @var string $position */
				$position = df_cfgr()->promotion()->gifts()->getChooserPositionOnProductViewPage();
				$result =
							Df_Admin_Config_Source_Layout_Column::isLeft($position)
						&&
							'df_promo_gift.chooser.left' === $this->getNameInLayout()
					||
							Df_Admin_Config_Source_Layout_Column::isRight($position)
						&&
							'df_promo_gift.chooser.right' === $this->getNameInLayout()
				;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}


}