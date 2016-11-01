<?php
class Df_C1_Cml2_Export_DocumentMixin_Catalog extends Df_C1_Cml2_Export_DocumentMixin {
	/**
	 * @override
	 * @return array(string => string)
	 */
	public function getAttributes() {
		return array(
			'xmlns' => 'urn:1C.ru:commerceml_2'
			/**
			 * Префикс надо дублировать, иначе он пропадёт.
			 * http://stackoverflow.com/a/6928183
			 */
			,'xmlns:xmlns:xs' => 'http://www.w3.org/2001/XMLSchema'
			,'xmlns:xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance'
			,'ВерсияСхемы' => '2.08'
			,'ДатаФормирования' => $this->formatDate(Zend_Date::now())
		);
	}

	/**
	 * Обратите внимание, что хотя при обмене заказами
	 * интернет-магазин должен передавать в 1С документы обязательно с символом BOM в начале,
	 * при экспорте каталога, наоборот, символ BOM обязательно должен отсутствовать.
	 * @override
	 * @param bool $reformat [optional]
	 * @return string
	 */
	public function getXml($reformat = false) {return $this->parent(__FUNCTION__, $reformat);}

	/**
	 * @used-by Df_C1_Cml2_Export_Document_Catalog::createMixin()
	 * @param \Df\Xml\Generator\Document $parent
	 * @return Df_C1_Cml2_Export_DocumentMixin_Catalog
	 */
	public static function i(\Df\Xml\Generator\Document $parent) {
		return self::ic(__CLASS__, $parent);
	}
}