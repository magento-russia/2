<?php
class Df_1C_Cml2_Import_Data_Collection_PriceTypes
	extends Df_1C_Cml2_Import_Data_Collection {
	/**
	 * @override
	 * @see \Df\Xml\Parser\Entity::e()
	 * @return \Df\Xml\X
	 */
	public function e() {
		if (!isset($this->{__METHOD__})) {
			/** @var \Df\Xml\X $result */
			$result = null;
			if (
				Df_1C_Cml2_State_Import::s()->getFileOffers()->getXml()
					->descend('ПакетПредложений/ТипыЦен')
			) {
				$result = Df_1C_Cml2_State_Import::s()->getFileOffers()->getXml();
			}
			else if (
				Df_1C_Cml2_State_Import::s()->getFileCatalogStructure()->getXml()
					->descend('Классификатор/ТипыЦен')
			) {
				$result = Df_1C_Cml2_State_Import::s()->getFileCatalogStructure()->getXml();
			}
			df_assert($result instanceof \Df\Xml\X);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_1C_Cml2_Import_Data_Entity_PriceType */
	public function getMain() {
		if (!isset($this->{__METHOD__})) {
			if (!$this->hasItems()) {
				$this->throwError_noPriceTypes();
			}
			$this->{__METHOD__} = $this->findByName(rm_1c_cfg()->product()->prices()->getMain());
			if (!$this->{__METHOD__}) {
				df_error(
					  'Модуль «1С:Управление торговлей» для Magento'
					. ' не нашёл в полученных из «1С:Управление торговлей» данных'
					. ' цены типового соглашения (типа) «{название типового соглашения}».'
					. "\nИменно это типовое соглашение (тип цен) указано администратором как основное"
					. ' в настройках модуля «1С:Управление торговлей» для Magento:'
					. "\n(«Система» -> «Настройки» -> «1С:Управление торговлей»"
					. ' -> «Российская сборка» -> «1С:Управление торговлей» -> «Цены»'
					. ' -> «Название основной цены или типового соглашения»).'
					. "\nВам сейчас нужно убедиться, что типовое соглашение (тип цен) с данным именем"
					. ' действительно присутствует в «1С:Управление торговлей»'
					. ' и указано в настройках задействованного для обмена данными с интернет-магазином'
					. ' узла обмена в «1С:Управление торговлей».'
					. "\nИнструкция по настройке типового соглашения «1С:Управление торговлей»"
					. ' для обмена данными с интернет-магазином: http://magento-forum.ru/topic/3100/'
					,array('{название типового соглашения}' => rm_1c_cfg()->product()->prices()->getMain())
				);
			}
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return Df_1C_Cml2_Import_Data_Entity_PriceType::class;}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemPath()
	 * @return string|string[]
	 */
	protected function itemPath() {
		/** @var string $type */
		$type = $this->getDocument()->isOffers() ? 'ПакетПредложений' : 'Классификатор';
		return "/КоммерческаяИнформация/{$type}/ТипыЦен/ТипЦены";
	}

	/** @return Df_1C_Cml2_Import_Data_Document */
	private function getDocument() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_1C_Cml2_Import_Data_Document $result */
			$result = null;
			if (
				Df_1C_Cml2_State_Import::s()->getFileOffers()->getXml()
					->descend('ПакетПредложений/ТипыЦен')
			) {
				$result = Df_1C_Cml2_State_Import::s()->getFileOffers()->getXmlDocument();
			}
			/**
			 * В новых версиях модуля 1С-Битрикс (ветка 4, CommerceML 2.0.8)
			 * типы цен неожиданно переместились в файл каталога.
			 */
			else if (
				Df_1C_Cml2_State_Import::s()->getFileCatalogStructure()->getXml()
					->descend('Классификатор/ТипыЦен')
			) {
				$result = Df_1C_Cml2_State_Import::s()->getFileCatalogStructure()->getXmlDocument();
			}
			if (!$result) {
				$this->throwError_noPriceTypes();
			}
			df_assert($result instanceof Df_1C_Cml2_Import_Data_Document);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return void */
	private function throwError_noPriceTypes() {
		df_error(
			'«1С:Управление торговлей» не передала в интернет-магазин цены на товары.'
			. "\nВероятно, используемый для обмена данными с интернет-магазином"
			. ' узел обмена данными «1С:Управление торговлей» настроен не в полной мере.'
			. "\nЕщё раз внимательно прочитайте и выполните инструкции по настройке обмена данными"
			. ' между «1С:Управление торговлей» и Российской сборкой Magento:'
			. ' http://magento-forum.ru/forum/265/'
		);
	}

	/**
	 * @used-by Df_1C_Cml2_State::getPriceTypes()
	 * @return Df_1C_Cml2_Import_Data_Collection_PriceTypes
	 */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}