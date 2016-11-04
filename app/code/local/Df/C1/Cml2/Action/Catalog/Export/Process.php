<?php
namespace Df\C1\Cml2\Action\Catalog\Export;
use Df_Catalog_Model_Product_Exporter as Exporter;
/**
 * Экспорт товаров из интернет-магазина в 1С:Управление торговлей
 * http://1c.1c-bitrix.ru/blog/blog1c/catalog_import.php
 */
class Process extends \Df\C1\Cml2\Action\GenericExport {
	/**
	 * @override
	 * @see \Df\C1\Cml2\Action\GenericExport::createDocument()
	 * @used-by \Df\C1\Cml2\Action\GenericExport::getDocument()
	 * @return \Df\C1\Cml2\Export\Document\Catalog
	 */
	protected function createDocument() {return
		\Df\C1\Cml2\Export\Document\Catalog::i(
			Exporter::i([
				Exporter::P__RULE => df_c1_cfg()->catalogExport()->getRule()
				// нам нужны все свойства, потому что мы их экспортируем
				,Exporter::P__NEED_LOAD_ALL_ATTRIBUTES => true
			])->getResult()
		)
	;}

	/**
	 * @override
	 * @see Df_Core_Model_Action::responseLogActionName()
	 * @used-by Df_Core_Model_Action::responseLogName()
	 * @return string
	 */
	protected function responseLogActionName() {return 'catalog.export';}
}