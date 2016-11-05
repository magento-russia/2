<?php
class Df_Dataflow_Model_Settings_Common extends Df_Core_Model_Settings {
	/** @return boolean */
	public function getShowInteractiveMessages() {return $this->getYesNo('show_interactive_messages');}
	/** @return boolean */
	public function getSupportHtmlTagsInExcel() {return $this->getYesNo('support_html_tags_in_excel');}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_dataflow/common/';}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}