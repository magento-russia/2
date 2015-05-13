<?php
class Df_Adminhtml_Model_Dispatcher  {
	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function adminhtml_block_html_before(Varien_Event_Observer $observer) {
		try {
			/**
			 * Для ускорения работы системы проверяем класс блока прямо здесь,
			 * а не в обработчике события.
			 * Это позволяет нам не создавать обработчики событий для каждого блока.
			 */
			/** @var Mage_Core_Block_Abstract $block */
			$block = $observer->getData('block');
			if ($block instanceof Mage_Adminhtml_Block_Widget_Form) {
				df_handle_event(
					Df_Adminhtml_Model_Handler_AdjustLabels_Form::_CLASS
					,Df_Core_Model_Event_CoreBlockAbstract_ToHtml_Before::_CLASS
					,$observer
				);
			}
			else if ($block instanceof Mage_Adminhtml_Block_Widget_Grid) {
				df_handle_event(
					Df_Adminhtml_Model_Handler_AdjustLabels_Grid::_CLASS
					,Df_Core_Model_Event_CoreBlockAbstract_ToHtml_Before::_CLASS
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
	public function adminhtml_sales_order_create_process_data_before(Varien_Event_Observer $observer) {
		try {
			/**
			 * @link http://magento-forum.ru/topic/2612/
			 */
			df_handle_event(
				Df_Adminhtml_Model_Handler_Sales_Order_Address_SetRegionName::_CLASS
				,Df_Adminhtml_Model_Event_Sales_Order_Create_Process_Data_Before::_CLASS
				,$observer
			);
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * Заплатка от сбоя
	 * «Notice: Undefined property: Mage_Adminhtml_Block_Report_Product_Sold_Grid::$_filterValues»
	 * @see Mage_Adminhtml_Block_Report_Grid::_prepareCollection()
		Mage::dispatchEvent('adminhtml_widget_grid_filter_collection',
			array('collection' => $this->getCollection(), 'filter_values' => $this->_filterValues)
		);
	 * @link http://magento-forum.ru/topic/4337/
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function adminhtml_widget_container_html_before(Varien_Event_Observer $observer) {
		try {
			/** @var Mage_Adminhtml_Block_Widget_Grid_Container $gridContainer */
			$gridContainer = $observer->getData('block');
			if ($gridContainer instanceof Mage_Adminhtml_Block_Widget_Grid_Container) {
				/** @var Mage_Adminhtml_Block_Widget_Grid $grid */
				$grid = $gridContainer->getChild('grid');
				if ($grid instanceof Mage_Adminhtml_Block_Widget_Grid) {
					if (!isset($grid->_filterValues)) {
						$grid->_filterValues = null;
					}
					/**
					 * @see Mage_Adminhtml_Block_Report_Grid::getLocale()
					 * Свойство _locale используется там без предварительной инициализации:
						if (!$this->_locale) {
							$this->_locale = Mage::app()->getLocale();
						}
					 */
					if (!isset($grid->_locale)) {
						$grid->_locale = null;
					}
				}
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
	public function controller_action_predispatch(Varien_Event_Observer $observer) {
		try {
			df_handle_event(
				Df_Adminhtml_Model_Handler_CorrectUsedModuleName::_CLASS
				,Df_Core_Model_Event_Controller_Action_Predispatch::_CLASS
				,$observer
			);
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function core_block_abstract_to_html_before(Varien_Event_Observer $observer) {
		try {
			/**
			 * Для ускорения работы системы проверяем класс блока прямо здесь,
			 * а не в обработчике события.
			 * Это позволяет нам не создавать обработчики событий для каждого блока.
			 */
			/** @var Mage_Core_Block_Abstract $block */
			$block = $observer->getData('block');
			if ($block instanceof Mage_Adminhtml_Block_Widget_Button) {
				df_handle_event(
					Df_Adminhtml_Model_Handler_AdjustLabels_Button::_CLASS
					,Df_Core_Model_Event_CoreBlockAbstract_ToHtml_Before::_CLASS
					,$observer
				);
			}
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}
}