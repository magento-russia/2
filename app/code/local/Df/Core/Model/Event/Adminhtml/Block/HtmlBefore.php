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
	public function getBlock() {return $this->getEventParam('block');}
	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedEventPrefix() {return 'adminhtml_block_html_before';}

	/**
	 * @used-by Df_Reports_Observer::adminhtml_block_html_before()
	 * @used-by Df_Reports_Model_Handler_SetDefaultFilterValues::getEventClass()
	 * @used-by Df_Reports_Model_Handler_GroupResultsByWeek_AddOptionToFilter::getEventClass()
	 * @used-by Df_Reports_Model_Handler_GroupResultsByWeek_SetColumnRenderer::getEventClass()
	 */
	const _C = __CLASS__;
}