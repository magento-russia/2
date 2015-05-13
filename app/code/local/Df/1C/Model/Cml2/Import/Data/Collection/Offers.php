<?php
class Df_1C_Model_Cml2_Import_Data_Collection_Offers extends Df_1C_Model_Cml2_Import_Data_Collection {
	/**
	 * @override
	 * @return Df_1C_Model_Cml2_Import_Data_Entity_Offer[]
	 */
	public function getItems() {
		if (!isset($this->{__METHOD__})) {
			// Важно здесь сразу инициализировать переменную $this->{__METHOD__},
			// чтобы не попадать в рекурсию в коде ниже.
			$this->{__METHOD__} = parent::getItems();
			/**
			 * Заметил в магазине термобелье.su,
			 * что «1С: Управление торговлей» передаёт в интернет-магазин в файле offers.xml
			 * простые варианты настраиваемого товара, не передавая при этом сам настраиваемый товар!
			 * Версия «1С: Управление торговлей»: 11.1.2.22
			 * Версия платформы «1С:Предприятие»: 8.2.19.80
			 * Похоже, система так ведёт себя,
			 * когда в «1С: Управление торговлей» характеристики заданы индивидуально для товара,
			 * а не общие для вида номенклатуры.
			 * Цитата из интерфейса «1С: Управление торговлей»:
			 * «Рекомендуется использовать характеристики общие для вида номенклатуры.
			 * Тогда, например, можно задать единую линейку размеров для всей номенклатуры этого вида».
			 * @link http://magento-forum.ru/topic/4197/
			 */
			foreach ($this->{__METHOD__} as $offer) {
				/** @var Df_1C_Model_Cml2_Import_Data_Entity_Offer $offer */
				if ($offer->isTypeConfigurableChild() && is_null($offer->getConfigurableParent())) {
					$this->addItem(Df_1C_Model_Cml2_Import_Data_Entity_Offer_ParentStub::i($offer));
				}
			}
		}
		return $this->{__METHOD__};
	}
	
	/**
	 * @override
	 * @return string
	 */
	protected function getItemClass() {return Df_1C_Model_Cml2_Import_Data_Entity_Offer::_CLASS;}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getItemsXmlPathAsArray() {
		return array(
			''
			,'КоммерческаяИнформация'
			,'ПакетПредложений'
			,'Предложения'
			,'Предложение'
		);
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param Df_Varien_Simplexml_Element $xml
	 * @return Df_1C_Model_Cml2_Import_Data_Collection_Offers
	 */
	public static function i(Df_Varien_Simplexml_Element $xml) {
		return new self(array(self::P__SIMPLE_XML => $xml));
	}
}