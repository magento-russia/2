<?php
/**
 * Экспорт товаров из интернет-магазина в 1С:Управление торговлей
 * http://1c.1c-bitrix.ru/blog/blog1c/catalog_import.php
 */
class Df_C1_Cml2_Action_Catalog_Export_Process extends Df_C1_Cml2_Action_GenericExport {
	/**
	 * @override
	 * @see Df_C1_Cml2_Action_GenericExport::createDocument()
	 * @used-by Df_C1_Cml2_Action_GenericExport::getDocument()
	 * @return Df_C1_Cml2_Export_Document_Catalog
	 */
	protected function createDocument() {
		return Df_C1_Cml2_Export_Document_Catalog::i(
			Df_Catalog_Model_Product_Exporter::i(array(
				Df_Catalog_Model_Product_Exporter::P__RULE => df_c1_cfg()->catalogExport()->getRule()
				// нам нужны все свойства, потому что мы их экспортируем
				,Df_Catalog_Model_Product_Exporter::P__NEED_LOAD_ALL_ATTRIBUTES => true
			))->getResult()
		);
	}

	/**
	 * @override
	 * @see Df_Core_Model_Action::responseLogActionName()
	 * @used-by Df_Core_Model_Action::responseLogName()
	 * @return string
	 */
	protected function responseLogActionName() {return 'catalog.export';}
}