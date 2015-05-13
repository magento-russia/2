<?php
/**
 * Cообщение:		«adminhtml_block_html_before»
 * Источник:		Mage_Adminhtml_Block_Template::toHtml()
 * [code]
		Mage::dispatchEvent('adminhtml_block_html_before', array('block' => $this));
 * [/code]
 */
class Df_Core_Model_Event_Adminhtml_Block_HtmlBefore extends Df_Core_Model_Event {
	/** @return Mage_Core_Block_Abstract */
	public function getBlock() {return $this->getEventParam(self::EVENT_PARAM__BLOCK);}
	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedEventPrefix() {return self::EXPECTED_EVENT_PREFIX;}

	const _CLASS = __CLASS__;
	const EVENT_PARAM__BLOCK = 'block';
	const EXPECTED_EVENT_PREFIX = 'adminhtml_block_html_before';
}