<?php
namespace Df\Xml\Generator;
/**
 * Класс @see Df_1C_Cml2_Export_Document_Catalog
 * (потомок @see \Df\Xml\Generator\Document)
 * должен наследовать как черты, общие для документов, передаваемых из интернет-магазина в 1C
 * (черты, общие для экспорта не только товаров, но и заказов),
 * так и черты, общие для экспорта товаров из интернет-магазина
 * (черты, общие для экспорта не только в формате 1С, но и, скажем, в формате Яндекс.Маркета).
 *
 * Т.е. нам нужно множественное наследование.
 * Как правило, в современых языках программирования
 * задачи множественного наследования решаются через технологию «mixin»:
 * http://en.wikipedia.org/wiki/Mixin
 * В PHP, начиная с ветки 5.4, технология «mixin» поддерживается под названием «trait»:
 * http://en.wikipedia.org/wiki/Trait_(computer_programming)#PHP
 *
 * Magento 2 требует минимум PHP 5.6:
 * http://mage2.ru/topic/1/
 * Поэтому в Magento 2 мы сможем использовать технологию «trait».
 *
 * Magento 1 должна поддерживать старые версии PHP, начиная с версии 5.2.13:
 * http://magento.com/resources/previous-magento-system-requirements
 * По этой причине нам вместо «mixin»/«trait»
 * приходится решать задачу множественного наследования своим кустарным способом.
 */
/**
 * @method \Df\Xml\Generator\Document getParent()
 */
class DocumentMixin extends \Df_Core_Model_Mixin {
	/** @return \Df\Xml\X|null */
	public function createElement() {return null;}

	/** @return array(string => string)|null */
	public function getAttributes() {return null;}

	/** @return array(string => mixed)|null */
	public function getContentsAsArray() {return null;}

	/** @return string|null */
	public function getDocType() {return null;}

	/** @return \Df\Xml\X|null */
	public function getElement() {return null;}

	/** @return string|null */
	public function getLogDocumentName() {return null;}

	/** @return string */
	public function getOperationNameInPrepositionalCase() {return null;}

	/** @return string|null */
	public function getTagName() {return null;}

	/**
	 * @param bool $reformat [optional]
	 * @return string|null
	 */
	public function getXml($reformat = false) {return null;}

	/** @return bool|null */
	public function hasEncodingWindows1251() {return null;}

	/** @return bool|null */
	public function needDecodeEntities() {return null;}

	/** @return bool|null */
	public function needLog() {return null;}

	/** @return bool|null */
	public function needRemoveLineBreaks() {return null;}

	/** @return bool|null */
	public function needSkipXmlHeader() {return null;}

	/** @return bool|null */
	public function needWrapInCDataAll() {return null;}

	const _C = __CLASS__;
}