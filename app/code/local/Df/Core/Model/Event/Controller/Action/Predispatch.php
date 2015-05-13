<?php
/**
 * Cообщение:		«controller_action_predispatch»
 * Источник:		Mage_Core_Controller_Varien_Action::preDispatch()
 * [code]
		Mage::dispatchEvent('controller_action_predispatch', array('controller_action'=>$this));
 * [/code]
 *
 * Назначение:		Позволяет выполнить дополнительную обработку запроса
 * 					перед обработкой запроса контроллером
 */
class Df_Core_Model_Event_Controller_Action_Predispatch extends Df_Core_Model_Event {
	/** @return Mage_Core_Controller_Varien_Action */
	public function getController() {return $this->getEventParam(self::EVENT_PARAM__CONTROLLER_ACTION);}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedEventPrefix() {return self::EXPECTED_EVENT_PREFIX;}

	const _CLASS = __CLASS__;
	const EVENT_PARAM__CONTROLLER_ACTION = 'controller_action';
	const EXPECTED_EVENT_PREFIX = 'controller_action_predispatch';
}