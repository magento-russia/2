<?php
class Df_Page_Model_Admin_Notifier_Merge_Css extends Df_Page_Model_Admin_Notifier_Merge {
	/**
	 * @override
	 * @return string
	 */
	protected function getConfigPath() {return 'dev/css/merge_css_files';}
	/**
	 * @override
	 * @return string
	 */
	protected function getFileType() {return 'CSS';}
}