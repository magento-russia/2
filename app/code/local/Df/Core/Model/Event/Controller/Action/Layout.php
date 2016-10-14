<?php
abstract class Df_Core_Model_Event_Controller_Action_Layout	extends Df_Core_Model_Event {
	/** @return Mage_Core_Controller_Varien_Action */
	public function getAction() {return $this->getEventParam('action');}
	/** @return Mage_Core_Model_Layout */
	public function getLayout() {return $this->getEventParam('layout');}
	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedEventPrefix() {return 'controller_action_layout';}
}