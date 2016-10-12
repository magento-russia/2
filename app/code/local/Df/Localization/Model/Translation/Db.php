<?php
class Df_Localization_Model_Translation_Db extends Df_Core_Model {
	/** @return string[] */
	public function getItems() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->getCoreTranslateResource()->getTranslationArray(
					null, df_mage()->core()->translateSingleton()->getLocale()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Mage_Core_Model_Resource_Translate|Mage_Core_Model_Mysql4_Translate */
	private function getCoreTranslateResource() {
		return df_mage()->core()->translateSingleton()->getResource();
	}
	/** @return Df_Localization_Model_Translation_Db */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}