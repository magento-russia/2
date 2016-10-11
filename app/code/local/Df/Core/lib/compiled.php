<?php
if (false) {
	/**
	 * @param Varien_Simplexml_Element $target
	 * @param Varien_Simplexml_Element $source
	 * @param bool $overwrite [optional]
	 * @return void
	 */
	function rm_xml_extend(
		Varien_Simplexml_Element $target, Varien_Simplexml_Element $source, $overwrite = false
	) {
		df_should_not_be_here(__FUNCTION__);
		$target->extend($source, $overwrite);
	}

	/**
	 * @param Varien_Simplexml_Element $target
	 * @param Varien_Simplexml_Element $source
	 * @param bool $overwrite [optional]
	 * @return void
	 */
	function rm_xml_extend_child(
		Varien_Simplexml_Element $target, Varien_Simplexml_Element $source, $overwrite = false
	) {
		df_should_not_be_here(__FUNCTION__);
		$target->extend($source, $overwrite);
	}

	/**
	 * @param Varien_Simplexml_Element $target
	 * @return void
	 */
	function rm_xml_remove_comments(Varien_Simplexml_Element $target) {
		df_should_not_be_here(__FUNCTION__);
	}

	/**
	 * @param Varien_Simplexml_Element $element
	 * @param int $level
	 * @return void
	 */
	function rm_xml_serialize_nice(Varien_Simplexml_Element $element, $level = 0) {
		df_should_not_be_here(__FUNCTION__);
	}
}
