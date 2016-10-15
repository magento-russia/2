<?php
class Df_1C_Cml2_Import_Processor_Product_Type_Configurable
	extends Df_1C_Cml2_Import_Processor_Product_Type {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		if ($this->getEntityOffer()->isTypeConfigurableParent()) {
			// Сначала импортируем настраиваемые варианты в виде простых товаров
			$this->importChildren();
			// Затем создаём настраиваемый товар
			$this->importParent();
		}
	}

	/**
	 * @param Df_1C_Cml2_Import_Data_Entity_Offer $offer
	 * @return array(array(string => string|int|float)))
	 */
	protected function getConfigurableProductData(Df_1C_Cml2_Import_Data_Entity_Offer $offer) {
		/** @var array(mixed => mixed) $result */
		$result = array();
		foreach ($this->getTypeInstance()->getConfigurableAttributesAsArray() as $attribute) {
			/** @var array(string => string|int) $attribute */
			/** @var Df_1C_Cml2_Import_Data_Entity_OfferPart_OptionValue $optionValue */
			$optionValue = $offer->getOptionValues()->findByAttributeId($attribute['attribute_id']);
			/** @var int $valueId */
			$valueId = $optionValue->getValueId();
			df_nat($valueId);
			$result[]= array(
				'attribute_id' => $attribute['attribute_id']
				,'pricing_value' => $offer->getProduct()->getPrice()
				,'is_percent' => false
				,'value_index' => $valueId
				,'use_default_value' => true
			);
		}
		return $result;
	}

	/** @return array(int => array(array(string => string|int|float))) */
	protected function getConfigurableProductsData() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(int => array(array(string => string|int|float))) $result */
			$result = array();
			foreach ($this->getEntityOffer()->getConfigurableChildren() as $offer) {
				/** @var Df_1C_Cml2_Import_Data_Entity_Offer $offer */
				$result[$offer->getProduct()->getId()] = $this->getConfigurableProductData($offer);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Обратите внимание, что 1С может вполне не передавать цену.
	 * Это возможно в следующих ситуациях:
	 * 1) Когда цена на товар отсутствует в 1С
	 * 2) Когда передача цен отключена в настройках узла обмена
	 * (а это возможно, как минимум, в новых версиях модуля 1С-Битрикс (ветка 4)).
	 * 3) В новых версиях  модуля 1С-Битрикс (ветка 4, CommerceML 2.0.8)
	 * 1С передаёт цены не в файле offers.xml (как было в прежних версиях),
	 * а отдельным файлом prices_*.xml, который передаётся после файла offers_*.xml,
	 * в то время как файл offers_*.xml цен не содержит.
	 * @override
	 * @return float|null
	 */
	protected function getPrice() {
		if (!isset($this->{__METHOD__})) {
			/** @var float|null $result */
			$result = null;
			//Mage::log(__METHOD__);
			//Mage::log('children count: ' . count($this->getEntityOffer()->getConfigurableChildren()));
			foreach ($this->getEntityOffer()->getConfigurableChildren() as $offer) {
				/** @var Df_1C_Cml2_Import_Data_Entity_Offer $offer */
				/** @var float|null $currentPrice */
				//Mage::log('product name: ' . $offer->getProduct()->getName());
				//Mage::log('product price: ' . $offer->getProduct()->getPrice());
				$currentPrice = $offer->getProduct()->getPrice();
				/**
				 * Раньше тут стояло: !is_null($currentPrice), что неверно.
				 * Настраиваемые варианты с нулевой ценой надо игнорировать
				 * при расчёте стоимости настраиваемого товара
				 * (вариантов с нулевой ценой наверняка просто нет на складе),
				 * потому что иначе цена настраиваемого товара получится равной нулю.
				 * ведь по данному алгоритму ценой настраиваемого товара считается
				 * цена самого дешёвого настраиваемого варианта.
				 */
				if (0 < $currentPrice) {
					/** @var float $currentPriceAsFloat */
					$currentPriceAsFloat = df_float($currentPrice);
					if (is_null($result) || ($result > $currentPriceAsFloat)) {
						$result = $currentPriceAsFloat;
					}
				}
			}
			$this->{__METHOD__} = df_n_set($result);
		}
		return df_n_get($this->{__METHOD__});
	}

	/** @return Df_Catalog_Model_Product */
	protected function getProductMagento() {df_abstract(__METHOD__);}

	/**
	 * @override
	 * @return string
	 */
	protected function getSku() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
 			if ($this->getExistingMagentoProduct()) {
				$result = $this->getExistingMagentoProduct()->getSku();
			}
			else {
				$result = $this->getEntityProduct()->getSku();
				if (!$result) {
					rm_1c_log(
						'У товара «%s» в 1С отсутствует артикул.', $this->getEntityProduct()->getName()
					);
					$result = $this->getEntityOffer()->getExternalId();
				}
				$result = df_sku_adapt($result);
				if (df_h()->catalog()->product()->isExist($result)) {
					/** @var Df_Catalog_Model_Product $existingProduct */
					$existingProduct = df_product($result);
					// Вдруг товар с данным артикулом уже присутствует в системе?
					rm_1c_log(
						'В магазине уже присутствует товар с артикулом «{артикул}»:'
						. ' он имеет номер {идентификатор уже имеющегося товара},'
						. ' название «{название уже имеющегося товара}»'
						. ' и внешний идентификатор {внешний идентификатор уже имеющегося товара}.'
						, array(
							'{артикул}' => $result
							,'{идентификатор уже имеющегося товара}' => $existingProduct->getId()
							,'{внешний идентификатор уже имеющегося товара}' =>
									$existingProduct->getExternalId()
							,'{название уже имеющегося товара}' => $existingProduct->getName()
						)
					);
					df_assert_ne($result, $this->getEntityOffer()->getExternalId());
					$result = df_sku_adapt($this->getEntityOffer()->getExternalId());
				}
			}
			df_result_sku($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getType() {return Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE;}

	/** @return Mage_Catalog_Model_Product_Type_Configurable */
	protected function getTypeInstance() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getProductMagento()->getTypeInstance();
			df_assert($this->{__METHOD__} instanceof Mage_Catalog_Model_Product_Type_Configurable);
		}
		return $this->{__METHOD__};
	}

	/** @return void */
	private function importChildren() {
		/** @var int $count */
		$count = count($this->getEntityOffer()->getConfigurableChildren());
		if (!$count) {
			rm_1c_log('Простые варианты настраиваемых товаров отсутствуют.');
		}
		else {
			rm_1c_log('Найдено простых вариантов настраиваемых товаров: %d.', $count);
			rm_1c_log('Импорт простых вариантов настраиваемых товаров начат.');
			foreach ($this->getEntityOffer()->getConfigurableChildren() as $offer) {
				/** @var Df_1C_Cml2_Import_Data_Entity_Offer $offer */
				Df_1C_Cml2_Import_Processor_Product_Type_Configurable_Child::p($offer);
			}
			rm_1c_log('Импорт простых вариантов настраиваемых товаров завершён.');
		}
	}

	/** @return void */
	private function importParent() {
		$this->getExistingMagentoProduct()
		? $this->importParentUpdate()
		: $this->importParentNew();
	}

	/** @return void */
	private function importParentNew() {
		Df_1C_Cml2_Import_Processor_Product_Type_Configurable_New::p_new($this);
	}

	/** @return void */
	private function importParentUpdate() {
		Df_1C_Cml2_Import_Processor_Product_Type_Configurable_Update::p_update($this);
	}

	/**
	 * @override
	 * @return int
	 */
	protected function getVisibility() {return Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH;}

	/**
	 * @used-by Df_1C_Cml2_Action_Catalog_Import::importProductsConfigurable()
	 * @param Df_1C_Cml2_Import_Data_Entity_Offer $offer
	 * @return void
	 */
	public static function p(Df_1C_Cml2_Import_Data_Entity_Offer $offer) {
		self::ic(__CLASS__, $offer)->process();
	}
}