<?php
class Df_Sales_Observer {
	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function sales_convert_order_to_quote(Varien_Event_Observer $o) {
		try {
			if (df_cfg()->checkout()->patches()->fixSalesConvertOrderToQuote()) {
				/** @var Df_Sales_Model_Order $sourceOrder */
				$sourceOrder = $o['order'];
				df_assert($sourceOrder instanceof Df_Sales_Model_Order);
				/** @var Mage_Sales_Model_Quote $targetQuote */
				$targetQuote = $o['quote'];
				df_assert($targetQuote instanceof Mage_Sales_Model_Quote);
				$targetQuote->setCustomerLastname($sourceOrder->getCustomerLastname());
				$targetQuote->setCustomerFirstname($sourceOrder->getCustomerFirstname());
				$targetQuote->setCustomerMiddlename($sourceOrder->getCustomerMiddlename());
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
	public function df_adminhtml_block_sales_order_grid__prepare_collection(Varien_Event_Observer $o) {
		try {
			df_handle_event(
				Df_Sales_Model_Handler_AdminOrderGrid_AddProductDataToCollection::class
				,Df_Core_Model_Event_Adminhtml_Block_Sales_Order_Grid_PrepareCollection::class
				,$o
			);
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
	public function df_adminhtml_block_sales_order_grid__prepare_columns_after(Varien_Event_Observer $o) {
		try {
			df_handle_event(
				Df_Sales_Model_Handler_AdminOrderGrid_AddProductColumn::class
				,Df_Core_Model_Event_Adminhtml_Block_Sales_Order_Grid_PrepareColumnsAfter::class
				,$o
			);
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
	public function sales_order_grid_collection_load_before(Varien_Event_Observer $o) {
		try {
			df_handle_event(
				Df_Sales_Model_Handler_AdminOrderGrid_AddProductDataToCollection::class
				,Df_Core_Model_Event_Core_Collection_Abstract_LoadBefore::class
				,$o
			);
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
	public function sales_order_status_history_save_before(Varien_Event_Observer $o) {
		try {
			df_handle_event(
				Df_Sales_Model_Handler_OrderStatusHistory_SetVisibleOnFrontParam::class
				,Df_Sales_Model_Event_OrderStatusHistory_SaveBefore::class
				,$o
			);
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
	public function sales_quote_address_save_before(Varien_Event_Observer $o) {
		/** @var Df_Sales_Model_Quote_Address $quoteAddress */
		$quoteAddress = $o['data_object'];
		if ($quoteAddress) {
			$quoteAddress->convertStreetToText();
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function core_copy_fieldset_customer_address_to_quote_address(Varien_Event_Observer $o) {
		/** @var bool $patchNeeded */
		static $patchNeeded;
		if (is_null($patchNeeded)) {
			$patchNeeded = df_magento_version('1.5.0.1', '=');
		}
		if ($patchNeeded) {
			$quoteAddress = $o['target'];
			/** @var Df_Sales_Model_Quote_Address $quoteAddress */
			$street = $quoteAddress->getStreet();
			if (is_string($street)) {
				// Здесь нужно именно «n»:
				// видимо, эта странная константа и привела к дефекту в ядре Magento CE 1.5.0.1.
				$quoteAddress->setStreet(explode('n', $street));
			}
		}
	}
}