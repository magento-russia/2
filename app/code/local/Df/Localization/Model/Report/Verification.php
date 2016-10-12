<?php
class Df_Localization_Model_Report_Verification extends Df_Core_Model {
	/** @return Df_Localization_Model_Translation_File_Collection */
	public function getFiles() {
		return rm_translator()->getDefaultFileStorage()->getFiles();
	}

	const _CLASS = __CLASS__;
	/** @return Df_Localization_Model_Report_Verification */
	public static function i() {return new self;}
}