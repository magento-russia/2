<?php
class Df_Logging_Block_Details extends Mage_Adminhtml_Block_Widget_Container {
	/** @return Df_Logging_Model_Event|null */
	public function getCurrentEvent() {
		if (null === $this->_currentEvent) {
			$this->_currentEvent = Mage::registry('current_event');
		}
		return $this->_currentEvent;
	}
	/** @var Df_Logging_Model_Event */
	private $_currentEvent = null;

	/**
	 * Replace /n => <br /> in event error_message
	 * @return string|bool
	 */
	public function getEventError() {
		if ($this->getCurrentEvent()) {
			return df_t()->nl2br($this->getCurrentEvent()->getErrorMessage());
		}
		return false;
	}

	/**
	 * Convert ip to string
	 * @return string|bool
	 */
	public function getEventIp() {
		if ($this->getCurrentEvent()) {
			return long2ip($this->getCurrentEvent()->getIp());
		}
		return false;
	}

	/** @return Mage_Admin_Model_User|null */
	public function getEventUser() {
		if (null === $this->_eventUser){
			$this->_eventUser = df_model('admin/user')->load($this->getUserId());
		}
		return $this->_eventUser;
	}
	/** @var Mage_Admin_Model_User */
	private $_eventUser = null;

	/**
	 * Convert x_forwarded_ip to string
	 * @return string|bool
	 */
	public function getEventXForwardedIp() {
		if ($this->getCurrentEvent()) {
			$xForwarderFor = long2ip($this->getCurrentEvent()->getXForwardedIp());
			if ($xForwarderFor && $xForwarderFor != '0.0.0.0') {
				return $xForwarderFor;
			}
		}
		return false;
	}

	/**
	 * @override
	 * @return string
	 */
	public function getHeaderText() {
		if ($this->getCurrentEvent()) {
			return Df_Logging_Helper_Data::s()->__('Log Entry #%d', $this->getCurrentEvent()->getId());
		}
		return Df_Logging_Helper_Data::s()->__('Log Entry Details');
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_addButton('back', array(
			'label' => Df_Logging_Helper_Data::s()->__('Back')
			,'onclick' => rm_admin_button_location('*/*/')
			,'class' => 'back'
		));
	}
}