<?php
class Df_Localization_Translation_File extends Df_Core_Model {
	/** @return string[] */
	public function getAbsentEntries() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				array_keys(
					is_null($this->getTranslatedFile())
					? $this->getEntries()
					: array_diff_key($this->getEntries(), $this->getTranslatedFile()->getEntries())
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string[] */
	public function getEntries() {
		if (!isset($this->{__METHOD__})) {
			df_assert(file_exists($this->getPath()));
			/** @var Varien_File_Csv $parser */
			$parser = new Varien_File_Csv();
			$parser->setDelimiter(Mage_Core_Model_Translate::CSV_SEPARATOR);
			$this->{__METHOD__} = $parser->getDataPairs($this->getPath());
		}
		return $this->{__METHOD__};
	}

	/**
	 * Для коллекций
	 * @return string
	 */
	public function getId() {return $this->getName();}

	/** @return string */
	public function getName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = basename($this->getPath());
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	public function getNumAbsentEntries() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = count($this->getAbsentEntries());
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	public function getNumEntries() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = count($this->getEntries());
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	public function getNumUntranslatedEntries() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = count($this->getUntranslatedEntries());
		}
		return $this->{__METHOD__};
	}

	/** @return string[] */
	public function getUntranslatedEntries() {
		if (!isset($this->{__METHOD__})) {
			/** @var string[] $result */
			$result = array();
			foreach ($this->getEntries() as $entryKey => $entryValue) {
				/** @var string $entryKey */
				df_assert_string($entryKey);
				/**
				 * $entryValue будет равно null,
				 * если в языковом файле присутствует ключ,
				 * но отсутствует запятая-разделитель и значение.
				 * @var string|null $entryValue
				 */
				if (is_null($entryValue)) {
					$result[]= $entryKey;
				}
				else {
					df_assert_string($entryValue);
					/** @var string|null $translatedValue */
					$translatedValue =
						is_null($this->getTranslatedFile())
						? null
						: dfa($this->getTranslatedFile()->getEntries(), $entryKey)
					;
					if (!is_null($translatedValue)) {
						df_assert_string($translatedValue);
					}
					if ($translatedValue === $entryValue) {
						$result[]= $entryKey;
					}
				}
			}
			df_result_array($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function isFullyTranslated() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				0 === ($this->getNumAbsentEntries() + $this->getNumUntranslatedEntries())
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getPath() {return $this->cfg(self::P__PATH);}

	/** @return Df_Localization_Translation_File|null */
	private function getTranslatedFile() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__}  = rm_n_set($this->getTranslatedFiles()->getItemById($this->getName()));
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return Df_Localization_Translation_File_Collection */
	private function getTranslatedFiles() {
		return rm_translator()->getRussianFileStorage()->getFiles();
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__PATH, RM_V_STRING_NE);
	}
	/** @used-by Df_Localization_Translation_File_Collection::itemClass() */
	const _C = __CLASS__;
	const P__PATH = 'path';
	/**
	 * @param string $path
	 * @return Df_Localization_Translation_File
	 */
	public static function i($path) {return new self(array(self::P__PATH => $path));}
}