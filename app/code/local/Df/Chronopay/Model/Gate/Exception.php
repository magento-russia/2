<?php
class Df_Chronopay_Model_Gate_Exception extends Mage_Core_Exception {
	const _CLASS = __CLASS__;
	/** @var array */
	private $_params;

	/**
	 * @override
	 * @param mixed[] $params
	 * @return Df_Chronopay_Model_Gate_Exception
	 */
	public function __construct(array $params = array()) {
		$this->_params = $params;
		parent::__construct($this->getMessageForCustomer());
	}

	/** @return string */
	public function getMessageForCustomer() {
		return $this->getSpecificMessage("messageForCustomer");
	}

	/** @return string */
	public function getMessageForLog() {
		return $this->getSpecificMessage("messageForLog");
	}

	/** @return string */
	public function getMessageForStatus() {
 		return $this->getSpecificMessage("messageForStatus");
	}

	/** @return string */
	private function getSpecificMessage($messageType) {
 		return df_a($this->_params, $messageType, $this->getDefaultMessage());
	}

	/** @return string */
	private function getDefaultMessage() {
 		return df_a($this->_params, "message");
	}

}