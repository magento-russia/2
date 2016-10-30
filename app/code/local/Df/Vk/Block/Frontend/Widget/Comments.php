<?php
class Df_Vk_Block_Frontend_Widget_Comments extends Df_Vk_Block_Frontend_Widget {
	/**
	 * @override
	 * @return string
	 */
	public function getJavaScriptNameSpace() {return 'comments';}

	/**
	 * @override
	 * @return string
	 */
	protected function getJavaScriptObjectName() {return 'VK.Widgets.Comments';}

	/**
	 * @override
	 * @return Df_Vk_Model_Settings_Widget
	 */
	protected function getSettings() {return df_cfgr()->vk()->comments();}

	
}