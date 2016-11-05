<?php
namespace Df\C1\Cml2\InputRequest;
class Generic extends \Df_Core_Model_InputRequest {
	/** @return string */
	public function getMode() {return $this->getParamFromRange('mode', [
		self::$MODE__CHECK_AUTH
		,self::MODE__DEACTIVATE
		,self::MODE__FILE
		,self::MODE__IMPORT
		,self::MODE__INIT
		,self::MODE__QUERY
		,self::MODE__SUCCESS
	]);}

	/** @return string */
	public function getType() {return $this->getParamFromRange('type', [
		self::TYPE__CATALOG
		,self::TYPE__GET_CATALOG
		,self::TYPE__ORDERS
		,self::TYPE__REFERENCE
	]);}

	/** @return bool */
	public function isCheckAuth() {return self::$MODE__CHECK_AUTH === $this->getMode();}

	/**
	 * @override
	 * @return string
	 */
	protected function getErrorMessagePrefix() {return "1C нарушила протокол обмена данными.\n";}

	/**
	 * Этот режим имеется в версии 4.0.2.3 модуля 1С-Битрикс для обмена с сайтом:
		Процедура ДобавитьПараметрыПротоколаОбменаВСтруктуру(СтруктураПараметров)
			СтруктураПараметров.Вставить("ПараметрЗапросаHTTP_Инициализация"			, "&mode=init");
			СтруктураПараметров.Вставить("ПараметрЗапросаHTTP_ПередачаФайла"			, "&mode=file&filename=");
			СтруктураПараметров.Вставить("ПараметрЗапросаHTTP_ИмпортФайлаСервером"		, "&mode=import&filename=");
			СтруктураПараметров.Вставить("ПараметрЗапросаHTTP_ПолучитьДанные"			, "&mode=query");
			СтруктураПараметров.Вставить("ПараметрЗапросаHTTP_УспешноеЗавершениеИмпорта", "&mode=success");
			СтруктураПараметров.Вставить("ПараметрЗапросаHTTP_ДеактивацияДанныхПоДате"	, "&mode=deactivate");
			(...)
		КонецПроцедуры
	 * http://dev.1c-bitrix.ru/community/blogs/product_features/exchange-module-with-1cbitrix-40.php
	 * Что он означает — пока неясно: надо смотреть исходники последних версий 1С-Битрикс.
	 * В журнале 1С этот режим прокомментирован так:
	 * «Деактивация элементов, не попавшие в полную пакетную выгрузку.»
	 * @used-by getMode()
	 * @used-by \Df\C1\Cml2\Action\Front::action_catalog()
	 */
	const MODE__DEACTIVATE = 'deactivate';
	/**
	 * @used-by getMode()
	 * @used-by \Df\C1\Cml2\Action\Front::action_catalog()
	 */
	const MODE__FILE = 'file';
	/**
	 * @used-by getMode()
	 * @used-by \Df\C1\Cml2\Action\Front::action_catalog()
	 */
	const MODE__IMPORT = 'import';
	/**
	 * @used-by getMode()
	 * @used-by \Df\C1\Cml2\Action\Front::action_catalog()
	 */
	const MODE__INIT = 'init';
	/**
	 * @used-by getMode()
	 * @used-by \Df\C1\Cml2\Action\Front::action_catalogExport()
	 * @used-by \Df\C1\Cml2\Action\Front::action_orders()
	 */
	const MODE__QUERY = 'query';
	/**
	 * @used-by getMode()
	 * @used-by \Df\C1\Cml2\Action\Front::action_orders()
	 */
	const MODE__SUCCESS = 'success';
	/**
	 * 2015-01-07
	 * Режим импорта каталога товаров из интернет-магазина в 1С.
	 * http://1c.1c-bitrix.ru/blog/blog1c/catalog_import.php
	 * @used-by getType()
	 * @used-by \Df\C1\Cml2\Action\Front::_process()
	 */
	const TYPE__GET_CATALOG = 'get_catalog';
	/**
	 * @used-by getType()
	 * @used-by \Df\C1\Cml2\Action\Front::_process()
	 */
	const TYPE__CATALOG = 'catalog';
	/**
	 * @used-by getType()
	 * @used-by \Df\C1\Cml2\Action\Front::_process()
	 */
	const TYPE__ORDERS = 'sale';
	/**
	 * 2015-01-06
	 * В новых версиях модуля обмена 1С-Битрикс (заметил, начиная с 4.0.4.2)
	 * появилась новая функциональная возможность «Обмен пользовательскими справочниками»,
	 * и если включить эту функцию и на появившейся вкладке «Обмен польз. справочников»
	 * указать справочники для обмена,
	 * то 1С будет модуля обмена будет передавать в интернет-магазин указанные справочники
	 * следующими запросами:
			type=reference&mode=checkauth
			type=reference&mode=init&&version=2.08
			type=reference&mode=file&filename=references___43777219-7239-4676-b58d-88673a75326e.xml&
			type=reference&mode=import&filename=references___43777219-7239-4676-b58d-88673a75326e.xml&
	 * http://magento-forum.ru/topic/4891/
	 * @used-by getType()
	 * @used-by \Df\C1\Cml2\Action\Front::_process()
	 */
	const TYPE__REFERENCE = 'reference';

	/**
	 * @used-by getMode()
	 * @var string
	 */
	private static $MODE__CHECK_AUTH = 'checkauth';
}