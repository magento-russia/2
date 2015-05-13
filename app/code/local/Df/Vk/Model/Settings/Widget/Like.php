<?php
class Df_Vk_Model_Settings_Widget_Like extends Df_Vk_Model_Settings_Widget {
	/**
	 * @override
	 * @return string
	 */
	protected function getWidgetType() {
		return 'like';
	}
	/** @return Df_Vk_Model_Settings_Widget_Like */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}