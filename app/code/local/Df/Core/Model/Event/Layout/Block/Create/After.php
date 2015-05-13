<?php
/**
 * Cообщение:		«core_layout_block_create_after»
 * Источник:		Mage_Core_Model_Layout::createBlock()
 * [code]
		Mage::dispatchEvent('core_layout_block_create_after', array('block'=>$block));
 * [/code]
 *
 * Назначение:		Позволяет выполнить дополнительную настройку блока
 * 					после его создания
 */
class Df_Core_Model_Event_Layout_Block_Create_After extends Df_Core_Model_Event {
	/** @return Mage_Core_Block_Abstract */
	public function getBlock() {return $this->getEventParam(self::EVENT_PARAM__BLOCK);}
	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedEventPrefix() {return self::EXPECTED_EVENT_PREFIX;}

	const _CLASS = __CLASS__;
	const EVENT_PARAM__BLOCK = 'block';
	const EXPECTED_EVENT_PREFIX = 'core_layout_block_create_after';
}