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
		return
			df_clean(
				array_map(
					array($this, 'explodeCategoryPath')
					, $this->getCategoriesPathsAsStrings()
				)
			)
		;
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @link http://php.net/manual/en/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * @link http://3v4l.org/OipEQ
	 *
	 * @param string $pathAsString
	 * @return array
	 */
	private function explodeCategoryPath($pathAsString) {
		$result =
			array_map(
				"df_trim"
				,explode(
					"/"
					,str_replace(
						$this->getSlashEscaped()
						,$this->getUniqueMarker()
						,$pathAsString
					)
				)
			)
		;
		foreach ($result as &$string) {
			$string =
				str_replace(
					$this->getUniqueMarker()
					,"/"
					,$string
				)
			;
		}
		return $result;
	}

	/** @return array */
	private function getCategoriesPathsAsStrings() {
		$result =
			array_map(
				"df_trim"
				,explode(
					","
					,str_replace(
						$this->getCommaEscaped()
						,$this->getUniqueMarker()
						,$this->getImportedValue()
					)
				)
			)
		;
		foreach ($result as &$string) {
			$string =
				str_replace(
					$this->getUniqueMarker()
					,","
					,$string
				)
			;
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
