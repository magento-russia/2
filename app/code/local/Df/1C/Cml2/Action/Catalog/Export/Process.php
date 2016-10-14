<?php
/**
 * Экспорт товаров из интернет-магазина в 1С:Управление торговлей
 * http://1c.1c-bitrix.ru/blog/blog1c/catalog_import.php
 */
class Df_1C_Cml2_Action_Catalog_Export_Process extends Df_1C_Cml2_Action_GenericExport {
	/**
	 * @override
	 * @see Df_1C_Cml2_Action_GenericExport::createDocument()
	 * @used-by Df_1C_Cml2_Action_GenericExport::getDocument()
	 * @return Df_1C_Cml2_Export_Document_Catalog
	 */
	protected function createDocument() {
		return Df_1C_Cml2_Export_Document_Catalog::i(
			Df_Catalog_Model_Product_Exporter::i(array(
				Df_Catalog_Model_Product_Exporter::P__RULE => rm_1c_cfg()->catalogExport()->getRule()
				// нам нужны все свойства, потому что мы их экспортируем
				,Df_Catalog_Model_Product_Exporter::P__NEED_LOAD_ALL_ATTRIBUTES => true
			))->getResult()
		);
	}

	/**
	 * @override
	 * @see Df_Core_Model_Action::getResponseLogActionName()
	 * @used-by Df_Core_Model_Action::getResponseLogFileName()
	 * @return string
	 */
	protected function getResponseLogActionName() {return 'catalog.export';}
}