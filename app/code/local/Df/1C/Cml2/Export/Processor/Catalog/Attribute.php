<?php
/**
 * @method Df_1C_Cml2_Export_Document_Catalog getDocument()
 */
abstract class Df_1C_Cml2_Export_Processor_Catalog_Attribute
	extends \Df\Xml\Generator\Part {
	/**
	 * @param Df_Catalog_Model_Product $product
	 * @return string|string[]|null
	 */
	abstract protected function getЗначение(Df_Catalog_Model_Product $product);
	/** @return string */
	abstract protected function getИд();
	/** @return string */
	abstract protected function getНаименование();

	/** @return string */
	abstract protected function getТипЗначений();

	/**
	 * @override
	 * @return array(string => string|mixed)
	 */
	public function getResult() {return $this->getСвойство();}

	/**
	 * @param Df_Catalog_Model_Product $product
	 * @return array(string => string|string[])|null
	 */
	public function getЗначенияСвойства(Df_Catalog_Model_Product $product) {
		/** @var string $значение */
		$значение = $this->getЗначение($product);
		/** @var array(string => string|string[]) $result */
		return
			// не добавляем в документ незаполненные значения свойств
			is_null($значение) || df_empty_string($значение)
			? null
			: array(
				'Ид' => $this->getИд()
				,'Наименование' => df_cdata($this->getНаименование())
				/**
				 * @see df_cdata() здесь намеренно не вызываем:
				 * пусть свойство-потомок само решает,
				 * нужно ли его значению CDATA.
				 * Обратите внимание, что значением может быть guid,
				 * и там CDATA точно не нужно.
				 */
				,'Значение' => $значение
			)
		;
	}

	/** @return array(string => string|mixed) */
	public function getСвойство() {
		return df_clean_xml(array(
			'Ид' => $this->getИд()
			,'Наименование' => df_cdata($this->getНаименование())
			/**
			 * Текущая версия 4.0.5.2 «Помощника импорта товаров с сайта»
			 * дополнения 1С-Битрикс для обмена данными с интернет-магазином
			 * http://www.1c-bitrix.ru/download/1c/ecommerce/4.0.5.2_UT11.1.9.61.zip
			 * поле «Описание» не обрабатывает,
			 * однако это поле возможно по текущей версии 2.08 стандарта CommerceML 2
			 * http://v8.1c.ru/edi/edi_stnd/90/CML208.XSD
			 */
			,'Описание' => df_cdata($this->getОписание())
			/**
			 * Пока не совсем понимаю, какое значение указывать для поля «Обязательное».
			 * Первой мыслью было указать так:
					$this->getAttribute()->getIsRequired() ? 'Для каталога' : null
			 * Однако неочевидны два момента:
			 * 1) что нужно писать именно «Для каталога», а не «Для предложений»
			 * 2) к каким конкретно товарам 1C применит эту обязательность?
			 * Если ко всем — то это неправильно, потому что в Magento
			 * обязательное свойство обязательно ттолько для товаров тех прикладных типов,
			 * которые содержат данное свойство (а не для всех товаров).
			 *
			 * Более того, текущая версия 4.0.5.2 «Помощника импорта товаров с сайта»
			 * дополнения 1С-Битрикс для обмена данными с интернет-магазином
			 * http://www.1c-bitrix.ru/download/1c/ecommerce/4.0.5.2_UT11.1.9.61.zip
			 * поле «Обязательное» не обрабатывает,
			 * однако это поле возможно по текущей версии 2.08 стандарта CommerceML 2
			 * http://v8.1c.ru/edi/edi_stnd/90/CML208.XSD
			 */
			,'Обязательное' => null
			/**
			 * Если свойство не является множественным,
			 * то не будем добавлять поле «Множественное» к документу
			 * (поля со значием null автоматичеки удаляются функцией @see df_clean_xml())
			 */
			,'Множественное' => $this->isМножественное() ? 1 : null
			,'ТипЗначений' => $this->getТипЗначений()
			,'ВариантыЗначений' => $this->getВариантыЗначений()
			/**
			 * 'ИспользованиеСвойства' => 'ДляТоваров'
			 *
			 * Текущая версия 4.0.5.2 «Помощника импорта товаров с сайта»
			 * дополнения 1С-Битрикс для обмена данными с интернет-магазином
			 * http://www.1c-bitrix.ru/download/1c/ecommerce/4.0.5.2_UT11.1.9.61.zip
			 * поле «ИспользованиеСвойства» не обрабатывает,
			 * однако это поле возможно по текущей версии 2.08 стандарта CommerceML 2
			 * http://v8.1c.ru/edi/edi_stnd/90/CML208.XSD
			 */
		));
	}

	/** @return Df_1C_Cml2_Export_Entry */
	protected function entry() {return Df_1C_Cml2_Export_Entry::s();}

	/** @return array(string => mixed)|null */
	protected function getВариантыЗначений() {return null;}

	/** @return string */
	protected function getОписание() {return '';}

	/** @return bool */
	protected function isМножественное() {return false;}

	/**
	 * @used-by Df_1C_Cml2_Export_Document_Catalog::getProcessorsForVirtualAttributes()
	 * @used-by Df_1C_Cml2_Export_Processor_Catalog_Attribute_Url::i()
	 * @param string $class
	 * @param \Df\Xml\Generator\Document $document
	 * @return Df_1C_Cml2_Export_Processor_Catalog_Attribute
	 */
	public static function ic($class, \Df\Xml\Generator\Document $document) {
		return df_ic($class, __CLASS__, array(self::$P__DOCUMENT => $document));
	}
}