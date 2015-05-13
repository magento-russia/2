<?php
class Df_Dataflow_Model_Import_Product_Row extends Df_Dataflow_Model_Import_Abstract_Row {
	/** @return int|null */
	public function getAttributeSetId() {
		if (!isset($this->{__METHOD__})) {
			/** @var int|null $result */
			$result = null;
			if (!is_null($this->getAttributeSetName())){
				/** @var Mage_Eav_Model_Entity_Attribute_Set|null $attributeSet */
				$attributeSet = df()->registry()->attributeSets()->findByLabel($this->getAttributeSetName());
				if (!$attributeSet) {
					$this->error(
						'Прикладной тип товара «%s», '
						.'указаный в поле «attribute_set» строки №%d импортируемых данных, '
						.' неизвестен системе.'
						,$this->getAttributeSetName()
						,$this->getOrdering()
					);
				}
				/** @var int $result */
				$result = rm_nat0($attributeSet->getId());
			}
			if (!is_null($result)) {
				df_result_integer($result);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
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
			$this->{__METHOD__} = rm_n_set(df_h()->catalog()->product()->getIdBySku($this->getSku()));
		}
		return rm_n_get($this->{__METHOD__});
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
			$this->{__METHOD__} = rm_n_set($result);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return Mage_Core_Model_Store */
	public function getStore() {
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
			/** @var Mage_Core_Model_Store $result */
			$result = Mage::app()->getStore($storeCode);
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

	/** @return Mage_Core_Model_Website[] */
	public function getWebsites() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Core_Model_Website[] $result */
			$result = array();
			if (!is_null($this->getWebsitesAsString())) {
				/** @var array $websiteCodes */
				$websiteCodes = explode(
					',',
					$this->getWebsitesAsString()
				);
				df_assert_array($websiteCodes);
				foreach ($websiteCodes as $websiteCode) {
					/** @var string $websiteCode */
					/** @var Mage_Core_Model_Website $website */
					$website = Mage::app()
						->getWebsite(df_trim($websiteCode));
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

	/** @return string|null */
	private function getAttributeSetName() {
		// Название набора свойств обязательно для указания только для новых товаров
		return $this->getFieldValue(self::FIELD__ATTRIBUTE_SET, $isRequired = $this->isProductNew());
	}

	/** @return string|null */
	private function getWebsitesAsString() {return $this->getFieldValue(self::FIELD__WEBSITES);}

	/**
	 * @param string $fieldName
	 * @param string $profileFieldName
	 * @param int $ordering
	 * @param bool $treatedAsNew [optional]
	 * @return void
	 * @throws Df_Core_Exception_Client
	 */
	private function throwPleaseFillTheField(
		$fieldName, $profileFieldName, $ordering, $treatedAsNew = false
	) {
		$this->error(
			($treatedAsNew
			? "Система считает Ваш товар новым (не нашла его в своей базе данных по артикулу).\r\n"
			: '')
			. 'Вы должны либо заполнить поле «%s» в строке импортируемых данных №%d, '
			.'либо заполнить поле «%s» в профиле Magento Dataflow.'
			,$fieldName, $ordering, $profileFieldName
		);
	}

	const _CLASS = __CLASS__;
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