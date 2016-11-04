<?php
namespace Df\C1\Cml2\Export\Processor\Catalog\Attribute;
class Url extends \Df\C1\Cml2\Export\Processor\Catalog\Attribute {
	/**
	 * @override
	 * @param \Df_Catalog_Model_Product $product
	 * @return string|string[]|null
	 */
	protected function getЗначение(\Df_Catalog_Model_Product $product) {
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

	/** @used-by \Df\C1\Cml2\Export\Document\Catalog::getVirtualAttributeProcessorClasses() */


	/**
	 * @static
	 * @param \Df\C1\Cml2\Export\Document\Catalog $document
	 * @return \Df\C1\Cml2\Export\Processor\Catalog\Attribute\Url
	 */
	public static function i(\Df\C1\Cml2\Export\Document\Catalog $document) {
		return self::ic(__CLASS__, $document);
	}
}