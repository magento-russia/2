<?php
namespace Df\C1\Cml2\Import\Data\Entity;
use Df_Catalog_Model_Installer_AttributeSet as Installer;
use Df\C1\Cml2\Import\Data\Collection\ProductPart\Images;
use Df\C1\Cml2\Import\Data\Collection\ProductPart\AttributeValues\Custom as AttributeValuesCustom;
use Df_Eav_Model_Entity_Attribute_Set as AttributeSet;
class Product extends \Df\C1\Cml2\Import\Data\Entity {
	/** @return string */
	public function getAppliedTypeName() {
		/** @var string $result */
		$result = $this->getRequisiteValue('ВидНоменклатуры') ?:
			df_h()->catalog()->product()->getDefaultAttributeSet()->getAttributeSetName();
		df_result_string_not_empty($result);
		return $result;
	}

	/** @return AttributeSet */
	public function getAttributeSet() {return dfc($this, function() {
		/** @var AttributeSet $result */
		$result = df()->registry()->attributeSets()->findByLabel($this->getAppliedTypeName()) ?:
			// Добавляем в систему новый прикладной тип товара
			Installer::create($this->getAppliedTypeName(),
				// Избегаем автроматической перестройки денормализованной таблицы,
				// потому что ниже мы добавим к прикладному типу товара новое свойство
				// (внешний идентификатор 1C), и перестройку денормализованной таблицы
				// всё равно пришлось бы выполнять заново.
				$skipReindexing = true
			)
		;
		df_assert($result instanceof AttributeSet);
		/**
		 * Прикладной тип товара уже имеется в системе,
		 * однако неизвестно, есть ли у него свойство «внешний идентификатор 1С»
		 * http://magento-forum.ru/topic/3115/
		 */
		// Добавляем к прикладному типу товаров свойство «внешний идентификатор 1С».
		// Все требуемые для такого добавления операции выполняются только при необходимости
		// (свойство добавляется, только если оно ещё не было добавлено ранее)
		df_c1_add_external_id_attribute_to_set($result);
		df_assert(!is_null($this->getAttributeSets()->findByLabel($this->getAppliedTypeName())));
		return $result;
	});}

	/** @return AttributeValuesCustom */
	public function getAttributeValuesCustom() {return dfc($this, function() {return
		AttributeValuesCustom::i($this->e(), $this)
	;});}
	
	/** @return \Df_Catalog_Model_Category[] */
	public function getCategories() {return dfc($this, function() {return
		array_map(function($externalId) {return
			df()->registry()->categories()->findByExternalId($externalId) ?:
				df_error("Товарный раздел не найден в реестре: «{$externalId}».")
		;}, $this->getCategoryExternalIds())
	;});}
	
	/** @return int[] */
	public function getCategoryIds() {return dfc($this, function() {return
		dfa_ids($this->getCategories())
	;});}

	/** @return string */
	public function getDescription() {return dfc($this, function() {return
		$this->leaf('Описание') ?: df_c1_cfg()->product()->description()->getDefault()
	;});}

	/** @return string */
	public function getDescriptionFull() {return $this->getRequisiteValue('ОписаниеВФорматеHTML');}

	/** @return Images */
	public function getImages() {return dfc($this, function() {return Images::i($this->e());});}

	/** @return string */
	public function getNameFull() {return dfc($this, function() {
		/** @var string $result */
		$result = $this->leaf('ПолноеНаименование') ?: (
			$this->getRequisiteValue('Полное наименование') ?: $this->getName()
		);
		df_result_string_not_empty($result);
		return $result;
	});}

	/** @return string|null */
	public function getSku() {return $this->leaf('Артикул');}

	/** @return int|null */
	public function taxClassId() {return dfc($this, function() {
		/** @var float|null $rate */
		$rate = $this->vatRate();
		return is_null($rate) ? null : df_product_tax_class_id($rate);
	});}

	/** @return float */
	public function getWeight() {return dfc($this, function() {return
		df_float($this->getRequisiteValue('Вес'))
	;});}

	/** @return \Df_Dataflow_Model_Registry_Collection_AttributeSets */
	private function getAttributeSets() {return df()->registry()->attributeSets();}
	
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
	private function getCategoryExternalIds() {return dfc($this, function() {return
		// http://stackoverflow.com/a/513054
		array_map('strval', $this->e()->xpath('Группы/Ид'))
	;});}

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
	private function taxes() {return dfc($this, function() {return
		$this->e()->map('СтавкиНалогов/СтавкаНалога', 'Наименование', 'Ставка')
	;});}

	/**
	 * 2015-08-09
	 * На текущий момент для всех стран СНГ
	 * применимые к интернет-магазинам ставки НДС являются натуральными числами,
	 * однако на всякий случай используем для обработки тип float.
	 * @used-by taxClassId()
	 * @return float|null
	 */
	private function vatRate() {return dfc($this, function() {
		/** @var string|null $result */
		$resultS = dfa($this->taxes(), 'НДС');
		return is_null($resultS) ? null : df_float_positive0($resultS);
	});}
}