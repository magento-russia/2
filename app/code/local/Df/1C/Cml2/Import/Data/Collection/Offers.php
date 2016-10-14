<?php
class Df_1C_Cml2_Import_Data_Collection_Offers extends Df_1C_Cml2_Import_Data_Collection {
	/**
	 * Заметил в магазине термобелье.su,
	 * что «1С:Управление торговлей» передаёт в интернет-магазин в файле offers.xml
	 * простые варианты настраиваемого товара, не передавая при этом сам настраиваемый товар!
	 * Версия «1С:Управление торговлей»: 11.1.2.22
	 * Версия платформы «1С:Предприятие»: 8.2.19.80
	 * Похоже, система так ведёт себя,
	 * когда в «1С:Управление торговлей» характеристики заданы индивидуально для товара,
	 * а не общие для вида номенклатуры.
	 * Цитата из интерфейса «1С:Управление торговлей»:
	 * «Рекомендуется использовать характеристики общие для вида номенклатуры.
	 * Тогда, например, можно задать единую линейку размеров для всей номенклатуры этого вида».
	 * http://magento-forum.ru/topic/4197/
	 *
	 * 2014-04-11
	 * Заметил в магазине зоомир.укр,
	 * что «1С:Управление торговлей» передаёт в интернет-магазин в файле offers.xml
	 * простые варианты настраиваемого товара,
	 * не передавая при этом сам настраиваемый товар!
	 * При этом в «1С:Управление торговлей» используются характеристики,
	 * общие для вида номенклатуры.
	 * Версия «1С:Управление торговлей»: Управление торговлей для Украины, редакция 3.0
	 * Версия платформы «1С:Предприятие»: 8.3.4.408
	 * http://magento-forum.ru/topic/4347/
	 * При этом система в состоянии идентифицировать товарные предложения
	 * как составные части настраиваемых товаров,
	 * потому что внешние идентификаторы таких товарных предложений
	 * имеют формат
	 * <Ид>816d609d-b8ae-11e3-bba1-08606ed36063#816d6099-b8ae-11e3-bba1-08606ed36063</Ид>,
	 * где часть до «#» — общая для всех простых вариантов настраиваемого товара,
	 * причём эта часть присутствует в файле products.xml.
	 *
	 * products.xml:
		<Товар>
			<Ид>816d609d-b8ae-11e3-bba1-08606ed36063</Ид>
			<Наименование>Аквариум тест 1</Наименование>
			(...)
		</Товар>
	 *
	 * offers.xml:
		<Предложение>
			<Ид>816d609d-b8ae-11e3-bba1-08606ed36063#816d6099-b8ae-11e3-bba1-08606ed36063</Ид>
			<Наименование>Аквариум тест 1 (Белый)</Наименование>
			(...)
		</Предложение>
		<Предложение>
			<Ид>816d609d-b8ae-11e3-bba1-08606ed36063#816d609a-b8ae-11e3-bba1-08606ed36063</Ид>
			<Наименование>Аквариум тест 1 (Черный)</Наименование>
			(...)
		</Предложение>
	 *
	 * 2015-08-04
	 * Тестирую сейчас модуль 1С-Битрикс 5.0.6 / CommerceML версии 2.09
	 * со стандартными демо-данными УТ 11.1.10.138
	 * и заметил, что описанное выше поведение, положе, стало стандартным:
	 * модуль 1С не передает настраиваемый товар в качестве товарного предложения.
	 * В качестве товарных предложений передаются только настраиваемые варианты,
	 * а настраиваемый товар передается в ветке товаров.
	 *
	 * 2015-08-04
	 * @override
	 * @see Df_Core_Xml_Parser_Collection::postInitItems()
	 * @used-by Df_Core_Xml_Parser_Collection::getItems()
	 * @param Df_1C_Cml2_Import_Data_Entity_Offer[] $items
	 * @return void
	 */
	protected function postInitItems(array $items) {
		foreach ($items as $offer) {
			/** @var Df_1C_Cml2_Import_Data_Entity_Offer $offer */
			if ($offer->isTypeConfigurableChild() && is_null($offer->getConfigurableParent())) {
				$this->addItem(Df_1C_Cml2_Import_Data_Entity_Offer_ParentStub::i($offer));
			}
		}
	}
	
	/**
	 * @override
	 * @see Df_Core_Xml_Parser_Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return Df_1C_Cml2_Import_Data_Entity_Offer::_C;}

	/**
	 * @override
	 * @see Df_Core_Xml_Parser_Collection::itemPath()
	 * @return string|string[]
	 */
	protected function itemPath() {
		return '/КоммерческаяИнформация/ПакетПредложений/Предложения/Предложение';
	}

	/**
	 * @used-by Df_1C_Cml2_State_Import_Collections::getOffers()
	 * @used-by Df_1C_Cml2_State_Import_Collections::getOffersBase()
	 * @static
	 * @param Df_Core_Sxe $xml
	 * @return Df_1C_Cml2_Import_Data_Collection_Offers
	 */
	public static function i(Df_Core_Sxe $xml) {return new self(array(self::$P__E => $xml));}
}