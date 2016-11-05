<?php
class Df_Localization_Exporter extends Df_Core_Model {
	/** @return Df_Localization_Exporter */
	public function process() {
		$this->writeTranslationToFiles($this->getTranslationByModules());
		return $this;
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @used-by getTranslationByModules()
	 * http://php.net/manual/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * http://3v4l.org/OipEQ
	 * @param Df_Localization_Translation_Db_Source_Key $sourceKey
	 * @param string $stringInTargetLanguage
	 * @return Df_Localization_Exporter
	 */
	private function addTranslationStringToModule(
		Df_Localization_Translation_Db_Source_Key $sourceKey
		,$stringInTargetLanguage
	) {
		if (!isset($this->{__METHOD__}[$sourceKey->getModule()])) {
			$this->{__METHOD__}[$sourceKey->getModule()] = [];
		}
		$this->{__METHOD__}[$sourceKey->getModule()][$sourceKey->getString()] =
			$stringInTargetLanguage
		;
		return $this;
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @used-by getTranslationByModules()
	 * http://php.net/manual/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * http://3v4l.org/OipEQ
	 * @param string $sourceKey
	 * @return Df_Localization_Translation_Db_Source_Key
	 */
	private function convertSourceKeyToObject($sourceKey) {
		return Df_Localization_Translation_Db_Source_Key::i($sourceKey);
	}

	/**
	 * @param string $module
	 * @param string[] $translation
	 * @return string[]
	 */
	private function correctTranslationWithCsvFileData($module, array $translation) {
		df_param_string($module, 0);
		/** @var string[] $result */
		$result =
			array_merge(
				$translation
				,$this->getTranslationFromCsv($module)
			)
		;
		return $result;
	}

	/** @return array(string => string) */
	private function getTranslationByModules() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				array_map(
					/** @uses addTranslationStringToModule() */
					array($this, 'addTranslationStringToModule')
					,array_map(
					/** @uses convertSourceKeyToObject() */
						array($this, 'convertSourceKeyToObject')
						,array_keys($this->getDb()->getItems())
					)
					,array_values($this->getDb()->getItems())
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Localization_Translation_Db */
	private function getDb() {return Df_Localization_Translation_Db::s();}

	/**
	 * @param string $module
	 * @return string[]
	 */
	private function getTranslationFromCsv($module) {
		df_param_string($module, 0);
		/** @var string[] $result */
		$result = [];
		/** @var string $filePath */
		$filePath =
			df_cc_path(
				Mage::getBaseDir('locale')
				,df_mage()->core()->translateSingleton()->getLocale()
				,$module . '.csv'
			)
		;
		if (file_exists($filePath)) {
			/** @var Varien_File_Csv $parser */
			$parser = new Varien_File_Csv();
			$parser->setDelimiter(Mage_Core_Model_Translate::CSV_SEPARATOR);
			/** @var string[] $result */
			$result =
				$parser->getDataPairs(
					$filePath
				)
			;
			df_result_array($result);
		}
		return $result;
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @used-by writeTranslationToFiles()
	 * http://php.net/manual/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * http://3v4l.org/OipEQ
	 * @param string $moduleName
	 * @param array $translation
	 * @return Df_Localization_Exporter
	 * @throws Exception
	 */
	private function writeTranslationToFile($moduleName, array $translation) {
		df_param_string($moduleName, 0);
		/** @var string[] $translation */
		$translation = $this->correctTranslationWithCsvFileData($moduleName, $translation);
		df_assert_array($translation);
		/** @var string $targetDir */
		$targetDir =
			df_cc_path(
				Mage::getBaseDir('var')
				,'translations'
				,df_mage()->core()->translateSingleton()->getLocale()
			)
		;
		if (!file_exists($targetDir)) {
			if (!mkdir($targetDir, null, true)) {
				df_error('Cannot create $targetDir');
			}
		}
		/** @var string $targetFile */
		$targetFile = $targetDir . DS . $moduleName . '.csv';
		df_assert_string($targetFile);
		/** @var Varien_File_Csv $parser */
		$parser = new Varien_File_Csv();
		/** string[] @var $csvdata */
		$csvdata = [];
		foreach ($translation as $key => $value)
			/** @var string $value */
			/** @var string $value */
			$csvdata[]= array($key, $value)
		;
		$parser->saveData($targetFile, $csvdata);
		return $this;
	}

	/**
	 * @param array $translation
	 * @return Df_Localization_Exporter
	 */
	private function writeTranslationToFiles(array $translation) {
		array_map(
			/** @uses writeTranslationToFile() */
			array($this, 'writeTranslationToFile')
			,array_keys($translation)
			,array_values($translation)
		)
		;
		return $this;
	}
	/** @return Df_Localization_Exporter */
	public static function i() {return new self;}
}