<?php
class Df_Localization_Model_Onetime_Dictionary extends Df_Localization_Model_Dictionary {
	/** @return Df_Localization_Model_Onetime_Dictionary_Config_Entries */
	public function getConfigEntries() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Localization_Model_Onetime_Dictionary_Config_Entries::i($this->e())
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Localization_Model_Onetime_Dictionary_Db_Tables */
	public function getDbTables() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Localization_Model_Onetime_Dictionary_Db_Tables::i($this->e())
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Localization_Model_Onetime_Dictionary_Filesystem_Operations */
	public function getFilesystemOperations() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Localization_Model_Onetime_Dictionary_Filesystem_Operations::i($this->e())
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Localization_Model_Onetime_Dictionary_Rules */
	public function getRules() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Localization_Model_Onetime_Dictionary_Rules::i($this->e());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getType() {return 'onetime';}

	const _CLASS = __CLASS__;
	/**
	 * @param string $pathLocal
	 * @return Df_Localization_Model_Onetime_Dictionary
	 */
	public static function i($pathLocal) {return self::_i(__CLASS__, $pathLocal);}
}