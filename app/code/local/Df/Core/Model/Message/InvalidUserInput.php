<?php
class Df_Core_Model_Message_InvalidUserInput extends Mage_Core_Model_Message_Error {
	/**
	 * @param Zend_Form_Element $element
	 * @return Df_Core_Model_Message_InvalidUserInput
	 */
	public function setElement(Zend_Form_Element $element) {
		$this->_element = $element;
		return $this;
	}

	/** @return Zend_Form_Element */
	public function getElement() {return $this->_element;}
	/** @var Zend_Form_Element */
	private $_element;


}