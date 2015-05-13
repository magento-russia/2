<?php
class Df_Reports_Model_Dispatcher {
	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function adminhtml_block_html_before(Varien_Event_Observer $observer) {
		try {
			/**
			 * Для ускорения работы системы проверяем класс блока прямо здесь,
			 * а не в обработчике события.
			 * Это позволяет нам не создавать обнаботчики событий для каждого блока.
			 */
			/** @var Mage_Core_Block_Abstract $block */
			$block = $observer->getData('block');
			if (
					($block instanceof Mage_Adminhtml_Block_Report_Filter_Form)
				&&
					df_cfg()->reports()->common()->enableGroupByWeek()
				&&
					df_enabled(Df_Core_Feature::REPORTS)
			) {
				df_handle_event(
					Df_Reports_Model_Handler_GroupResultsByWeek_AddOptionToFilter::_CLASS
					,Df_Core_Model_Event_Adminhtml_Block_HtmlBefore::_CLASS
					,$observer
				);
			}



			if (
					($block instanceof Mage_Adminhtml_Block_Report_Filter_Form)
				&&
					df_cfg()->reports()->common()->needSetEndDateToTheYesterday()
				&&
					df_enabled(Df_Core_Feature::REPORTS)
			) {
				df_handle_event(
					Df_Reports_Model_Handler_SetDefaultFilterValues::_CLASS
					,Df_Core_Model_Event_Adminhtml_Block_HtmlBefore::_CLASS
					,$observer
				);
			}
			if (
					($block instanceof Mage_Adminhtml_Block_Report_Grid_Abstract)
				&&
					df_cfg()->reports()->common()->enableGroupByWeek()
				&&
					df_h()->reports()->groupResultsByWeek()->isSelectedInFilter()
				&&
					df_enabled(Df_Core_Feature::REPORTS)
			) {
				df_handle_event(
					Df_Reports_Model_Handler_GroupResultsByWeek_SetColumnRenderer::_CLASS
					,Df_Core_Model_Event_Adminhtml_Block_HtmlBefore::_CLASS
					,$observer
				);
			}
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function controller_action_layout_generate_blocks_after(
		Varien_Event_Observer $observer
	) {
		try {
			if (
					df_cfg()->reports()->common()->needRemoveTimezoneNotice()
				&&
					df_enabled(Df_Core_Feature::REPORTS)
			) {
				df_handle_event(
					Df_Reports_Model_Handler_RemoveTimezoneNotice::_CLASS
					,Df_Core_Model_Event_Controller_Action_Layout_GenerateBlocksAfter::_CLASS
					,$observer
				);
			}
		}

		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}

	}

	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function core_collection_abstract_load_before(
		Varien_Event_Observer $observer
	) {
		try {
			df_handle_event(
				Df_Reports_Model_Handler_GroupResultsByWeek_PrepareCollection::_CLASS
				,Df_Core_Model_Event_Core_Collection_Abstract_LoadBefore::_CLASS
				,$observer
			);
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}
}