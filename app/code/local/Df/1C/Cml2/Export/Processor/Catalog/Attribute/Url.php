<?php
class Df_1C_Cml2_Export_Processor_Catalog_Attribute_Url
	extends Df_1C_Cml2_Export_Processor_Catalog_Attribute {
	/**
	 * @override
	 * @param Df_Catalog_Model_Product $product
	 * @return string|string[]|null
	 */
	protected function getЗначение(Df_Catalog_Model_Product $product) {
		return $this->getDocument()->getProcessorForProduct($product)->getUrl();
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getИд() {return 'rm__url';}

	/**
	 * @override
	 * @return string
	 */
	protected function getНаименование() {return 'Веб-адрес';}

	/**
	 * @override
	 * @return string
	 */
	protected function getОписание() {return 'Веб-адрес товара на витрине';}

	/**
	 * @override
	 * @return string
	 */
	protected function getТипЗначений() {return 'Строка';}

	/** @used-by Df_1C_Cml2_Export_Document_Catalog::getVirtualAttributeProcessorClasses() */
	const _C = __CLASS__;

	/**
	 * @static
	 * @param Df_1C_Cml2_Export_Document_Catalog $document
	 * @return Df_1C_Cml2_Export_Processor_Catalog_Attribute_Url
	 */
	public static function i(Df_1C_Cml2_Export_Document_Catalog $document) {
		return self::ic(__CLASS__, $document);
	}
}