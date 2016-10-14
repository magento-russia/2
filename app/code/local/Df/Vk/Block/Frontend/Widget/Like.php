<?php
class Df_Vk_Block_Frontend_Widget_Like extends Df_Vk_Block_Frontend_Widget {
	/**
	 * @override
	 * @return string
	 */
	public function getJavaScriptNameSpace() {return 'like';}

	/**
	 * @override
	 * @return string
	 */
	protected function getJavaScriptObjectName() {return 'VK.Widgets.Like';}

	/**
	 * @override
	 * @return Df_Vk_Model_Settings_Widget
	 */
	protected function getSettings() {return df_cfg()->vk()->like();}

	const _C = __CLASS__;
}