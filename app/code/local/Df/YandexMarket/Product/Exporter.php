<?php
// 2016-10-09
class Df_YandexMarket_Product_Exporter extends Df_Catalog_Product_Exporter {
	/**
	 * 2016-10-09
	 * @override
	 * @see Df_Catalog_Product_Exporter::additionalAttributes()
	 * @used-by Df_Catalog_Product_Exporter::getAttributesToSelect()
	 * @return string|string[]
	 */
	protected function additionalAttributes() {return array(
		Df_YandexMarket_Const::ATTRIBUTE__CATEGORY
		,Df_YandexMarket_Const::ATTRIBUTE__SALES_NOTES
	);}

	/**
	 * 2016-10-09
	 * @override
	 * @see Df_Catalog_Product_Exporter::limit()
	 * @used-by Df_Catalog_Product_Exporter::getResult()
	 * @return int
	 */
	protected function limit() {return df_cfg()->yandexMarket()->diagnostics()->limit();}

	/**
	 * 2016-10-09
	 * @override
	 * @see Df_Catalog_Product_Exporter::needRemoveNotSalable()
	 * @used-by Df_Catalog_Product_Exporter::getResult()
	 * @return bool
	 */
	protected function needRemoveNotSalable() {return true;}

	/**
	 * 2016-10-09
	 * @override
	 * @see Df_Catalog_Product_Exporter::needRemoveOutOfStock()
	 * @used-by Df_Catalog_Product_Exporter::getResult()
	 * @return bool
	 */
	protected function needRemoveOutOfStock() {return true;}

	/**
	 * 2016-10-09
	 * @override
	 * @see Df_Catalog_Product_Exporter::rule()
	 * @used-by Df_Catalog_Product_Exporter::applyRule()
	 * @return Mage_CatalogRule_Model_Rule|null
	 */
	protected function rule() {return df_cfg()->yandexMarket()->products()->getRule();}

	/**
	 * 2016-10-09
	 * @override
	 * @see Df_Catalog_Product_Exporter::noMatchingProductIds()
	 * @used-by Df_Catalog_Product_Exporter::applyRule()
	 * @return void
	 */
	protected function noMatchingProductIds() {
		df_h()->yandexMarket()->error_noOffers(
			'Заданным администратором в графе'
			.' «Система» → «Настройки» → «Российская сборка» → «Яндекс.Маркет»'
			. ' → «Товары» → «Условия» условиям публикации товаров'
			. ' не соответствует ни один из товаров интернет-магазина.'
		);
	}
}

