<?php
/**
 * Cообщение:		«adminhtml_sales_order_create_process_data_before»
 * Источник:		Mage_Adminhtml_Sales_Order_CreateController::_processActionData()
 * [code]
	$eventData = array(
		'order_create_model' => $this->_getOrderCreateModel(),'request_model'	  => $this->getRequest(),'session'			=> $this->_getSession(),);
	Mage::dispatchEvent('adminhtml_sales_order_create_process_data_before', $eventData);
 * [/code]
 */
class Df_Adminhtml_Model_Event_Sales_Order_Create_Process_Data_Before extends Df_Core_Model_Event {
	/** @return Mage_Core_Controller_Request_Http */
	public function getRequest() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getEventParam('request_model');
			df_assert($this->{__METHOD__} instanceof Mage_Core_Controller_Request_Http);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedEventPrefix() {return 'adminhtml_sales_order_create_process_data_before';}

	/**
	 * @used-by Df_Adminhtml_Observer::adminhtml_sales_order_create_process_data_before()
	 * @used-by Df_Adminhtml_Model_Handler_Sales_Order_Address_SetRegionName::getEventClass()
	 */

}