<?php
class Df_Cms_Model_Source_Hierarchy_Menu_Chapter {
	/** @return array(array(string => string)) */
	public function toOptionArray() {
		return rm_map_to_options(array(
			'' => 'No', 'chapter' => 'Chapter', 'section' => 'Section', 'both' => 'Both'
		), $this);
	}
}