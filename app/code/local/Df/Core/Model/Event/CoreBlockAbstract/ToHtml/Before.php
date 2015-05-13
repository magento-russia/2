<?php
/**
 * Cообщение:		«core_block_abstract_to_html_before»
 * Источник:		Mage_Core_Block_Abstract::toHtml()
 * [code]
		Mage::dispatchEvent('core_block_abstract_to_html_before', array('block' => $this));
 * [/code]
 */
class Df_Core_Model_Event_CoreBlockAbstract_ToHtml_Before extends
	Df_Core_Model_Event_CoreBlockAbstract_ToHtml_Abstract {
	/** @return string */
	protected function getExpectedEventSuffix() {
		return self::EXPECTED_EVENT_SUFFIX;
	}

	const _CLASS = __CLASS__;
	const EXPECTED_EVENT_SUFFIX = '_html_before';
}