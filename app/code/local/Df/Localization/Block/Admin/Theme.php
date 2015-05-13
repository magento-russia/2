<?php
class Df_Localization_Block_Admin_Theme extends Df_Core_Block_Admin {
	/**
	 * @param Df_Localization_Model_Onetime_Processor $processor
	 * @return string
	 */
	public function renderProcessor(Df_Localization_Model_Onetime_Processor $processor) {
		return df_block_render(
			Df_Localization_Block_Admin_Theme_Processor::getBlockClass($processor)
			, ''
			, array(Df_Localization_Block_Admin_Theme_Processor::P__PROCESSOR => $processor)
		);
	}
}