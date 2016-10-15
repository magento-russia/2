<?php
class Df_Reports_Observer {
	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function adminhtml_block_html_before(Varien_Event_Observer $o) {
		try {
			/**
			 * Для ускорения работы системы проверяем класс блока прямо здесь,
			 * а не в обработчике события.
			 * Это позволяет нам не создавать обнаботчики событий для каждого блока.
			 */
			/** @var Mage_Core_Block_Abstract $block */
			$block = $o['block'];
			if (
				$block instanceof Mage_Adminhtml_Block_Report_Filter_Form
				&& df_cfg()->reports()->common()->enableGroupByWeek()
			) {
				df_handle_event(
					Df_Reports_Model_Handler_GroupResultsByWeek_AddOptionToFilter::class
					,Df_Core_Model_Event_Adminhtml_Block_HtmlBefore::class
					,$o
				);
			}
			if (
				$block instanceof Mage_Adminhtml_Block_Report_Filter_Form
				&& df_cfg()->reports()->common()->needSetEndDateToTheYesterday()
			) {
				df_handle_event(
					Df_Reports_Model_Handler_SetDefaultFilterValues::class
					,Df_Core_Model_Event_Adminhtml_Block_HtmlBefore::class
					,$o
				);
			}
			if ($block instanceof Mage_Adminhtml_Block_Report_Grid_Abstract
				&& df_cfg()->reports()->common()->enableGroupByWeek()
				&& df_h()->reports()->groupResultsByWeek()->isSelectedInFilter()
			) {
				df_handle_event(
					Df_Reports_Model_Handler_GroupResultsByWeek_SetColumnRenderer::class
					,Df_Core_Model_Event_Adminhtml_Block_HtmlBefore::class
					,$o
				);
			}
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function controller_action_layout_generate_blocks_after(Varien_Event_Observer $o) {
		try {
			if (df_cfg()->reports()->common()->needRemoveTimezoneNotice()) {
				df_handle_event(
					Df_Reports_Model_Handler_RemoveTimezoneNotice::class
					,Df_Core_Model_Event_Controller_Action_Layout_GenerateBlocksAfter::class
					,$o
				);
			}
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function core_collection_abstract_load_before(Varien_Event_Observer $o) {
		try {
			df_handle_event(
				Df_Reports_Model_Handler_GroupResultsByWeek_PrepareCollection::class
				,Df_Core_Model_Event_Core_Collection_Abstract_LoadBefore::class
				,$o
			);
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}
}