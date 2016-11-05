<?php
class Df_Cms_Model_Source_Hierarchy_Visibility {
	/**
	 * Retrieve options array
	 * @return array(string => string)
	 */
	public function toOptionArray(){
		return array(
			Df_Cms_Helper_Hierarchy::METADATA_VISIBILITY_PARENT => df_h()->cms()->__('Use Parent')
			,Df_Cms_Helper_Hierarchy::METADATA_VISIBILITY_YES => df_h()->cms()->__('Yes')
			,Df_Cms_Helper_Hierarchy::METADATA_VISIBILITY_NO => df_h()->cms()->__('No')
		);
	}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}