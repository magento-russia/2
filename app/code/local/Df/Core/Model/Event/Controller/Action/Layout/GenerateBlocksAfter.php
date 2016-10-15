<?php
/**
 * Cообщение:		«controller_action_layout_generate_blocks_after»
 * Источник:		Mage_Core_Controller_Varien_Action::generateLayoutBlocks()
 * [code]
		if (!$this->getFlag('', self::FLAG_NO_DISPATCH_BLOCK_EVENT)) {
			Mage::dispatchEvent(
				'controller_action_layout_generate_blocks_after',array('action'=>$this, 'layout'=>$this->getLayout())
			);
		}
 * [/code]
 */
class Df_Core_Model_Event_Controller_Action_Layout_GenerateBlocksAfter
	extends Df_Core_Model_Event_Controller_Action_Layout {
	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedEventPrefix() {return 'controller_action_layout_generate_blocks_after';}
	/**
	 * @used-by Df_Cms_Observer::controller_action_layout_generate_blocks_after()
	 * @used-by Df_Cms_Model_Handler_ContentsMenu_Insert::getEventClass()
	 * @used-by Df_Reports_Observer::controller_action_layout_generate_blocks_after()
	 * @used-by Df_Reports_Model_Handler_RemoveTimezoneNotice::getEventClass()
	 * @used-by Df_Tweaks_Observer::controller_action_layout_generate_blocks_after()
	 * @used-by Df_Tweaks_Model_Handler_AdjustBanners::getEventClass()
	 * @used-by Df_Tweaks_Model_Handler_AdjustCartPage::getEventClass()
	 * @used-by Df_Tweaks_Model_Handler_AdjustNewsletterSubscription::getEventClass()
	 * @used-by Df_Tweaks_Model_Handler_AdjustPaypalLogo::getEventClass()
	 * @used-by Df_Tweaks_Model_Handler_AdjustPoll::getEventClass()
	 * @used-by Df_Tweaks_Model_Handler_Remover::getEventClass()
	 * @used-by Df_Tweaks_Model_Handler_Account_AdjustLinks::getEventClass()
	 * @used-by Df_Tweaks_Model_Handler_Footer_AdjustCopyright::getEventClass()
	 * @used-by Df_Tweaks_Model_Handler_Footer_AdjustLinks::getEventClass()
	 * @used-by Df_Tweaks_Model_Handler_Header_AdjustLinks::getEventClass()
	 * @used-by Df_Tweaks_Model_Handler_ProductBlock_Wishlist::getEventClass()
	 * @used-by Df_Tweaks_Model_Handler_ProductBlock_Recent_Viewed::getEventClass()
	 */

}