<?php
namespace Df\C1\Cml2\Export;
use Df\Xml\Generator\Document as Document;
class DocumentMixin extends \Df\Xml\Generator\DocumentMixin {
	/**
	 * @override
	 * @return array(string => string)
	 */
	public function getAttributes() {return [
		'ВерсияСхемы' => '2.05'
		,'ДатаФормирования' => $this->formatDate(\Zend_Date::now())
		,'ФорматДаты' => 'ДФ=yyyy-MM-dd; ДЛФ=DT'
		,'ФорматВремени' => 'ДФ=ЧЧ:мм:сс; ДЛФ=T'
		,'РазделительДатаВремя' => 'T'
		,'ФорматСуммы' => 'ЧЦ=18; ЧДЦ=2; ЧРД=.'
		,'ФорматКоличества' => 'ЧЦ=18; ЧДЦ=2; ЧРД=.'
	];}

	/**
	 * @override
	 * @return string
	 */
	public function tag() {return 'КоммерческаяИнформация';}

	/**
	 * Документы в кодировке UTF-8 должны передаваться в 1С:Управление торговлей
	 * с символом BOM в начале.
	 * http://habrahabr.ru/company/bitrix/blog/129156/#comment_4277527
	 * @override
	 * @param bool $reformat [optional]
	 * @return string
	 */
	public function getXml($reformat = false) {return
		df_t()->bomAdd($this->parent(__FUNCTION__, $reformat))
	;}

	/**
	 * @param \Zend_Date $date
	 * @return string
	 */
	protected function formatDate(\Zend_Date $date) {return implode('T', [
		df_dts($date, self::DATE_FORMAT), df_dts($date, \Zend_Date::TIME_MEDIUM)
	]);}
	/**
	 * @used-by \Df\C1\Cml2\Export\Processor\Sale\Order::getDocumentData_Order()
	 * @used-by \Df\C1\Cml2\Export\Data\Entity\Customer::getDateOfBirthAsString()
	 */
	const DATE_FORMAT = 'y-MM-dd';
	/**
	 * @param Document $parent
	 * @return \Df\C1\Cml2\Export\DocumentMixin
	 */
	public static function i(Document $parent) {return self::ic(__CLASS__, $parent);}
}