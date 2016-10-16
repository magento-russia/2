<?php
class Df_Logging_Block_Container extends Mage_Adminhtml_Block_Widget_Grid_Container {
	/**
	 * @deprecated after 1.6.0.0
	 * @return object Df_Logging_Model_Event
	 */
	public function getEventData() {
		/** @noinspection PhpDeprecationInspection */
		if (!$this->_eventData) {
			/** @noinspection PhpDeprecationInspection */
			$this->_eventData = Mage::registry('current_event');
		}
		/** @noinspection PhpDeprecationInspection */
		return $this->_eventData;
	}

	/**
	 * Replace /n => <br /> in event error_message
	 * @deprecated after 1.6.0.0
	 * @return string
	 */
	public function getEventError() {
		return df_t()->nl2br($this->getEventData()->getErrorMessage());
	}

	/**
	 * Convert ip to string
	 * @deprecated after 1.6.0.0
	 * @return string
	 */
	public function getEventIp() {
		return long2ip($this->getEventData()->getIp());
	}

	/**
	 * Convert x_forwarded_ip to string
	 * @deprecated after 1.6.0.0
	 * @return string
	 */
	public function getEventXForwardedIp() {
		return long2ip($this->getEventData()->getXForwardedIp());
	}

	/**
	 * @override
	 * @return string
	 */
	public function getHeaderText() {
		return Df_Logging_Helper_Data::s()->__($this->_getData('header_text'));
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$action = Mage::app()->getRequest()->getActionName();
		$this->_blockGroup = 'df_logging';
		$this->_controller = 'adminhtml_' . $action;
		$this->_removeButton('add');
	}

	/**
	 * Curent event data storage
	 * @deprecated after 1.6.0.0
	 * @var object
	 */
	protected $_eventData = null;
}