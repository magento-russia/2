<?php
class Df_Localization_Report_Verification extends Df_Core_Model {
	/** @return Df_Localization_Translation_File_Collection */
	public function getFiles() {
		return df_translator()->getDefaultFileStorage()->getFiles();
	}

	const _C = __CLASS__;
	/** @return Df_Localization_Report_Verification */
	public static function i() {return new self;}
}