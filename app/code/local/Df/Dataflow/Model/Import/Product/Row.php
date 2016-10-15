<?php
class Df_Dataflow_Model_Import_Product_Row extends Df_Dataflow_Model_Import_Abstract_Row {
	/**
	 * 2015-08-10
	 * Проверяем, что все требуемые Magento свойства входят в тот прикладной тип товаров
	 * которому принадлежит текущий создаваемый товар.
	 * @used-by Df_Dataflow_Model_Importer_Product::import()
	 * @return void
	 * @throws Df_Dataflow_Exception_Import
	 */
	public function assertRequiredAttributesBelongToTheProductAttributeSet() {
		// этот метод предназначен только для первичного импорта
		df_assert($this->isProductNew());
		/** @var int $attributeSetId */
		$attributeSetId = $this->getAttributeSetId();
		/** @var array(int => string) $attributeCodes */
		$attributeCodes = rm_attribute_set()->attributeCodes($attributeSetId);
		/** @var string[] $missed */
		$missed = array_diff($this->helper()->getRequiredFields(), $attributeCodes);
		if ($missed) {
			$this->error(
				'Прикладной тип товара «%s», '
				.'указаный в поле «attribute_set» строки №%d импортируемых данных, '
				.' не содержит обязательных для Magento свойств: %s.'
				."\nЕсли прикладной тип товаров был создан системой автоматически — "
				."то это дефект системы, сообщите о нём на форуме Российской сборки Magento."
				."\nЕсли же прикладной тип товаров был создан или редактировался вручную,"
				." то добавьте к нему перечисленные выше свойства."
				,$this->getAttributeSetName()
				,$this->getOrdering()
				,df_csv_pretty_quote($missed)
			);
		}
	}

	/** @return int|null */
	public function getAttributeSetId() {
		if (!isset($this->{__METHOD__})) {
			/** @var int|null $result */
			$result = null;
			if (!is_null($this->getAttributeSetName())) {
				/** @var int $result */
				$result = rm_attribute_set()->idByName($this->getAttributeSetName());
				if (!$result) {
					$this->error(
						'Прикладной тип товара «%s», '
						.'указаный в поле «attribute_set» строки №%d импортируемых данных, '
						.' неизвестен системе.'
						,$this->getAttributeSetName()
						,$this->getOrdering()
					);
				}
			}
			if (!is_null($result)) {
				df_result_integer($result);
			}
			$this->{__METHOD__} = df_n_set($result);
		}
		return df_n_get($this->{__METHOD__});
	}

	/** @return string|null */
	public function getCategoryIdsAsString() {return $this->getFieldValue(self::FIELD__CATEGORY_IDS);}

	/**
	 * Идентификатор товара, расчитанный на основе артикула.
	 * При импорте новых товаров - отсутствует.
	 * @return int|null
	 */
	public function getId() {
		if (!isset($this->{__METHOD__})) {
			// Идентификатор отсутствует у новых товаров.
			$this->{__METHOD__} = df_n_set(df_h()->catalog()->product()->getIdBySku($this->getSku()));
		}
		return df_n_get($this->{__METHOD__});
	}

	/**
	 * Обратите внимание, что идентификатором типа товаров является строка, а не число.
	 * Пример идентификатарора: «simple», «bundle»
	 * @return string
	 */
	public function getProductType() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result =
				$this->getFieldValue(
					self::FIELD__PRODUCT_TYPE
					,false
					,$this->getConfig()->getParam(self::FIELD__PRODUCT_TYPE)
				)
			;
			if (!is_string($result)) {
				$this->throwPleaseFillTheField(
					self::FIELD__PRODUCT_TYPE
					,Df_Dataflow_Model_Import_Config::DATAFLOW_PARAM__PRODUCT_TYPE
					,$this->getOrdering()
					/**
					 * Тип товара нам может понадобиться только при первичном импорте.
					 * Явно указываем, что мы подразумеваем именно первичный импорт,
					 * чтобы не ставить пользователя в ступор такими фразами:
					 * «Вы должны либо заполнить поле «type» в строке импортируемых данных №1,
					 * либо заполнить поле «type» в профиле Magento Dataflow.».
					 * в том случае, когда артикул товара по какой-то причине указапн неправильно,
					 * и пользователь думает, что он выполняет обновление,
					 * а система подразумевает именно первичный импорт.
					 */
					,$treatedAsNew = true
				);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getSku() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getFieldValue(self::FIELD__SKU, $isRequired = true);
			df_result_sku($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return string|null */
	public function getSkuNew() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|null $result */
			$result = $this->getFieldValue(self::FIELD__SKU_NEW, $isRequired = false);
			if ($result) {
				df_result_sku($result);
			}
			$this->{__METHOD__} = df_n_set($result);
		}
		return df_n_get($this->{__METHOD__});
	}

	/** @return Mage_Core_Model_Website[] */
	public function getWebsites() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Core_Model_Website[] $result */
			$result = array();
			if (!is_null($this->getWebsitesAsString())) {
				/** @var string[] $websiteCodes */
				$websiteCodes = df_csv_parse($this->getWebsitesAsString());
				df_assert_array($websiteCodes);
				foreach ($websiteCodes as $websiteCode) {
					/** @var string $websiteCode */
					/** @var Mage_Core_Model_Website $website */
					$website = df_website(df_trim($websiteCode));
					if (!$website) {
						$this->error(
							'Сайт с кодом «%s», указанный в строке №%d, не найден в системе.',
							$websiteCode,
							$this->getOrdering()
						);
					}
					$result[] = $website;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function isProductNew() {return is_null($this->getId());}

	/**
	 * @used-by Df_Dataflow_Model_Importer_Product::store()
	 * @return Df_Core_Model_StoreM
	 */
	public function store() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|null $storeCode */
			$storeCode = $this->getFieldValue(self::FIELD__STORE);
			if (is_null($storeCode)) {
				$storeCode =
					$this->getConfig()->getParam(
						Df_Dataflow_Model_Import_Config::DATAFLOW_PARAM__STORE
					)
				;
			}
			if (is_null($storeCode)) {
				$this->throwPleaseFillTheField(
					self::FIELD__STORE
					,Df_Dataflow_Model_Import_Config::DATAFLOW_PARAM__STORE
					,$this->getOrdering()
				);
			}
			df_assert_string($storeCode);
			/** @var Df_Core_Model_StoreM $result */
			$result = df_store($storeCode);
			if (!$result) {
				$this->error(
					"В строке импортируемых данных №%d указан несуществующий магазин «%s»."
					."\nВы должны либо для каждого импортируемого товара указать магазин"
					." в поле «%s» строки импортируемых данных,"
					." либо указать магазин по умолчанию в поле «%s» профиля Magento Dataflow."
					,$this->getOrdering()
					,$storeCode
					,self::FIELD__STORE
					,Df_Dataflow_Model_Import_Config::DATAFLOW_PARAM__STORE
				);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string|null */
	private function getAttributeSetName() {
		// Название набора свойств обязательно для указания только для новых товаров
		return $this->getFieldValue(self::FIELD__ATTRIBUTE_SET, $isRequired = $this->isProductNew());
	}

	/** @return string|null */
	private function getWebsitesAsString() {return $this->getFieldValue(self::FIELD__WEBSITES);}

	/** @return Df_Catalog_Helper_Product_Dataflow */
	private function helper() {return Df_Catalog_Helper_Product_Dataflow::s();}

	/**
	 * @param string $fieldName
	 * @param string $profileFieldName
	 * @param int $ordering
	 * @param bool $treatedAsNew [optional]
	 * @return void
	 * @throws \Df\Core\Exception
	 */
	private function throwPleaseFillTheField(
		$fieldName, $profileFieldName, $ordering, $treatedAsNew = false
	) {
		$this->error(
			($treatedAsNew
			? "Система считает Ваш товар новым (не нашла его в своей базе данных по артикулу).\n"
			: '')
			. 'Вы должны либо заполнить поле «%s» в строке импортируемых данных №%d, '
			.'либо заполнить поле «%s» в профиле Magento Dataflow.'
			,$fieldName, $ordering, $profileFieldName
		);
	}


	const FIELD__ATTRIBUTE_SET = 'attribute_set';
	const FIELD__BUNDLE = 'df_bundle';
	const FIELD__CATEGORY_IDS = 'category_ids';
	const FIELD__PRODUCT_TYPE = 'type';
	const FIELD__SKU = 'sku';
	const FIELD__SKU_NEW = 'sku_new';
	const FIELD__STORE = 'store';
	const FIELD__WEBSITES = 'websites';

	/**
	 * @static
	 * @param array(string => mixed) $productData
	 * @param int $ordering [optional]
	 * @return Df_Dataflow_Model_Import_Product_Row
	 */
	public static function i(array $productData, $ordering = 1) {
		return new self(array(self::P__ROW_AS_ARRAY => $productData, self::P__ORDERING => $ordering));
	}
}