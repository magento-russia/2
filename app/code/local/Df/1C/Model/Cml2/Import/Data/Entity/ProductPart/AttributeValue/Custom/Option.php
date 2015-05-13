<?php
class Df_1C_Model_Cml2_Import_Data_Entity_ProductPart_AttributeValue_Custom_Option
	extends Df_1C_Model_Cml2_Import_Data_Entity_ProductPart_AttributeValue_Custom {
	/**
	 * @override
	 * @return string
	 */
	public function getExternalId() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|null $result */
			$result = $this->getEntityParam('ИдЗначения');
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
				$value = $this->getEntityParam('Значение');
				if (df_h()->_1c()->cml2()->isExternalId($value)) {
					$result = $value;
				}
			}
			df_result_string_not_empty($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	public function getValue() {return !$this->getOption() ? '' : $this->getOption()->getId();}

	/**
	 * @override
	 * @return string
	 */
	public function getValueForDataflow() {
		return !$this->getOption() ? '' : $this->getOption()->getData('value');
	}

	/** @return Mage_Eav_Model_Entity_Attribute_Option|null */
	private function getOption() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Eav_Model_Entity_Attribute_Option|null $result */
			$result = null;
			df_assert(!is_null($this->getExternalId()));
			/** @var Df_Eav_Model_Resource_Entity_Attribute_Option_Collection $options */
			$options = Df_Eav_Model_Resource_Entity_Attribute_Option_Collection::i();
			$options->setPositionOrder('asc');
			$options->setAttributeFilter($this->getAttributeMagento()->getId());
			$options->setStoreFilter($this->getAttributeMagento()->getDataUsingMethod('store_id'));
			$options->addFieldToFilter(Df_Eav_Const::ENTITY_EXTERNAL_ID, $this->getExternalId());
			if (!count($options)) {
				/**
				 * Из 1С:Управление торговлей в интернет-магазин передано справочное значение,
				 * отсутствующее в соответствующем справочнике интернет-магазина.
				 */
				rm_1c_log(strtr(
					"Из «1С:Управление торговлей» в интернет-магазин передано "
					."значение «%value%» свойства «%attributeName%» («%attributeLabel%») "
					."для товара %productName% («%productSku%»), "
					."однако это значение не является допустимым "
					."для свойства «%attributeName%» («%attributeLabel%»).\n "
					."Такое могло произойти по причине наличия "
					."в «1С:Управление торговлей» нескольких одинаковых (дублирующих друг друга) "
					."значений для свойства «%attributeLabel%»."
					,array(
						'%value%' => $this->getExternalId()
						,'%attributeName%' => $this->getAttributeMagento()->getName()
						,'%attributeLabel%' =>
							$this->getAttributeMagento()->getDataUsingMethod('frontend_label')
						,'%productName%' => $this->getProduct()->getName()
						,'%productSku%' => $this->getProduct()->getSku()
					)
				));
				/** @var Df_Eav_Model_Resource_Entity_Attribute_Option_Collection $optionsAll */
				$optionsAll = Df_Eav_Model_Resource_Entity_Attribute_Option_Collection::i();
				$optionsAll->setPositionOrder('asc');
				$optionsAll->setAttributeFilter($this->getAttributeMagento()->getId());
				$optionsAll->setStoreFilter($this->getAttributeMagento()->getDataUsingMethod('store_id'));
				rm_1c_log(strtr(
					'Допустимые значения свойства «%attributeName%» («%attributeLabel%»)'
					,array(
						'%attributeName%' => $this->getAttributeMagento()->getName()
						,'%attributeLabel%' =>
							$this->getAttributeMagento()->getDataUsingMethod('frontend_label')
					)
				));
				foreach ($optionsAll as $option) {
					/** @var Mage_Eav_Model_Entity_Attribute_Option $option */
					rm_1c_log(
						strtr(
							'«%optionLabel%» («%optionExternalId%»)'
							,array(
								'%optionLabel%' => $option->getData('value')
								,'%optionExternalId%' => $option->getData(Df_Eav_Const::ENTITY_EXTERNAL_ID)
							)
						)
					);
				}
			}
			else {
				$result = $options->fetchItem();
			}
			$this->{__METHOD__} = rm_n_set($result);
			if ($result) {
				df_assert($result instanceof Mage_Eav_Model_Entity_Attribute_Option);
			}
		}
		return rm_n_get($this->{__METHOD__});
	}

	/**
	 * Используется из
	 * @see Df_1C_Model_Cml2_Import_Data_Collection_ProductPart_AttributeValues_Custom::createItemFromSimpleXmlElement()
	 */
	const _CLASS = __CLASS__;
}