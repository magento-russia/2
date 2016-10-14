<?php
class Df_Dataflow_Model_Importer_Product_Categories_Format_Simple
	extends Df_Dataflow_Model_Importer_Product_Categories_Parser {
	/**
	 * Can process the following formats:
	 * Category1, Category2
	 * Category1/Category2, Category3
	 * Escapes:
	 * \/ => /
	 * \, => , *
	 * @return array
	 */
	public function getPaths() {
		return df_clean(array_map(
			/** @uses explodeCategoryPath() */
			array($this, 'explodeCategoryPath'), $this->getCategoriesPathsAsStrings()
		));
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @used-by getPaths()
	 * http://php.net/manual/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * http://3v4l.org/OipEQ
	 * @param string $pathAsString
	 * @return string[]
	 */
	private function explodeCategoryPath($pathAsString) {
		/** @var string[] $result */
		$result = df_trim(df_explode_xpath(str_replace(
			$this->getSlashEscaped(), $this->getUniqueMarker(), $pathAsString
		)));
		foreach ($result as &$string) {
			/** @var string $string */
			$string = str_replace($this->getUniqueMarker(), '/', $string);
		}
		return $result;
	}

	/** @return string[] */
	private function getCategoriesPathsAsStrings() {
		/** @var string[] $result */
		$result = df_csv_parse(str_replace(
			$this->getCommaEscaped(), $this->getUniqueMarker(), $this->getImportedValue()
		));
		foreach ($result as &$string) {
			/** @var string $string */
			$string = str_replace($this->getUniqueMarker(), ',', $string);
		}
		return $result;
	}

	/** @return string */
	private function getSlashEscaped() {
		return '\/';
	}

	/** @return string */
	private function getCommaEscaped() {
		return '\,';
	}

	/** @return string */
	private function getUniqueMarker() {
		return '###';
	}
	/** @return Df_Dataflow_Model_Importer_Product_Categories_Format_Simple */
	public static function i() {return new self;}
}
