<?php
abstract class Df_1C_Cml2_Import_Data_Entity_AttributeValue
	extends Df_1C_Cml2_Import_Data_Entity {
	/**
	 * 2015-02-06
	 * @used-by Df_1C_Cml2_Import_Processor_Product_Type::getProductDataNewOrUpdateAttributeValues()
	 * Метод @used-by Df_Dataflow_Model_Import_Abstract_Row::getFieldValue()
	 * проверяет принадлежность результата @see getValueForDataflow()
	 * одному из типов: string|int|float|bool|null
	 * @return string|int|float|bool|null
	 */
	abstract public function getValueForDataflow();
	/** @return bool */
	abstract public function isValidForImport();
	/** @return Df_Catalog_Model_Resource_Eav_Attribute|null */
	abstract protected function findMagentoAttributeInRegistry();
	/** @return string */
	abstract protected function getAttributeCodeNew();
	/** @return string */
	abstract protected function getAttributeExternalId();
	/** @return string */
	abstract protected function getAttributeFrontendLabel();
	/** @return Df_1C_Cml2_Import_Data_Entity_Attribute */
	abstract protected function getAttributeTemplate();
	/** @return Df_1C_Cml2_Import_Data_Entity_Product */
	abstract protected function getProduct();

	/**
	 * Добавил к названию метода окончание «Magento»,
	 * чтобы избежать конфликта с родительским методом
	 * Df_Core_Xml_Parser_Entity::getAttribute()
	 * @return Df_Catalog_Model_Resource_Eav_Attribute
	 */
	public function getAttributeMagento() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Catalog_Model_Resource_Eav_Attribute|null $result */
			$result = $this->findMagentoAttributeInRegistry();
			if (!$result) {
				// Вот здесь-то мы можем добавить в Magento нестандартные свойства товаров,
				// учёт которых ведётся в 1С:Управление торговлей.
				$result = $this->createMagentoAttribute();
				rm_1c_log('Создано свойство «%s».', $result->getName());
				rm_attributes()->addEntity($result);
			}
			df_assert($result instanceof Df_Catalog_Model_Resource_Eav_Attribute);
			// Мало, чтобы свойство присутствовало в системе:
			// надо добавить его к прикладному типу товара.
			rm_1c()->create1CAttributeGroupIfNeeded($this->getProduct()->getAttributeSet()->getId());
			/** @var string $status */
			$status = Df_Catalog_Model_Installer_AddAttributeToSet::p(
				$result->getAttributeCode()
				,$this->getProduct()->getAttributeSet()->getId()
				,$this->getGroupForAttribute($result)
			);
			switch ($status) {
				case Df_Catalog_Model_Resource_Installer_Attribute::ADD_ATTRIBUTE_TO_SET__ADDED:
					rm_1c_log(
						'К типу «%s» добавлено свойство «%s».'
						,$this->getProduct()->getAttributeSet()->getAttributeSetName()
						,$result->getName()
					);
					break;
				case Df_Catalog_Model_Resource_Installer_Attribute::ADD_ATTRIBUTE_TO_SET__CHANGED_GROUP:
					rm_1c_log(
						'В типе «%s» свойство «%s» сменило группу на «%s».'
						,$this->getProduct()->getAttributeSet()->getAttributeSetName()
						,$result->getName()
						,$this->getGroupForAttribute($result)
					);
					break;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string|null */
	public function getAttributeName() {return $this->getAttributeMagento()->getName();}

	/**
	 * @override
	 * @return Df_Catalog_Model_Resource_Eav_Attribute
	 */
	protected function createMagentoAttribute() {
		/** @var Df_Catalog_Model_Resource_Eav_Attribute $result */
		$result = rm_attributes()->createOrUpdate($this->getCreationParams());
		df_assert($result->_getData(Df_1C_Const::ENTITY_EXTERNAL_ID));
		rm_1c_log('Добавлено свойство «%s».', $this->getAttributeFrontendLabel());
		return $result;
	}

	/** @return array(string => string|int) */
	protected function getCreationParamsCustom() {return array();}

	/**
	 * @param Df_Catalog_Model_Resource_Eav_Attribute $attribute
	 * @return string
	 */
	protected function getGroupForAttribute(Df_Catalog_Model_Resource_Eav_Attribute $attribute) {
		return Df_1C_Const::PRODUCT_ATTRIBUTE_GROUP_NAME;
	}

	/** @return array(string => string|int) */
	private function getCreationParams() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * Обратите внимание, что переменная $template нужна не только для сокращения кода,
			 * чтобы вместо $this->getAttributeTemplate()->getBackendModel()
			 * писать $template->getBackendModel(),
			 * но и выполняет функцию кэша,
			 * потому что @see getAttributeTemplate() не обязана кэшировать результат.
			 * @see Df_1C_Cml2_Import_Data_Entity_AttributeValue_Barcode::getAttributeTemplate()
			 * @var Df_1C_Cml2_Import_Data_Entity_Attribute_Date $template
			 */
			$template = $this->getAttributeTemplate();
			$this->{__METHOD__} = array_merge(array(
				'entity_type_id' => rm_eav_id_product()
				,'attribute_code' => $this->getAttributeCodeNew()
				/**
				 * В Magento CE 1.4, если поле «attribute_model» присутствует,
				 * то его значение не может быть пустым
				 * @see Mage_Eav_Model_Config::_createAttribute()
				 */
				,'backend_model' => $template->getBackendModel()
				,'backend_type' => $template->getBackendType()
				,'backend_table' => null
				,'frontend_model' => null
				,'frontend_input' => $template->getFrontendInput()
				,'frontend_label' => $this->getAttributeFrontendLabel()
				,'frontend_class' => null
				,'source_model' => $template->getSourceModel()
				,'is_required' => 0
				,'is_user_defined' => 1
				,'default_value' => null
				,'is_unique' => 0
				// В Magento CE 1.4 значением поля «note» не может быть null
				,'note' => ''
				,'frontend_input_renderer' => null
				,'is_global' => 1
				,'is_visible' => 1
				,'is_searchable' => $this->isAttributeVisibleOnFront()
				,'is_filterable' => $this->isAttributeVisibleOnFront()
				,'is_comparable' => $this->isAttributeVisibleOnFront()
				,'is_visible_on_front' => $this->isAttributeVisibleOnFront()
				,'is_html_allowed_on_front' => 0
				,'is_used_for_price_rules' => 0
				,'is_filterable_in_search' => $this->isAttributeVisibleOnFront()
				,'used_in_product_listing' => 0
				,'used_for_sort_by' => 0
				,'is_configurable' => 0
				,'is_visible_in_advanced_search' => $this->isAttributeVisibleOnFront()
				,'position' => 0
				,'is_wysiwyg_enabled' => 0
				,'is_used_for_promo_rules' => 0
				,Df_1C_Const::ENTITY_EXTERNAL_ID => $this->getAttributeExternalId()
			), $this->getCreationParamsCustom());
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	protected function isAttributeVisibleOnFront() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_01(rm_1c_cfg()->product()->other()->showAttributesOnProductPage());
		}
		return $this->{__METHOD__};
	}
}