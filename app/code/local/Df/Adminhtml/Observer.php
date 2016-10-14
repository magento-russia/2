<?php
class Df_Adminhtml_Observer  {
	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function adminhtml_block_html_before(Varien_Event_Observer $o) {
		try {
			// 2015-03-12
			/** @var Mage_Core_Block_Abstract|Mage_Adminhtml_Block_Widget_Form|Mage_Adminhtml_Block_Widget_Grid $block */
			$block = $o['block'];
			// на странице System - Permissions - Roles $block->getForm() возвращает null
			if ($block instanceof Mage_Adminhtml_Block_Widget_Form && $block->getForm()) {
				/** @var Df_Admin_Config_Font $font */
				$font = df_cfg()->admin()->_interface()->getFormLabelFont();
				$font->applyLetterCaseToForm($block->getForm());
			}
			else if ($block instanceof Mage_Adminhtml_Block_Widget_Grid) {
				/** @var Df_Admin_Config_Font $font */
				$font = df_cfg()->admin()->_interface()->getGridLabelFont();
				foreach ($block->getColumns() as $column) {
					/** @var Varien_Object $column */
					$column['header'] = $font->applyLetterCase((string)$column['header']);
				}
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
	public function adminhtml_sales_order_create_process_data_before(Varien_Event_Observer $o) {
		try {
			/** http://magento-forum.ru/topic/2612/ */
			df_handle_event(
				Df_Adminhtml_Model_Handler_Sales_Order_Address_SetRegionName::_C
				,Df_Adminhtml_Model_Event_Sales_Order_Create_Process_Data_Before::_C
				,$o
			);
		}
		catch (Exception $e) {
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
	 * http://magento-forum.ru/topic/4337/
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function adminhtml_widget_container_html_before(Varien_Event_Observer $o) {
		try {
			/** @var Mage_Adminhtml_Block_Widget_Grid_Container $gridContainer */
			$gridContainer = $o['block'];
			if ($gridContainer instanceof Mage_Adminhtml_Block_Widget_Grid_Container) {
				/** @var Mage_Adminhtml_Block_Widget_Grid $grid */
				$grid = $gridContainer->getChild('grid');
				if ($grid instanceof Mage_Adminhtml_Block_Widget_Grid) {
					if (!isset($grid->_filterValues)) {
						$grid->_filterValues = null;
					}
					/**
					 * @used-by Mage_Adminhtml_Block_Report_Grid::getLocale()
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
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function controller_action_predispatch(Varien_Event_Observer $o) {
		try {
			/** @var Mage_Core_Controller_Varien_Action|Mage_Adminhtml_Controller_Action $controller */
			$controller = $o['controller_action'];
			/**
			 * Некоторые контроллеры,
			 * которые не являются наследниками Mage_Adminhtml_Controller_Action,
			 * пытаются, тем не менее, авторизоваться в административной части.
			 * Без проверки на наследство от Mage_Adminhtml_Controller_Action
			 * это приводит к сбою:
					Call to undefined method Mage_Rss_CatalogController::getUsedModuleName()
				при выполнении кода
			 * 		('adminhtml' === $this->getController()->getUsedModuleName())
			 * Встретил такое в магазине atletica.baaton.com
			 */
			if (
					rm_loc()->isEnabled()
				&&
					$controller instanceof Mage_Adminhtml_Controller_Action
				&&
					'adminhtml' === $controller->getUsedModuleName()
			) {
				$controller->setUsedModuleName(
					Df_Adminhtml_Model_Translator_Controller::s()->getModuleName($controller)
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
	public function core_block_abstract_to_html_before(Varien_Event_Observer $o) {
		try {
			/** @var Mage_Core_Block_Abstract|Mage_Adminhtml_Block_Widget_Button $block */
			$block = $o['block'];
			if ($block instanceof Mage_Adminhtml_Block_Widget_Button) {
				/** @var Df_Admin_Config_Font $font */
				$font = df_cfg()->admin()->_interface()->getButtonLabelFont();
				$block['label'] = $font->applyLetterCase((string)$block['label']);
			}
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}
}