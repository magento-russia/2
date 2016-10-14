<?php
class Df_Localization_Onetime_Dictionary extends Df_Localization_Dictionary {
	/** @return Df_Localization_Onetime_Dictionary_Config_Entries */
	public function getConfigEntries() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Localization_Onetime_Dictionary_Config_Entries::i($this->e());
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Localization_Onetime_Dictionary_Filesystem_Operations */
	public function getFilesystemOperations() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Localization_Onetime_Dictionary_Filesystem_Operations::i($this->e());
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Localization_Onetime_Dictionary_Rules */
	public function getRules() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Localization_Onetime_Dictionary_Rules::i($this->e());
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-08-23
	 * @used-by Df_Localization_Onetime_Processor::applyDictionary()
	 * @return Df_Localization_Onetime_Dictionary_Db_Tables
	 */
	public function tables() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Localization_Onetime_Dictionary_Db_Tables::i($this->e());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @see Df_Localization_Dictionary::type()
	 * @used-by Df_Localization_Dictionary::pathFull()
	 * @return string
	 */
	protected function type() {return 'onetime';}

	/**
	 * @param string $pathLocal
	 * @return Df_Localization_Onetime_Dictionary
	 */
	public static function s($pathLocal) {return self::sc(__CLASS__, $pathLocal);}
}