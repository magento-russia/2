<?php
/**
 * @method Df_1C_Model_Cml2_Import_Data_Entity_Attribute_ReferenceList getEntity()
 */
class Df_1C_Model_Cml2_Import_Processor_ReferenceList extends Df_1C_Model_Cml2_Import_Processor {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		// Ищем справочник с данным идентификатором
		/**
		 * Обратите внимание, что класс — именно этот даже в Magento 1.4.0.1
		 * @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute
		 */
		$attribute =
			df()->registry()->attributes()->findByExternalId($this->getEntity()->getExternalId())
		;
		/** @var mixed[] $attributeData */
		if ($attribute) {
			self::removeDuplicateOptionsWithTheSameExternalId($attribute);
			$attributeData = array_merge($attribute->getData(), array(
				'frontend_label' => $this->getEntity()->getName()
				,'option' =>
					Df_Eav_Model_Entity_Attribute_Option_Calculator::calculateStatic(
						$attribute
						,df_a($this->getEntity()->getOptionsInMagentoFormat(), 'value')
					)
			));
			rm_1c_log('Обновление справочника «%s».', $this->getEntity()->getName());
		}
		else {
			/**
			 * Для некоторых свойств товара можно не создавать объекты-свойства,
			 * а использовать объекты-свойства из стандартной комплектации Magento
			 * @var string|null $standardCode
			 */
			$standardCode =
				df_a(
					$this->getMapFromExternalNameToStandardAttributeCode()
					,$this->getEntity()->getName()
				)
			;
			if ($standardCode) {
				// Убеждаемся, что стандартное свойство не удалено
				/** @var Df_Catalog_Model_Resource_Eav_Attribute $attribute */
				$attribute = Df_Catalog_Model_Resource_Eav_Attribute::i();
				$attribute->loadByCode(rm_eav_id_product(), $standardCode);
				if (!$attribute->getId()) {
					$attribute = null;
				}
				else if ($attribute->getData(Df_Eav_Const::ENTITY_EXTERNAL_ID)) {
					// Стандартное свойство Magento уже привязано к другому свойству из 1С
					$attribute = null;
				}
			}
			if ($attribute) {
				// Используем объект-свойство из стандартной комплектации
				$attributeData =
					array_merge(
						$attribute->getData()
						,array(
							Df_Eav_Const::ENTITY_EXTERNAL_ID => $this->getEntity()->getExternalId()
							,'option' =>
								Df_Eav_Model_Entity_Attribute_Option_Calculator
									::calculateStatic(
										$attribute
										,df_a($this->getEntity()->getOptionsInMagentoFormat(), 'value')
									)
						)
					)
				;
				rm_1c_log('Обновление справочника «%s».', $this->getEntity()->getName());
			}
			else {
				$attributeData =
					array(
						'entity_type_id' => rm_eav_id_product()
						,'attribute_code' => df_h()->_1c()->generateAttributeCode($this->getEntity()->getName())
						/**
						 * В Magento CE 1.4, если поле «attribute_model» присутствует,
						 * то его значение не может быть пустым
						 * @see Mage_Eav_Model_Config::_createAttribute
						 */
						,'backend_model' => $this->getEntity()->getBackendModel()
						,'backend_type' => $this->getEntity()->getBackendType()
						,'backend_table' => null
						,'frontend_model' => null
						,'frontend_input' => $this->getEntity()->getFrontendInput()
						,'frontend_label' => $this->getEntity()->getName()
						,'frontend_class' => null
						,'source_model' => $this->getEntity()->getSourceModel()
						,'is_required' => 0
						,'is_user_defined' => 1
						,'default_value' => null
						,'is_unique' => 0
						,/**
						 * В Magento CE 1.4 значением поля «note» не может быть null
						 */
						'note' => ''
						,'frontend_input_renderer' => null
						,'is_global' => 1
						,'is_visible' => 1
						,'is_searchable' => 1
						,'is_filterable' => 1
						,'is_comparable' => 1
						,'is_visible_on_front' =>
							rm_01(df_cfg()->_1c()->product()->other()->showAttributesOnProductPage())
						,'is_html_allowed_on_front' => 0
						,'is_used_for_price_rules' => 0
						,'is_filterable_in_search' => 1
						,'used_in_product_listing' => 0
						,'used_for_sort_by' => 0
						,'is_configurable' => 1
	//					,'apply_to' =>
	//						array(
	//							'simple'
	//							, 'grouped'
	//							, 'configurable'
	//							, 'virtual'
	//							, 'bundle'
	//							, 'downloadable'
	//						)
						,'is_visible_in_advanced_search' => 1
						,'position' => 0
						,'is_wysiwyg_enabled' => 0
						,'is_used_for_promo_rules' => 0
						,Df_Eav_Const::ENTITY_EXTERNAL_ID => $this->getEntity()->getExternalId()
						,'option' => $this->getEntity()->getOptionsInMagentoFormat()
					)
				;
				rm_1c_log('Создание справочника «%s».', $this->getEntity()->getName());
			}
		}
		/** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
		$attribute =
			df()->registry()->attributes()->findByCodeOrCreate(
				df_a($attributeData, 'attribute_code'), $attributeData
			)
		;
		if (!$attribute->getData(Df_Eav_Const::ENTITY_EXTERNAL_ID)) {
			df_error(
				'У свойства «%s» в данной точке программы'
				.' должен присутствовать внешний идентификатор.'
				,$attribute->getAttributeCode()
			);
		}
		// Назначаем справочным значениям идентификаторы из 1С
		$this->assignExternalIdToOptions($attribute);
		df_assert(
			!!df()->registry()->attributes()->findByExternalId(
				$attribute->getData(Df_Eav_Const::ENTITY_EXTERNAL_ID)
			)
		);
	}

	/**
	 * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
	 * @return void
	 */
	private function assignExternalIdToOptions(Mage_Catalog_Model_Resource_Eav_Attribute $attribute) {
		/** @var Df_Eav_Model_Resource_Entity_Attribute_Option_Collection $options */
		$options = Df_Eav_Model_Resource_Entity_Attribute_Option_Collection::i();
		$options->setPositionOrder('asc');
		$options->setAttributeFilter($attribute->getId());
		$options->setStoreFilter($attribute->getStoreId());
		/** @var string[] $alreadyAssigned */
		$alreadyAssigned = array();
		foreach ($options as $option) {
			/** @var Mage_Eav_Model_Entity_Attribute_Option $option */
			if (!$option->getData(Df_Eav_Const::ENTITY_EXTERNAL_ID)) {
				// Обратите внимание, что 1С:Управление торговлей
				// допускает сразу несколько одноимённых значений
				/** @var Df_1C_Model_Cml2_Import_Data_Entity_ReferenceListPart_Item[] $importedOption */
				$importedOptions = $this->getEntity()->getItems()->findByNameAll($option->getData('value'));
				/**
				 * Мы могли не найти внешний идентификатор опции,
				 * если опция была добавлена администратором вручную
				 */
				if ($importedOptions) {
					/** @var Df_1C_Model_Cml2_Import_Data_Entity_ReferenceListPart_Item|null $importedOption */
					$importedOption = null;
					foreach ($importedOptions as $importedOptionCurrent) {
						/** @var Df_1C_Model_Cml2_Import_Data_Entity_ReferenceListPart_Item $importedOptionCurrent */
						if (!in_array($importedOptionCurrent->getExternalId(), $alreadyAssigned)) {
							$importedOption = $importedOptionCurrent;
							$alreadyAssigned[]= $importedOptionCurrent->getExternalId();
							break;
						}
					}
					if ($importedOption) {
						df_assert($importedOption instanceof Df_1C_Model_Cml2_Import_Data_Entity_ReferenceListPart_Item);
						$option->setData(Df_Eav_Const::ENTITY_EXTERNAL_ID, $importedOption->getExternalId());
						$option->save();
					}
				}
			}
		}
	}

	/**
	 * Для некоторых свойств товара можно не создавать объекты-свойства,
	 * а использовать объекты-свойства из стандартной комплектации Magento
	 * @return string[]
	 */
	private function getMapFromExternalNameToStandardAttributeCode() {
		return array('Производитель' => 'manufacturer');
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(
			self::P__ENTITY, Df_1C_Model_Cml2_Import_Data_Entity_Attribute_ReferenceList::_CLASS
		);
	}
	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param Df_1C_Model_Cml2_Import_Data_Entity_Attribute_ReferenceList $refList
	 * @return Df_1C_Model_Cml2_Import_Processor_ReferenceList
	 */
	public static function i(Df_1C_Model_Cml2_Import_Data_Entity_Attribute_ReferenceList $refList) {
		return new self(array(self::P__ENTITY => $refList));
	}

	/**
	 * @static
	 * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
	 * @return void
	 */
	public static function removeDuplicateOptionsWithTheSameExternalId(
		Mage_Catalog_Model_Resource_Eav_Attribute $attribute
	) {
		/** @var Df_Eav_Model_Resource_Entity_Attribute_Option_Collection $options */
		$options = Df_Eav_Model_Resource_Entity_Attribute_Option_Collection::i();
		$options->setPositionOrder('asc');
		$options->setAttributeFilter($attribute->getId());
		$options->setStoreFilter($attribute->getStoreId());
		$options->addFieldToSelect(Df_Eav_Const::ENTITY_EXTERNAL_ID);
		/** @var string[] $alreadyAssigned */
		$alreadyProcessed = array();
		foreach ($options as $option) {
			/** @var Mage_Eav_Model_Entity_Attribute_Option $option */
			/** @var string|null $externalId */
		    $externalId = $option->getData(Df_Eav_Const::ENTITY_EXTERNAL_ID);
			if (!is_null($externalId)) {
				if (in_array($externalId, $alreadyProcessed)) {
					$option->delete();
				}
				else {
					$alreadyProcessed[] = $externalId;
				}
			}
		}
	}
}