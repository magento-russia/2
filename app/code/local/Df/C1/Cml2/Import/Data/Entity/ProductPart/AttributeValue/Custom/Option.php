<?php
class Df_C1_Cml2_Import_Data_Entity_ProductPart_AttributeValue_Custom_Option
	extends Df_C1_Cml2_Import_Data_Entity_ProductPart_AttributeValue_Custom {
	/**
	 * @override
	 * @return string
	 */
	public function getExternalId() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|null $result */
			$result = $this->leaf('ИдЗначения');
			if (!$result) {
				/**
				 * В магазине sb-s.com.ua встречается такая конструкция:
				 *
		 			<ЗначенияСвойства>
						<Ид>6cc37c6d-7d15-11df-901f-00e04c595000</Ид>
						<Значение>6cc37c6e-7d15-11df-901f-00e04c595000</Значение>
					</ЗначенияСвойства>
				 */
				/** @var string|null $value */
				$value = $this->leaf('Значение');
				if (df_1c_is_external_id($value)) {
					$result = $value;
				}
			}
			df_result_string_not_empty($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-02-06
	 * @used-by getValueForObject()
	 * Обратите внимание на отличие метода @see getValue() от метода @see getValueForDataflow()
	 * Опция имеет следующую структуру данных:
		  array(
				[option_id] => 35
				[attribute_id] => 148
				[sort_order] => 2
				[df_1c_id] => 14ed8b52-55bd-11d9-848a-00112f43529a
				[default_value] => натуральная кожа
				[store_default_value] =>
				[value] => натуральная кожа
			 )
	 * Для приведённой выше структуры данных
	 * @see getValue() и @used-by getValueForObject() вернут значение «35»,
	 * а @see getValueForDataflow() вернёт значение «натуральная кожа».
	 * @override
	 * @return string|int
	 */
	public function getValue() {return !$this->getOption() ? '' : $this->getOption()->getId();}

	/**
	 * 2015-02-06
	 * @used-by Df_C1_Cml2_Import_Processor_Product_Type::getProductDataNewOrUpdateAttributeValues()
	 * Метод @used-by Df_Dataflow_Model_Import_Abstract_Row::getFieldValue()
	 * проверяет принадлежность результата @see getValueForDataflow()
	 * одному из типов: string|int|float|bool|null
	 *
	 * Обратите внимание на отличие метода @see getValueForDataflow()
	 * от метода @see getValue()
	 * и, косвенно, от метода @see getValueForObject(), который использует @see getValue().
	 * Опция имеет следующую структуру данных:
		  array(
				[option_id] => 35
				[attribute_id] => 148
				[sort_order] => 2
				[df_1c_id] => 14ed8b52-55bd-11d9-848a-00112f43529a
				[default_value] => натуральная кожа
				[store_default_value] =>
				[value] => натуральная кожа
			 )
	 * Для приведённой выше структуры данных
	 * @see getValue() и @used-by getValueForObject() вернут значение «35»,
	 * а @see getValueForDataflow() вернёт значение «натуральная кожа».
	 *
	 * @override
	 * @return string|int|float|bool|null
	 */
	public function getValueForDataflow() {
		return !$this->getOption() ? '' : $this->getOption()->getData('value');
	}

	/**
	 * 2015-02-06
	 * Опция имеет следующую структуру данных:
		  array(
				[option_id] => 35
				[attribute_id] => 148
				[sort_order] => 2
				[df_1c_id] => 14ed8b52-55bd-11d9-848a-00112f43529a
				[default_value] => натуральная кожа
				[store_default_value] =>
				[value] => натуральная кожа
			 )
	 * @return Df_Eav_Model_Entity_Attribute_Option|null
	 */
	private function getOption() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Eav_Model_Entity_Attribute_Option|null $result */
			$result = null;
			df_assert(!is_null($this->getExternalId()));
			/** @var Df_Eav_Model_Resource_Entity_Attribute_Option_Collection $options */
			$options = Df_Eav_Model_Entity_Attribute_Option::c();
			$options->setPositionOrder('asc');
			$options->setAttributeFilter($this->getAttributeMagento()->getId());
			$options->setStoreFilter($this->getAttributeMagento()->getStoreId());
			$options->addFieldToFilter(Df_C1_Const::ENTITY_EXTERNAL_ID, $this->getExternalId());
			if (!$options->count()) {
				// Из 1С:Управление торговлей в интернет-магазин передано справочное значение,
				// отсутствующее в соответствующем справочнике интернет-магазина.
				df_1c_log(
					"Из «1С:Управление торговлей» в интернет-магазин передано"
					. " значение «{value}» свойства {attribute}"
					. " для товара «{productName}» [{productSku}],"
					. " однако это значение не является допустимым"
					. " для данного свойства."
					. "\nТакое могло произойти по причине наличия"
					. " в «1С:Управление торговлей» нескольких одинаковых (дублирующих друг друга)"
					. " значений этого свойства."
					,array(
						'{value}' => $this->getExternalId()
						,'{attribute}' => $this->getAttributeMagento()->getTitle()
						,'{productName}' => $this->getProduct()->getName()
						,'{productSku}' => $this->getProduct()->getSku()
					)
				);
				/** @var Df_Eav_Model_Resource_Entity_Attribute_Option_Collection $optionsAll */
				$optionsAll = Df_Eav_Model_Entity_Attribute_Option::c();
				$optionsAll->setPositionOrder('asc');
				$optionsAll->setAttributeFilter($this->getAttributeMagento()->getId());
				$optionsAll->setStoreFilter($this->getAttributeMagento()->getStoreId());
				df_1c_log('Допустимые значения свойства %s:', $this->getAttributeMagento()->getTitle());
				foreach ($optionsAll as $option) {
					/** @var Df_Eav_Model_Entity_Attribute_Option $option */
					df_1c_log('«{optionLabel}» («{optionExternalId}»)', array(
						'{optionLabel}' => $option->getValue()
						,'{optionExternalId}' => $option->get1CId()
					));
				}
			}
			else {
				$result = $options->fetchItem();
			}
			$this->{__METHOD__} = df_n_set($result);
			if ($result) {
				df_assert($result instanceof Df_Eav_Model_Entity_Attribute_Option);
			}
		}
		return df_n_get($this->{__METHOD__});
	}

	/** @used-by Df_C1_Cml2_Import_Data_Collection_ProductPart_AttributeValues_Custom::createItem() */

}