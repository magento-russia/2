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
					Df_Tweaks_Model_Handler_Account_AdjustLinks::_C
					,Df_Tweaks_Model_Handler_Header_AdjustLinks::_C
					,Df_Tweaks_Model_Handler_Footer_AdjustLinks::_C
					,Df_Tweaks_Model_Handler_Footer_AdjustCopyright::_C
					,Df_Tweaks_Model_Handler_ProductBlock_Recent_Compared::_C
					,Df_Tweaks_Model_Handler_ProductBlock_Recent_Viewed::_C
					,Df_Tweaks_Model_Handler_ProductBlock_Wishlist::_C
					,Df_Tweaks_Model_Handler_AdjustBanners::_C
					,Df_Tweaks_Model_Handler_AdjustPaypalLogo::_C
					,Df_Tweaks_Model_Handler_AdjustPoll::_C
					,Df_Tweaks_Model_Handler_AdjustNewsletterSubscription::_C
					,Df_Tweaks_Model_Handler_AdjustCartMini::_C
					,Df_Tweaks_Model_Handler_AdjustCartPage::_C
				)
				,Df_Core_Model_Event_Controller_Action_Layout_GenerateBlocksAfter::_C
				,$observer
			);
			if (
					rm_handle_presents(Df_Core_Model_Layout_Handle::CATALOG_PRODUCT_VIEW)
				&&
					df_cfg()->tweaks()->catalog()->product()->view()->needHideTags()
			) {
				rm_block_remove('product_tag_list');
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
						rm_handle_presents(Df_Core_Model_Layout_Handle::CATALOG_PRODUCT_VIEW)
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