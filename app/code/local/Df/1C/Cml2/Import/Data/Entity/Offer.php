<?php
class Df_1C_Cml2_Import_Data_Entity_Offer extends Df_1C_Cml2_Import_Data_Entity {
	/** @return array(string => Df_Catalog_Model_Resource_Eav_Attribute) */
	public function getConfigurableAttributes() {
		if (!isset($this->{__METHOD__})) {
			df_assert($this->isTypeConfigurableParent() || $this->isTypeConfigurableChild());
			/** @var array(string => Df_Catalog_Model_Resource_Eav_Attribute) $result */
			$result = array();
			if ($this->isTypeConfigurableParent()) {
				foreach ($this->getConfigurableChildren() as $child) {
					/** @var Df_1C_Cml2_Import_Data_Entity_Offer $child */
					$result += $child->getConfigurableAttributes();
				}
			}
			else {
				foreach ($this->getOptionValues() as $optionValue) {
					/** @var Df_1C_Cml2_Import_Data_Entity_OfferPart_OptionValue $optionValue */
					$result[$optionValue->getAttributeMagento()->getAttributeCode()] =
						$optionValue->getAttributeMagento()
					;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_1C_Cml2_Import_Data_Entity_Offer[] */
	public function getConfigurableChildren() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_1C_Cml2_Import_Data_Entity_Offer[] $result */
			$result = array();
			if (!$this->isTypeConfigurableChild()) {
				foreach ($this->getStateOffers() as $offer) {
					/** @var Df_1C_Cml2_Import_Data_Entity_Offer $offer */
					if ($offer->isTypeConfigurableChild()) {
						if ($this->getExternalId() === $offer->getExternalIdForConfigurableParent()) {
							$result[]= $offer;
						}
					}
				}
			}
			if ($result) {
				rm_1c_log(
					"У товара «%s» найдено %d вариантов:\n%s"
					, $this->getName()
					, count($result)
					/** @uses Df_1C_Cml2_Import_Data_Entity_Offer::getName() */
					, df_cc_n(df_each($result, 'getName'))
				);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Обратите внимание, что если мы действиетльно работаем с настраиваемыми товарами,
	 * то предложение-родитель у нас в коллекции есть всегда,
	 * даже если его нет в offers.xml,
	 * потому что при отсутствии предложения-родителя в offers.xml
	 * мы его добавляем в коллекцию искусственно:
	 * @see Df_1C_Cml2_Import_Data_Collection_Offers::getItems()
	 *
	 * @return Df_1C_Cml2_Import_Data_Entity_Offer|null
	 */
	public function getConfigurableParent() {
		return
			!$this->isTypeConfigurableChild()
			? null
			: $this->getStateOffers()->findByExternalId(
				$this->getExternalIdForConfigurableParent()
			)
		;
	}
	
	/** @return Df_1C_Cml2_Import_Data_Entity_Product */
	public function getEntityProduct() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->getStateProductEntities()->findByExternalId(
					$this->getExternalIdForConfigurableParent()
				)
			;
			df_assert(!!$this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getExternalIdForConfigurableParent() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_first($this->getExternalIdExploded());
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return Df_1C_Cml2_Import_Data_Collection_OfferPart_OptionValues */
	public function getOptionValues() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				!$this->isBase()
				? $this->getBase()->getOptionValues()
				: Df_1C_Cml2_Import_Data_Collection_OfferPart_OptionValues::i($this, $this->e())
			;
			/**
			 * НЕЛЬЗЯ автоматически вызывать здесь
			 * @see Df_1C_Cml2_Import_Data_Collection_OfferPart_OptionValues::addAbsentItems(),
			 * потому что иначе мы попадём рекурсию.
			 */
		}
		return $this->{__METHOD__};
	}

	/** @return Df_1C_Cml2_Import_Data_Collection_OfferPart_Prices */
	public function getPrices() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_1C_Cml2_Import_Data_Collection_OfferPart_Prices::i(
				$this->e(), $this
			);
		}
		return $this->{__METHOD__};
	}
	
	/** @return Df_Catalog_Model_Product */
	public function getProduct() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df()->registry()->products()->findByExternalId($this->getExternalId());
			if (!$this->{__METHOD__}) {
				df_error('Товар не найден в реестре: «%s».', $this->getExternalId());
			}
		}
		return $this->{__METHOD__};
	}

	/**
	 * Как ни странно, в 1С количество может быть дробным:
	 * http://magento-forum.ru/topic/4389/
	 *
	 * 2015-01-23
	 * В Magento количество товара также может быть дробным:
	 * @see Mage_CatalogInventory_Model_Stock_Item::getQty()
	 * @see Mage_CatalogInventory_Model_Stock_Item::getMinQty()
	 *
	 * Обратите внимание, что в новых версиях модуля 1С-Битрикс
	 * текущий файл offers_*.xml может не содержать информации о количестве товара,
	 * потому что количества теперь передаются отдельным файлом.
	 * Поэтому для метода теперь допустимо вернуть null.
	 *
	 * @return float|null
	 */
	public function getQuantity() {
		if (!isset($this->{__METHOD__})) {
			/** @var float|null $result */
			/**
			 * В новой версии модуля 1С-Битрикс (ветка 4, CommerceML 2.0.8)
			 * 1С передаёт товарные остатки отдельным файлом rests_*.xml,
			 * который имеет следующую структуру:
					<Предложение>
						<Ид>cbcf4968-55bc-11d9-848a-00112f43529a</Ид>
						<Остатки>
							<Остаток>
								<Количество>765</Количество>
							</Остаток>
						</Остатки>
					</Предложение>
			 */
			$result = $this->descendF('Остатки/Остаток/Количество');
			if (is_null($result)) {
				/**
				 * Если новой версии модуля 1С-Битрикс (ветка 4, CommerceML 2.0.8)
				 * включена опция «Выгружать остатки по складам», то структура данных будет следующей:
					<Предложение>
						<Ид>cbcf4968-55bc-11d9-848a-00112f43529a</Ид>
						<Остатки>
							<Остаток>
								<Склад>
									<Ид>03ce4b6e-3ff7-11e0-af05-0015e9b8c48d</Ид>
									<Количество>0</Количество>
								</Склад>
							</Остаток>
							<Остаток>
								<Склад>
									<Ид>08305acc-7303-11df-b338-0011955cba6b</Ид>
									<Количество>201</Количество>
								</Склад>
							</Остаток>
							<Остаток>
								<Склад>
									<Ид>a4212b46-730a-11df-b338-0011955cba6b</Ид>
									<Количество>423</Количество>
								</Склад>
							</Остаток>
						</Остатки>
					</Предложение>
				 */
				if ($this->e()->descend('Остатки/Остаток/Склад')) {
					/**
					 * Включена опция «Выгружать остатки по складам».
					 * Конечно, можно было бы просто возбудить здесь исключительную ситуацию
					 * и потребовать у администраторов не включать эту опцию,
					 * но нам проще молча обработать это случай (просуммировать остатки по складам),
					 * чем решать проблемы с администраторами
					 * (их много, ведь Российская сборка Magento — тиражируемый продукт).
					 *
					 * 2015-08-04
					 * Также обратите внимание, что в версии 5.0.6 модуля 1С-Битрикс
					 * опция «Выгружать остатки по складам» включена по умолчанию,
					 * так что надо молча корректно обрабатывать такую ситуацию,
					 * а не принуждать администратора отключать опцию.
					 */
					/** @var \Df\Xml\X[] $elements */
					$elements = $this->e()->xpathA('Остатки/Остаток/Склад/Количество');
					$result = 0;
					foreach ($elements as $element) {
						/** @var \Df\Xml\X $element */
						$result += df_leaf_f($element);
					}
				}
				/**
				 * 2015-01-05
				 * В схеме CommerceML 2.0.7 (в частности, версии 3.1.2.31 модуля обмена)
				 * http://1c.1c-bitrix.ru/download/1c/ecommerce/ut_11_1_4_13.zip
				 * при включенности опции «Выгружать остатки по складам»
				 * остатки по складам передаются иначе:
				 *
					<Предложения>
						<Предложение>
							<Ид>cbcf4931-55bc-11d9-848a-00112f43529a</Ид>
							<Штрихкод>2000000036489</Штрихкод>
							<Наименование>Х-78666 Атлант Холодильный комбинат</Наименование>
							<БазоваяЕдиница Код="796" НаименованиеПолное="Штука" МеждународноеСокращение="PCE">шт</БазоваяЕдиница>
							<Склад ИдСклада="03ce4b6e-3ff7-11e0-af05-0015e9b8c48d" КоличествоНаСкладе=""/>
							<Склад ИдСклада="163cab5e-35ae-11e0-aefc-0015e9b8c48d" КоличествоНаСкладе=""/>
							<Склад ИдСклада="50d41482-e4f3-11e0-af8f-0015e9b8c48d" КоличествоНаСкладе=""/>
							<Склад ИдСклада="a4212b46-730a-11df-b338-0011955cba6b" КоличествоНаСкладе=""/>
							<Склад ИдСклада="4609c9f9-32c2-11e0-aef8-0015e9b8c48d" КоличествоНаСкладе="2"/>
							<Склад ИдСклада="1418c670-7307-11df-b338-0011955cba6b" КоличествоНаСкладе=""/>
							<Склад ИдСклада="9078db1b-ea8b-11e0-95a2-00055d4ef1e7" КоличествоНаСкладе=""/>
							<Склад ИдСклада="03ce4b6f-3ff7-11e0-af05-0015e9b8c48d" КоличествоНаСкладе=""/>
							<Склад ИдСклада="3f86a8a6-4a24-11e0-af0f-0015e9b8c48d" КоличествоНаСкладе="30"/>
							<Склад ИдСклада="08305acc-7303-11df-b338-0011955cba6b" КоличествоНаСкладе="1"/>
							<Склад ИдСклада="6f87e83f-722c-11df-b336-0011955cba6b" КоличествоНаСкладе="31"/>
							<Цены>
								<Цена>
									<Представление>24 568 RUB за шт</Представление>
									<ИдТипаЦены>ab933e2d-9418-11e4-8a4b-4061868fc6eb</ИдТипаЦены>
									<ЦенаЗаЕдиницу>24568.00</ЦенаЗаЕдиницу>
									<Валюта>RUB</Валюта>
									<Единица>шт</Единица>
									<Коэффициент>1</Коэффициент>
								</Цена>
							</Цены>
							<Количество>64</Количество>
						</Предложение>
					</Предложения>
				 * http://magento-forum.ru/topic/4868/
				 */
				else if ($this->isChildExist('Склад')) {
					/**
					 * Включена опция «Выгружать остатки по складам».
					 * Конечно, можно было бы просто возбудить здесь исключительную ситцацию
					 * и потребовать у администраторов не включать эту опцию,
					 * но нам проще молча обработать это случай (просуммировать остатки по складам),
					 * чем решать проблемы с администраторами
					 * (их много, ведь Российская сборка Magento — тиражируемый продукт).
					 */
					/** @var \Df\Xml\X[] $elements */
					$elements = $this->e()->xpathA('Склад');
					$result = 0;
					foreach ($elements as $element) {
						/** @var \Df\Xml\X $element */
						$result += df_float($element->getAttribute('КоличествоНаСкладе'));
					}
				}
				else {
					$result = $this->leaf('Количество');
					if (!is_null($result)) {
						$result = df_float($result);
					}
				}
			}
			$this->{__METHOD__} = df_n_set($result);
		}
		return df_n_get($this->{__METHOD__});
	}

	/** @return Df_1C_Cml2_Import_Data_Entity_Offer[] */
	public function getSiblings() {
		df_assert($this->isTypeConfigurableChild());
		return $this->getConfigurableParent()->getConfigurableChildren();
	}

	/** @return bool */
	public function isTypeConfigurableChild() {
		if (!isset($this->{__METHOD__})) {
			/** @var bool $result */
			$result = false;
			if (1 < count($this->getExternalIdExploded())) {
				/**
				 * Обратите внимание, что в данном месте мы не можем вызывать метод
				 * @see Df_1C_Cml2_Import_Data_Entity_Offer::isTypeConfigurableParent(),
				 * иначе попадём в рекурсию.
				 */
				if ($this->getStateOffers()->findByExternalId(
					$this->getExternalIdForConfigurableParent()
				)) {
					$result = true;
				}
				else {
					/**
					 * Заметил в магазине термобелье.su,
					 * что «1С:Управление торговлей» передаёт в интернет-магазин в файле offers.xml
					 * простые варианты настраиваемого товара,
					 * не передавая при этом сам настраиваемый товар!
					 * Версия «1С:Управление торговлей»: 11.1.2.22
					 * Версия платформы «1С:Предприятие»: 8.2.19.80
					 * Похоже, система так ведёт себя,
					 * когда в «1С:Управление торговлей» характеристики заданы индивидуально для товара,
					 * а не общие для вида номенклатуры.
					 * Цитата из интерфейса «1С:Управление торговлей»:
					 * «Рекомендуется использовать характеристики общие для вида номенклатуры.
					 * Тогда, например, можно задать единую линейку размеров
					 * для всей номенклатуры этого вида».
					 * http://magento-forum.ru/topic/4197/
					 *
					 * В этом случае считаем товарное предложение
					 * не простым вариантом настраиваемого товара,
					 * а простым товаром.
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
					 */
					$result =
						!!$this->getStateProductEntities()->findByExternalId(
							$this->getExternalIdForConfigurableParent()
						)
					;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function isTypeConfigurableParent() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = !!$this->getConfigurableChildren();
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function isTypeSimple() {
		return !$this->isTypeConfigurableChild() && !$this->isTypeConfigurableParent();
	}

	/**
	 * В новых версиях модуля обмена 1С-Битрикс
	 * товарное предложение может быть размазано на несколько файлов:
	 * offers_*.xml, prices_*.xml, rests_*.xml.
	 * Данный метод возвращает базовую информацию товарного предложения
	 * (ту, которая содержится в файле offers_*.xml):
	 * название, характеристики, настраиваемые опции.
	 * @return Df_1C_Cml2_Import_Data_Entity_Offer
	 */
	private function getBase() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->isBase()
				? $this
				: Df_1C_Cml2_State_Import::s()->collections()->getOffersBase()
					->findByExternalId($this->getExternalId())
			;
			df_assert($this->{__METHOD__} instanceof Df_1C_Cml2_Import_Data_Entity_Offer);
		}
		return $this->{__METHOD__};
	}

	/** @return string[] */
	private function getExternalIdExploded() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = explode('#', $this->getExternalId());
		}
		return $this->{__METHOD__};
	}

	/** @return Df_1C_Cml2_Import_Data_Collection_Offers */
	private function getStateOffers() {
		return Df_1C_Cml2_State_Import::s()->collections()->getOffers();
	}

	/** @return Df_1C_Cml2_Import_Data_Collection_Products */
	private function getStateProductEntities() {
		return Df_1C_Cml2_State_Import::s()->collections()->getProducts();
	}

	/** @return bool */
	private function isBase() {return !!parent::getName();}

	/**
	 * @used-by Df_1C_Cml2_Import_Data_Collection_Offers::itemClass()
	 * @used-by Df_1C_Cml2_Import_Data_Entity_OfferPart_OptionValue::_construct()
	 * @used-by Df_1C_Cml2_Import_Data_Collection_OfferPart_OptionValues::_construct()
	 * @used-by Df_1C_Cml2_Import_Data_Collection_OfferPart_Prices::_construct()
	 * @used-by Df_1C_Cml2_Import_Data_Entity_AttributeValue_OfferPart::_construct()
	 * @used-by Df_1C_Cml2_Import_Data_Entity_Offer_ParentStub::_construct()
	 * @used-by Df_1C_Cml2_Import_Processor_Product::_construct()
	 */
	const _C = __CLASS__;
}