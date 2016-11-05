<?php
class Df_Vk_Model_Settings_Widget_Comments extends Df_Vk_Model_Settings_Widget {
	/**
	 * @override
	 * @return string
	 */
	protected function getWidgetType() {
		return 'comments';
	}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}