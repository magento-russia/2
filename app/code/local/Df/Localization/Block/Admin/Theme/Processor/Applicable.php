<?php
class Df_Localization_Block_Admin_Theme_Processor_Applicable
	extends Df_Localization_Block_Admin_Theme_Processor {
	/**
	 * @override
	 * @return string
	 */
	public function getActionTitle() {return 'запустить';}

	/**
	 * @override
	 * @return string
	 */
	public function getLinkTitle() {return 'выполнить русификацию темы';}

	const _C = __CLASS__;
}