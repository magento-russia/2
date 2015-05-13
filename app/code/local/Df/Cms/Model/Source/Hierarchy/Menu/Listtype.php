<?php
class Df_Cms_Model_Source_Hierarchy_Menu_Listtype {
	/** @return array(string => string) */
	public function toOptionArray() {
		return array(
			'0' => df_h()->cms()->__('Unordered')
			,'1' => df_h()->cms()->__('Ordered')
		);
	}
	/** @return Df_Cms_Model_Source_Hierarchy_Menu_Listtype */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}