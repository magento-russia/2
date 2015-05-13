<?php
/**
 * Cообщение:		«controller_action_layout_generate_blocks_after»
 * Источник:		Mage_Core_Controller_Varien_Action::generateLayoutBlocks()
 * [code]
		if (!$this->getFlag('', self::FLAG_NO_DISPATCH_BLOCK_EVENT)) {
			Mage::dispatchEvent(
				'controller_action_layout_generate_blocks_after',array('action'=>$this, 'layout'=>$this->getLayout())
			);
		}
 * [/code]
 */
class Df_Core_Model_Event_Controller_Action_Layout_GenerateBlocksAfter
	extends Df_Core_Model_Event_Controller_Action_Layout {
	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedEventPrefix() {return 'controller_action_layout_generate_blocks_after';}
	const _CLASS = __CLASS__;
}