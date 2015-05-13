<?php
class Df_Cms_Model_Source_Hierarchy_Menu_Listmode {
	/** @return array(string => string) */
	public function toOptionArray() {
		return array(
			''  => df_h()->cms()->__('Default')
			,'1' => df_h()->cms()->__('Numbers (1, 2, 3, ...)')
			,'a' => df_h()->cms()->__('Lower Alpha (a, b, c, ...)')
			,'A' => df_h()->cms()->__('Upper Alpha (A, B, C, ...)')
			,'i' => df_h()->cms()->__('Lower Roman (i, ii, iii, ...)')
			,'I' => df_h()->cms()->__('Upper Roman (I, II, III, ...)')
			,'circle' => df_h()->cms()->__('Circle')
			,'disc' => df_h()->cms()->__('Disc')
			,'square' => df_h()->cms()->__('Square')
		);
	}
	/** @return Df_Cms_Model_Source_Hierarchy_Menu_Listmode */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}