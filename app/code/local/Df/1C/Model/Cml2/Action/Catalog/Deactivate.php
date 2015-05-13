<?php
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
 * @link http://dev.1c-bitrix.ru/community/blogs/product_features/exchange-module-with-1cbitrix-40.php
 * Что он означает — пока неясно: надо смотреть исходники последних версий 1С-Битрикс.
 * В журнале 1С этот режим прокомментирован так:
 * «Деактивация элементов, не попавшие в полную пакетную выгрузку.»
 */
class Df_1C_Model_Cml2_Action_Catalog_Deactivate extends Df_1C_Model_Cml2_Action_Catalog {
	/**
	 * @override
	 * @return void
	 */
	protected function processInternal() {$this->setResponseBodyAsArrayOfStrings(array('success', ''));}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_1C_Model_Cml2_Action_Catalog_Deactivate
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}