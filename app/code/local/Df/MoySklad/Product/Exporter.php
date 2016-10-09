<?php
// 2016-10-09
class Df_MoySklad_Product_Exporter extends Df_Catalog_Product_Exporter {
	/**
	 * 2016-10-09
	 * @override
	 * @see Df_Catalog_Product_Exporter::additionalAttributes()
	 * @used-by Df_Catalog_Product_Exporter::getAttributesToSelect()
	 * @return string|string[]
	 */
	protected function additionalAttributes() {return array();}

	/**
	 * 2016-10-09
	 * @override
	 * @see Df_Catalog_Product_Exporter::limit()
	 * @used-by Df_Catalog_Product_Exporter::getResult()
	 * @return int
	 */
	protected function limit() {return 0;}

	/**
	 * 2016-10-09
	 * @override
	 * @see Df_Catalog_Product_Exporter::needRemoveNotSalable()
	 * @used-by Df_Catalog_Product_Exporter::getResult()
	 * @return bool
	 */
	protected function needRemoveNotSalable() {return false;}

	/**
	 * 2016-10-09
	 * @override
	 * @see Df_Catalog_Product_Exporter::needRemoveOutOfStock()
	 * @used-by Df_Catalog_Product_Exporter::getResult()
	 * @return bool
	 */
	protected function needRemoveOutOfStock() {return false;}

	/**
	 * 2016-10-09
	 * @override
	 * @see Df_Catalog_Product_Exporter::rule()
	 * @used-by Df_Catalog_Product_Exporter::applyRule()
	 * @return Mage_CatalogRule_Model_Rule|null
	 */
	protected function rule() {return Df_MoySklad_Settings_Export_Products::s()->rule();}

	/**
	 * 2016-10-09
	 * @override
	 * @see Df_Catalog_Product_Exporter::noMatchingProductIds()
	 * @used-by Df_Catalog_Product_Exporter::applyRule()
	 * @return void
	 */
	protected function noMatchingProductIds() {
		rm_log(
			'Заданным администратором в графе'
			.' «Система» → «Настройки» → «Российская сборка» → «МойСклад»'
			. ' → «Экспорт товаров» → «Условия» условиям публикации товаров'
			. ' не соответствует ни один из товаров интернет-магазина.'
		);
	}
}

