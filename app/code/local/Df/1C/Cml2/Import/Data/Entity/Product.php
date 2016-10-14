<?php
class Df_1C_Cml2_Import_Data_Entity_Product extends Df_1C_Cml2_Import_Data_Entity {
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

	/** @return Df_Eav_Model_Entity_Attribute_Set */
	public function getAttributeSet() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Eav_Model_Entity_Attribute_Set $result */
			$result = df()->registry()->attributeSets()->findByLabel($this->getAppliedTypeName());
			if (!$result) {
				// Добавляем в систему новый прикладной тип товара
				$result = Df_Catalog_Model_Installer_AttributeSet::create(
					$this->getAppliedTypeName()
					// Избегаем автроматической перестройки денормализованной таблицы,
					// потому что ниже мы добавим к прикладному типу товара новое свойство
					// (внешний идентификатор 1C), и перестройку денормализованной таблицы
					// всё равно пришлось бы выполнять заново.
					,$skipReindexing = true
				);
			}
			df_assert($result instanceof Df_Eav_Model_Entity_Attribute_Set);
			/**
			 * Прикладной тип товара уже имеется в системе,
			 * однако неизвестно, есть ли у него свойство «внешний идентификатор 1С»
			 * http://magento-forum.ru/topic/3115/
			 */
			// Добавляем к прикладному типу товаров свойство «внешний идентификатор 1С».
			// Все требуемые для такого добавления операции выполняются только при необходимости
			// (свойство добавляется, только если оно ещё не было добавлено ранее)
			rm_1c_add_external_id_attribute_to_set($result);
			df_assert(!is_null($this->getAttributeSets()->findByLabel($this->getAppliedTypeName())));
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_1C_Cml2_Import_Data_Collection_ProductPart_AttributeValues_Custom */
	public function getAttributeValuesCustom() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_1C_Cml2_Import_Data_Collection_ProductPart_AttributeValues_Custom::i(
					$this->e(), $this
				)
			;
		}
		return $this->{__METHOD__};
	}
	
	/** @return Df_Catalog_Model_Category[] */
	public function getCategories() {
		if (!isset($this->{__METHOD__})) {
			/** @uses getCategoryByExternalId() */
			$this->{__METHOD__} =
				array_map(array($this, 'getCategoryByExternalId'), $this->getCategoryExternalIds())
			;
		}
		return $this->{__METHOD__};
	}
	
	/** @return int[] */
	public function getCategoryIds() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_each($this->getCategories(), 'getId');
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getDescription() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = $this->leaf('Описание');
			if (!$result) {
				$result = rm_1c_cfg()->product()->description()->getDefault();
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getDescriptionFull() {return $this->getRequisiteValue('ОписаниеВФорматеHTML');}

	/** @return Df_1C_Cml2_Import_Data_Collection_ProductPart_Images */
	public function getImages() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_1C_Cml2_Import_Data_Collection_ProductPart_Images::i($this->e());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getNameFull() {
		/** @var string $result */
		$result = $this->leaf('ПолноеНаименование');
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
	public function getSku() {return $this->leaf('Артикул');}

	/** @return int|null */
	public function taxClassId() {
		if (!isset($this->{__METHOD__})) {
			/** @var float|null $rate */
			$rate = $this->vatRate();
			$this->{__METHOD__} = rm_n_set(is_null($rate) ? null : rm_product_tax_class_id($rate));
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return float */
	public function getWeight() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_float($this->getRequisiteValue('Вес'));
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Dataflow_Model_Registry_Collection_AttributeSets */
	private function getAttributeSets() {return df()->registry()->attributeSets();}
	
	/**                 
	 * @param string $externalId
	 * @return Df_Catalog_Model_Category
	 */
	private function getCategoryByExternalId($externalId) {
		df_param_string_not_empty($externalId, 0);
		/** @var Df_Catalog_Model_Category $result */
		$result = df()->registry()->categories()->findByExternalId($externalId);
		if (!$result) {
			df_error('Товарный раздел не найден в реестре: «%s».', $externalId);
		}
		return $result;
	}	
	
	/**
	 * Современные (с конца 2012 года) версии
	 * «модуля расширения конфигурации для УТ 10.3.19.4 от 1С-Битрикс»
	 * http://1c.1c-bitrix.ru/ecommerce/download.php
	 * способны в настройках обмена данными с веб-сайтом
	 * привязывать товар сразу к нескольким товарным разделам
	 * http://1c.1c-bitrix.ru/blog/blog1c/catalog_tree.php
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
			/**
			 * @uses strval()
			 * http://stackoverflow.com/a/513054
			 */
			$this->{__METHOD__} = array_map('strval', $this->e()->xpath('Группы/Ид'));
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-08-08
	 * Информация о налогах передаётся в виде структуры:
		<СтавкиНалогов>
			<СтавкаНалога>
				<Наименование>НДС</Наименование>
				<Ставка>10</Ставка>
			</СтавкаНалога>
		</СтавкиНалогов>
	 * @used-by vatRate()
	 * @return array(string => float)
	 */
	private function taxes() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->e()->map('СтавкиНалогов/СтавкаНалога', 'Наименование', 'Ставка');
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-08-09
	 * На текущий момент для всех стран СНГ
	 * применимые к интернет-магазинам ставки НДС являются натуральными числами,
	 * однако на всякий случай используем для обработки тип float.
	 * @used-by taxClassId()
	 * @return float|null
	 */
	private function vatRate() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|null $result */
			$resultS = df_a($this->taxes(), 'НДС');
			$this->{__METHOD__} = rm_n_set(is_null($resultS) ? null : rm_float_positive0($resultS));
		}
		return rm_n_get($this->{__METHOD__});
	}

	/**
	 * @used-by Df_1C_Cml2_Import_Data_Collection_Products::itemClass()
	 * @used-by Df_1C_Cml2_Import_Data_Collection_ProductPart_AttributeValues_Custom::_construct()
	 * @used-by Df_1C_Cml2_Import_Data_Entity_ProductPart_AttributeValue_Custom::_construct()
	 */
	const _C = __CLASS__;
}