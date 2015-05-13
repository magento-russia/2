<?php
class Df_PromoGift_Block_Chooser extends Df_Core_Block_Template_NoCache {
	/** @return Df_PromoGift_Model_PromoAction_Collection */
	public function getApplicablePromoActions() {return df_h()->promoGift()->getApplicablePromoActions();}

	/**
	 * @param Df_PromoGift_Model_PromoAction $promoAction
	 * @param string $template
	 * @return string
	 */
	public function renderPromoAction(Df_PromoGift_Model_PromoAction $promoAction, $template) {
		df_param_string($template, 1);
		/** @var Df_PromoGift_Block_Chooser_PromoAction $block */
		$block = Df_PromoGift_Block_Chooser_PromoAction::i();
		$block->setPromoAction($promoAction);
		$block->setTemplate($template);
		return $block->renderView();
	}

	/**
	 * @override
	 * @return bool
	 */
	protected function needToShow() {
		return
				parent::needToShow()
			&&
				df_enabled(Df_Core_Feature::PROMO_GIFT)
			&&
				df_cfg()->promotion()->gifts()->getEnabled()
			&&
				$this->isEnabledForCurrentPageType()
			&&
				$this->hasDataToShow()
		;
	}

	/** @return bool */
	private function hasDataToShow() {
		/**
		 * Нельзя писать !!$this->getApplicablePromoActions(),
		 * потому что $this->getApplicablePromoActions() возвращает не массив, а коллекцию.
		 */
		return 0 < $this->getApplicablePromoActions()->count();
	}

	/** @return bool */
	private function isEnabledForCurrentPageType() {
		if (!isset($this->{__METHOD__})) {
			/** @var bool $result */
			$result =
					(
							rm_handle_presents(Df_Core_Model_Layout_Handle::CATALOG_CATEGORY_VIEW)
						&&
							df_cfg()->promotion()->gifts()->needShowChooserOnProductListPage()
					)
				||
					(
							rm_handle_presents(Df_Core_Model_Layout_Handle::CHECKOUT_CART_INDEX)
						&&
							df_cfg()->promotion()->gifts()->needShowChooserOnCartPage()
					)
				||
					(
							rm_handle_presents(Df_Core_Model_Layout_Handle::CMS_PAGE)
						&&
							!rm_handle_presents(Df_Core_Model_Layout_Handle::CMS_INDEX_INDEX)
						&&
							df_cfg()->promotion()->gifts()->needShowChooserOnCmsPage()
					)
				||
					(
							rm_handle_presents(Df_Core_Model_Layout_Handle::CMS_INDEX_INDEX)
						&&
							df_cfg()->promotion()->gifts()->needShowChooserOnFrontPage()
					)
			;
			if (rm_handle_presents(Df_Core_Model_Layout_Handle::CATALOG_PRODUCT_VIEW)) {
				/** @var string $position */
				$position = df_cfg()->promotion()->gifts()->getChooserPositionOnProductViewPage();
				$result =
							(Df_Admin_Model_Config_Source_Layout_Column::OPTION_VALUE__LEFT === $position)
						&&
							('df_promo_gift.chooser.left' === $this->getNameInLayout())
					||
								(Df_Admin_Model_Config_Source_Layout_Column::OPTION_VALUE__RIGHT === $position)
							&&
								('df_promo_gift.chooser.right' === $this->getNameInLayout())
				;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
}