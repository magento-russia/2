<?php
namespace Df\C1\Cml2\Export\Processor\Catalog\Attribute;
class Df_C1_Cml2_Export_Processor_Catalog_Attribute_Url
	extends Df_C1_Cml2_Export_Processor_Catalog_Attribute {
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
	protected function getИд() {return 'df__url';}

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

	/** @used-by Df_C1_Cml2_Export_Document_Catalog::getVirtualAttributeProcessorClasses() */


	/**
	 * @static
	 * @param Df_C1_Cml2_Export_Document_Catalog $document
	 * @return Df_C1_Cml2_Export_Processor_Catalog_Attribute_Url
	 */
	public static function i(Df_C1_Cml2_Export_Document_Catalog $document) {
		return self::ic(__CLASS__, $document);
	}
}