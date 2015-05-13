<?php
class Df_Page_Model_Admin_Notifier_Merge_Js extends Df_Page_Model_Admin_Notifier_Merge {
	/**
	 * @override
	 * @return string
	 */
	protected function getConfigPath() {return 'dev/js/merge_files';}
	/**
	 * @override
	 * @return string
	 */
	protected function getFileType() {return 'JavaScript';}
}