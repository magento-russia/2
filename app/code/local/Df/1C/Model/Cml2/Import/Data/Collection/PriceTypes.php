<?php
class Df_1C_Model_Cml2_Import_Data_Collection_PriceTypes
	extends Df_1C_Model_Cml2_Import_Data_Collection {
	/** @return Df_1C_Model_Cml2_Import_Data_Entity_PriceType */
	public function getMain() {
		if (!isset($this->{__METHOD__})) {
			if (!$this->hasItems()) {
				$this->throwError_noPriceTypes();
			}
			$this->{__METHOD__} = $this->findByName(df_cfg()->_1c()->product()->prices()->getMain());
			if (!$this->{__METHOD__}) {
				df_error(strtr(
					  'Модуль «1С:Управление торговлей» для Magento'
					. ' не нашёл в полученных из «1С:Управление торговлей» данных'
					. ' цены типового соглашения (типа) «{название типового соглашения}».'
					. "\r\nИменно это типовое соглашение (тип цен) указано администратором как основное"
					. ' в настройках модуля «1С:Управление торговлей» для Magento:'
					. "\r\n(«Система» -> «Настройки» -> «1С:Управление торговлей»"
					. ' -> «Российская сборка» -> «1С:Управление торговлей» -> «Цены»'
					. ' -> «Название основной цены или типового соглашения»).'
					. "\r\nВам сейчас нужно убедиться, что типовое соглашение (тип цен) с данным именем"
					. ' действительно присутствует в «1С:Управление торговлей»'
					. ' и указано в настройках задействованного для обмена данными с интернет-магазином'
					. ' узла обмена в «1С:Управление торговлей».'
					. "\r\nИнструкция по настройке типового соглашения «1С:Управление торговлей»"
					. ' для обмена данными с интернет-магазином: http://magento-forum.ru/topic/3100/'
					,array(
						'{название типового соглашения}' => df_cfg()->_1c()->product()->prices()->getMain()
					)
				));
			}
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return Df_Varien_Simplexml_Element
	 */
	public function getSimpleXmlElement() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Varien_Simplexml_Element $result */
			$result = null;
			if (
				Df_1C_Model_Cml2_State_Import::s()->getFileOffers()->getXml()
					->descend('ПакетПредложений/ТипыЦен')
			) {
				$result = Df_1C_Model_Cml2_State_Import::s()->getFileOffers()->getXml();
			}
			else if (
				Df_1C_Model_Cml2_State_Import::s()->getFileCatalogStructure()->getXml()
					->descend('Классификатор/ТипыЦен')
			) {
				$result = Df_1C_Model_Cml2_State_Import::s()->getFileCatalogStructure()->getXml();
			}
			df_assert($result instanceof Df_Varien_Simplexml_Element);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getItemClass() {return Df_1C_Model_Cml2_Import_Data_Entity_PriceType::_CLASS;}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getItemsXmlPathAsArray() {
		return array(
			''
			,'КоммерческаяИнформация'
			,$this->getDocument()->isOffers() ? 'ПакетПредложений' : 'Классификатор'
			,'ТипыЦен'
			,'ТипЦены'
		);
	}

	/** @return Df_1C_Model_Cml2_Import_Data_Document */
	private function getDocument() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_1C_Model_Cml2_Import_Data_Document $result */
			$result = null;
			if (
				Df_1C_Model_Cml2_State_Import::s()->getFileOffers()->getXml()
					->descend('ПакетПредложений/ТипыЦен')
			) {
				$result = Df_1C_Model_Cml2_State_Import::s()->getFileOffers()->getXmlDocument();
			}
			/**
			 * В новых версиях модуля 1С-Битрикс (ветка 4, CommerceML 2.0.8)
			 * типы цен неожиданно переместились в файл каталога.
			 */
			else if (
				Df_1C_Model_Cml2_State_Import::s()->getFileCatalogStructure()->getXml()
					->descend('Классификатор/ТипыЦен')
			) {
				$result =
					Df_1C_Model_Cml2_State_Import::s()->getFileCatalogStructure()->getXmlDocument()
				;
			}
			if (!$result) {
				$this->throwError_noPriceTypes();
			}
			df_assert($result instanceof Df_1C_Model_Cml2_Import_Data_Document);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return void */
	private function throwError_noPriceTypes() {
		df_error(
			'«1С:Управление торговлей» не передала в интернет-магазин цены на товары.'
			. "\r\nВероятно, используемый для обмена данными с интернет-магазином"
			. ' узел обмена данными «1С:Управление торговлей» настроен не в полной мере.'
			. "\r\nЕщё раз внимательно прочитайте и выполните инструкции по настройке обмена данными"
			. ' между «1С:Управление торговлей» и Российской сборкой Magento:'
			. ' http://magento-forum.ru/forum/265/'
		);
	}

	const _CLASS = __CLASS__;
	/** @return Df_1C_Model_Cml2_Import_Data_Collection_PriceTypes */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}