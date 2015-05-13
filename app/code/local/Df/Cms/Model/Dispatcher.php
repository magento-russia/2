<?php
class Df_Cms_Model_Dispatcher {
	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function controller_action_layout_generate_blocks_after(Varien_Event_Observer $observer) {
		try {
			if (df_cfg()->cms()->hierarchy()->isEnabled() && df_enabled(Df_Core_Feature::CMS_2)) {
				df_handle_event(
					Df_Cms_Model_Handler_ContentsMenu_Insert::_CLASS
					,Df_Core_Model_Event_Controller_Action_Layout_GenerateBlocksAfter::_CLASS
					,$observer
				);
			}
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}
}