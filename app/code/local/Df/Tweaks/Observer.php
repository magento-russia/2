<?php
class Df_Tweaks_Observer {
	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function controller_action_layout_generate_blocks_after(Varien_Event_Observer $observer) {
		try {
			df_handle_event(
				array(
					// Обратите внимание, что перечисленные ниже 3 класса
					// Df_Tweaks_Model_Handler_*_AdjustLinks — разные
					Df_Tweaks_Model_Handler_Account_AdjustLinks::class
					,Df_Tweaks_Model_Handler_Header_AdjustLinks::class
					,Df_Tweaks_Model_Handler_Footer_AdjustLinks::class
					,Df_Tweaks_Model_Handler_Footer_AdjustCopyright::class
					,Df_Tweaks_Model_Handler_ProductBlock_Recent_Compared::class
					,Df_Tweaks_Model_Handler_ProductBlock_Recent_Viewed::class
					,Df_Tweaks_Model_Handler_ProductBlock_Wishlist::class
					,Df_Tweaks_Model_Handler_AdjustBanners::class
					,Df_Tweaks_Model_Handler_AdjustPaypalLogo::class
					,Df_Tweaks_Model_Handler_AdjustPoll::class
					,Df_Tweaks_Model_Handler_AdjustNewsletterSubscription::class
					,Df_Tweaks_Model_Handler_AdjustCartMini::class
					,Df_Tweaks_Model_Handler_AdjustCartPage::class
				)
				,Df_Core_Model_Event_Controller_Action_Layout_GenerateBlocksAfter::class
				,$observer
			);
			if (
					df_handle(Df_Core_Model_Layout_Handle::CATALOG_PRODUCT_VIEW)
				&&
					df_cfg()->tweaks()->catalog()->product()->view()->needHideTags()
			) {
				df_block_remove('product_tag_list');
			}
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function catalog_product_is_salable_after(Varien_Event_Observer $observer) {
		if (df_installed()) {
			if (
						df_handle(Df_Core_Model_Layout_Handle::CATALOG_PRODUCT_VIEW)
					&&
						df_cfg()->tweaks()->catalog()->product()->view()->needHideAddToCart()
				||

						df_cfg()->tweaks()->catalog()->product()->_list()->needHideAddToCart()
					&&
						df_h()->tweaks()->isItCatalogProductList()
			) {
				/** @var Varien_Object $salable */
				$salable = $observer->getData('salable');
				$salable->setData('is_salable', false);
			}
		}
	}
}