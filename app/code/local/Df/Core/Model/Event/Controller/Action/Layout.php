<?php
abstract class Df_Core_Model_Event_Controller_Action_Layout	extends Df_Core_Model_Event {
	/** @return Mage_Core_Controller_Varien_Action */
	public function getAction() {return $this->getEventParam(self::EVENT_PARAM__ACTION);}
	/** @return Mage_Core_Model_Layout */
	public function getLayout() {return $this->getEventParam(self::EVENT_PARAM__LAYOUT);}
	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedEventPrefix() {return self::EXPECTED_EVENT_PREFIX;}

	const _CLASS = __CLASS__;
	const EVENT_PARAM__ACTION = 'action';
	const EVENT_PARAM__LAYOUT = 'layout';
	const EXPECTED_EVENT_PREFIX = 'controller_action_layout';
}