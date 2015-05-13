<?php
/**
 * Cообщение:		«controller_action_layout_load_before»
 * Источник:		Mage_Core_Controller_Varien_Action::loadLayoutUpdates()
 * [code]
		// dispatch event for adding handles to layout update
		Mage::dispatchEvent(
			'controller_action_layout_load_before',array('action'=>$this, 'layout'=>$this->getLayout())
		);
 * [/code]
 */
class Df_Core_Model_Event_Controller_Action_Layout_LoadBefore
	extends Df_Core_Model_Event_Controller_Action_Layout {
	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedEventPrefix() {
		return self::EXPECTED_EVENT_PREFIX;
	}

	const EXPECTED_EVENT_PREFIX = 'controller_action_layout_load_before';
	const _CLASS = __CLASS__;
}