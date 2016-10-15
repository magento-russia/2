<?php
class Df_1C_Cml2_Export_DocumentMixin extends \Df\Xml\Generator\DocumentMixin {
	/**
	 * @override
	 * @return array(string => string)
	 */
	public function getAttributes() {
		return array(
			'ВерсияСхемы' => '2.05'
			,'ДатаФормирования' => $this->formatDate(Zend_Date::now())
			,'ФорматДаты' => 'ДФ=yyyy-MM-dd; ДЛФ=DT'
			,'ФорматВремени' => 'ДФ=ЧЧ:мм:сс; ДЛФ=T'
			,'РазделительДатаВремя' => 'T'
			,'ФорматСуммы' => 'ЧЦ=18; ЧДЦ=2; ЧРД=.'
			,'ФорматКоличества' => 'ЧЦ=18; ЧДЦ=2; ЧРД=.'
		);
	}

	/**
	 * @override
	 * @return string
	 */
	public function getTagName() {return 'КоммерческаяИнформация';}

	/**
	 * Документы в кодировке UTF-8 должны передаваться в 1С:Управление торговлей
	 * с символом BOM в начале.
	 * http://habrahabr.ru/company/bitrix/blog/129156/#comment_4277527
	 * @override
	 * @param bool $reformat [optional]
	 * @return string
	 */
	public function getXml($reformat = false) {
		return df_t()->bomAdd($this->parent(__FUNCTION__, $reformat));
	}

	/**
	 * @param Zend_Date $date
	 * @return string
	 */
	protected function formatDate(Zend_Date $date) {
		return implode('T', array(
			df_dts($date, self::DATE_FORMAT), df_dts($date, Zend_Date::TIME_MEDIUM)
		));
	}

	/** @used-by Df_1C_Cml2_Action_GenericExport::generateResponseBodyFake() */

	/**
	 * @used-by Df_1C_Cml2_Export_Processor_Sale_Order::getDocumentData_Order()
	 * @used-by Df_1C_Cml2_Export_Data_Entity_Customer::getDateOfBirthAsString()
	 */
	const DATE_FORMAT = 'y-MM-dd';
	/**
	 * @param \Df\Xml\Generator\Document $parent
	 * @return Df_1C_Cml2_Export_DocumentMixin
	 */
	public static function i(\Df\Xml\Generator\Document $parent) {
		return self::ic(__CLASS__, $parent);
	}
}