<?php
class Df_1C_Model_Cml2_Import_Data_Entity_Product extends Df_1C_Model_Cml2_Import_Data_Entity {
	/** @return string */
	public function getAppliedTypeName() {
		/** @var string $result */
		$result = $this->getRequisiteValue('ВидНоменклатуры');
		if (!$result) {
			$result = df_h()->catalog()->product()->getDefaultAttributeSet()->getAttributeSetName();
		}
		df_result_string_not_empty($result);
		return $result;
	}

	/** @return Mage_Eav_Model_Entity_Attribute_Set */
	public function getAttributeSet() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Eav_Model_Entity_Attribute_Set|null $result */
			$result = df()->registry()->attributeSets()->findByLabel($this->getAppliedTypeName());
			if (!$result) {
				// Добавляем в систему новый прикладной тип товара
				$result =
					Df_Catalog_Model_Installer_AttributeSet::i(array(
						Df_Catalog_Model_Installer_AttributeSet::P__NAME => $this->getAppliedTypeName()
						// Избегаем автроматической перестройки денормализованной таблицы,
						// потому что ниже мы добавим к приклабному типу товара новое свойство
						// (внешний иентификатор 1C), и перестройку денормализованной таблицы
						// всё равно пришлось бы выполнять заново.
						,Df_Catalog_Model_Installer_AttributeSet::P__SKIP_REINDEXING => true
					))->getResult()
				;
				/**
				 * Добавляем к прикладному типу товаров
				 * свойство для учёта внешнего идентификатора товара в 1С:Управление торговлей
				 */
				df_h()->_1c()->cml2()->attributeSet()->addExternalIdToAttributeSet($result->getId());
				$result->setData(self::$ATTRIBUTE_SET_HAS_EXTERNAL_ID, true);
				$this->getAttributeSets()->addEntity($result);
				df_assert(!is_null($this->getAttributeSets()->findByLabel($this->getAppliedTypeName())));
			}
			else {
				/**
				 * Прикладной тип товара уже имеется в системе,
				 * однако неизвестно, есть ли у него свойство «внешний идентификатор 1С»
				 * @link http://magento-forum.ru/topic/3115/
				 */
				if (true !== $result->getData(self::$ATTRIBUTE_SET_HAS_EXTERNAL_ID)) {
					/**
					 * Добавляем свойство "внешний идентификатор 1С",
					 * если оно ещё не добавлено
					 */
					df_h()->_1c()->cml2()->attributeSet()->addExternalIdToAttributeSet($result->getId());
					$result->setData(self::$ATTRIBUTE_SET_HAS_EXTERNAL_ID, true);
					/**
					 * А теперь заново передобавляем прикладной тип в реестр,
					 * чтобы реестр перестроил карту соответствия внешних идентификаторов
					 * прикладым типам
					 */
					$this->getAttributeSets()->addEntity($result);
				}
			}
			df_assert($result instanceof Mage_Eav_Model_Entity_Attribute_Set);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_1C_Model_Cml2_Import_Data_Collection_ProductPart_AttributeValues_Custom */
	public function getAttributeValuesCustom() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_1C_Model_Cml2_Import_Data_Collection_ProductPart_AttributeValues_Custom::i(
					$this->e(), $this
				)
			;
		}
		return $this->{__METHOD__};
	}
	
	/** @return Mage_Catalog_Model_Category[] */
	public function getCategories() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Catalog_Model_Category[] $result  */
			$result = array();
			foreach ($this->getCategoryExternalIds() as $categoryExternalId) {
				/** @var string $categoryExternalId */
				$result[]= $this->getCategoryByExternalId($categoryExternalId);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}
	
	/** @return int[] */
	public function getCategoryIds() {
		if (!isset($this->{__METHOD__})) {
			/** @var int[] $result  */
			$result = array();
			foreach ($this->getCategories() as $category) {
				/** @var Mage_Catalog_Model_Category $category */
				$result[]= rm_nat($category->getId());
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getDescription() {
		return $this->getEntityParam(
			'Описание', df_cfg()->_1c()->product()->description()->getDefault()
		);
	}

	/** @return string */
	public function getDescriptionFull() {return $this->getRequisiteValue('ОписаниеВФорматеHTML');}

	/** @return Df_1C_Model_Cml2_Import_Data_Collection_ProductPart_Images */
	public function getImages() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_1C_Model_Cml2_Import_Data_Collection_ProductPart_Images::i($this->e())
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getNameFull() {
		/** @var string $result */
		$result = $this->getEntityParam('ПолноеНаименование');
		if (!$result) {
			$result = $this->getRequisiteValue('Полное наименование');
		}
		if (!$result) {
			$result = $this->getName();
		}
		df_result_string_not_empty($result);
		return $result;
	}

	/** @return string|null */
	public function getSku() {return $this->getEntityParam('Артикул');}

	/** @return float */
	public function getWeight() {
		/** @var $resultAsString $resultAsString */
		$resultAsString = $this->getRequisiteValue('Вес');
		return !$resultAsString ? 0.0 : rm_float($resultAsString);
	}

	/** @return Df_Dataflow_Model_Registry_Collection_AttributeSets */
	private function getAttributeSets() {return df()->registry()->attributeSets();}
	
	/**                 
	 * @param string $externalId
	 * @return Mage_Catalog_Model_Category
	 */
	private function getCategoryByExternalId($externalId) {
		df_param_string_not_empty($externalId, 0);
		/** @var Mage_Catalog_Model_Category $result */
		$result = df()->registry()->categories()->findByExternalId($externalId);
		if (!$result) {
			df_error(
				'Не могу найти в системе товарный раздел с внешним идентификатором «%s»'
				,$externalId
			);
		}
		return $result;
	}	
	
	/**
	 * Современные (с конца 2012 года) версии
	 * «модуля расширения конфигурации для УТ 10.3.19.4 от 1С-Битрикс»
	 * @link http://1c.1c-bitrix.ru/ecommerce/download.php
	 * способны в настройках обмена данными с веб-сайтом
	 * привязывать товар сразу к нескольким товарным разделам
	 * @link http://1c.1c-bitrix.ru/blog/blog1c/catalog_tree.php
	 * В этом случае привязка в файле обмена будет выглядеть так:
	 *
		<Товар>
			<Группы>
				<Ид>ac7f12c9-4a97-493d-98a8-e8aa6521901b</Ид>
				<Ид>831cb775-8c8f-4f9e-bb21-f3d8b1dd349b</Ид>
				<Ид>38888dc3-bd27-4c2a-bb4d-156ee4dfddc4</Ид>
			</Группы>
		</Товар>
	 *
	 * @return string[]
	 */
	private function getCategoryExternalIds() {
		if (!isset($this->{__METHOD__})) {
			/** @var SimpleXMLElement[] $nodes */
			$nodes = $this->e()->xpath('Группы/Ид');
			/** @var string[] $result */
			$result = array();
			foreach ($nodes as $node) {
				/** @var SimpleXMLElement $node */
				$result[]= (string)$node;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** Используется из @see Df_1C_Model_Cml2_Import_Data_Collection_Products::getItemClass() */
	const _CLASS = __CLASS__;
	/** @var string */
	private static $ATTRIBUTE_SET_HAS_EXTERNAL_ID = 'attribute_set_has_external_id';
}