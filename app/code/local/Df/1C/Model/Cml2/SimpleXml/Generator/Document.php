<?php
class Df_1C_Model_Cml2_SimpleXml_Generator_Document extends Df_Core_Model_SimpleXml_Generator_Document {
	/**
	 * @override
	 * @return string
	 */
	public function getXml() {
		/**
		 * Документы в кодировке UTF-8 должны передаваться в 1С:Управление торговлей
		 * с символом BOM в начале.
		 * @link http://habrahabr.ru/company/bitrix/blog/129156/#comment_4277527
		 */
		return df_text()->bomAdd(parent::getXml());
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getAttributes() {
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
	protected function getTagName() {return 'КоммерческаяИнформация';}

	/**
	 * @param Zend_Date $date
	 * @return string
	 */
	private function formatDate(Zend_Date $date) {
		return implode('T', array(
			df_dts($date, self::DATE_FORMAT)
			, df_dts($date, Zend_Date::TIME_MEDIUM)
		));
	}

	const _CLASS = __CLASS__;
	const DATE_FORMAT = 'y-MM-dd';

	/** @return Df_1C_Model_Cml2_SimpleXml_Generator_Document */
	public static function i() {return new self;}
}