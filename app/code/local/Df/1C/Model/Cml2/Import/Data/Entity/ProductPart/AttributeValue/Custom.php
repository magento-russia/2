<?php
class Df_1C_Model_Cml2_Import_Data_Entity_ProductPart_AttributeValue_Custom
	extends Df_1C_Model_Cml2_Import_Data_Entity_AttributeValue {
	/**
	 * @override
	 * @return string
	 */
	public function getAttributeExternalId() {return $this->getEntityParam('Ид');}

	/** @return string */
	public function getAttributeName() {return $this->getAttributeMagento()->getName();}

	/**
	 * @override
	 * @return string
	 */
	public function getExternalId() {
		return implode('::', array($this->getAttributeExternalId(), $this->getValue()));
	}

	/** @return string */
	public function getValue() {return $this->getEntityParam('Значение');}

	/**
	 * @override
	 * @return string
	 */
	public function getValueForDataflow() {
		return $this->getAttributeEntity()->convertValueToMagentoFormat($this->getValue());
	}

	/** @return string */
	public function getValueForObject() {
		return $this->getAttributeEntity()->convertValueToMagentoFormat($this->getValue());
	}

	/**
	 * @override
	 * @return bool
	 */
	public function isValidForImport() {
		return
			// 1C для каждого товара
			// указывает не только значения свойств, относящихся к товару,
			// но и значения свойств, к товару никак не относящихся,
			// при этом значения — пустые, например:
			//
			//	<ЗначенияСвойства>
			//		<Ид>b79b0fe0-c8a5-11e1-a928-4061868fc6eb</Ид>
			//		<Значение/>
			//	</ЗначенияСвойства>
			//
			// Мы не обрабатываем эти свойства, потому что их обработка приведёт к добавлению
			// к прикладному типу товара данного свойства, а нам это не нужно, потому что
			// свойство не имеет отношения к прикладному типу товара.
				$this->getEntityParam('Значение')
			||
				$this->getEntityParam('ИдЗначения')
			// 11 февраля 2014 года заметил,
			// что 1С:Управление торговлей 11.1 при использовании версии 2.05 протокола CommerceML
			// и версии 8.3 платформы 1С:Предприятие
			// при обмене данными с интернет-магазином передаёт информацию о производителе
			// не в виде стандартного атрибута, а иначе:
			//
			//	<КоммерческаяИнформация ВерсияСхемы="2.05" ДатаФормирования="2014-02-11T15:32:13">
			//		(...)
			//		<Каталог СодержитТолькоИзменения="false">
			//			(...)
			//			<Товары>
			//				<Товар>
			//					(...)
			//					<Изготовитель>
			//						<Ид>9bf2b1bf-8e9a-11e3-bd2c-742f68ccd0fb</Ид>
			//						<Наименование>Tecumseh</Наименование>
			//						<ОфициальноеНаименование>Tecumseh</ОфициальноеНаименование>
			//					</Изготовитель>
			//					(...)
			//				</Товар>
			//			</Товары>
			//		</Каталог>
			//	</КоммерческаяИнформация>
			||
				$this->getEntityParam('Наименование')
			||
				$this->isAttributeExistAndBelongToTheProductType()
		;
	}

	/**
	 * @override
	 * @return array(string => string|int)
	 */
	protected function getCreationParamsCustom() {return array('is_configurable' => 1);}

	/**
	 * @override
	 * @return Mage_Catalog_Model_Resource_Eav_Attribute
	 */
	protected function findMagentoAttributeInRegistry() {
		return df()->registry()->attributes()->findByExternalId($this->getAttributeExternalId());
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getAttributeCodeNew() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_h()->_1c()->generateAttributeCode(
					$this->getAttributeEntity()->getName()
					// Намеренно убрал второй параметр ($this->getProduct()->getAppliedTypeName()),
					// потому что счёл ненужным в данном случае
					// использовать приставку для системных имён товарных свойств,
					// потому что приставка (прикладной тип товара),
					// как правило, получается слишком длинной,
					// а название системного имени товарного свойства
					// ограничено 32 символами
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	protected function getAttributeFrontendLabel() {return $this->getAttributeEntity()->getName();}

	/**
	 * @override
	 * @return Df_1C_Model_Cml2_Import_Data_Entity_Attribute
	 */
	protected function getAttributeTemplate() {return $this->getAttributeEntity();}

	/**
	 * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
	 * @return string
	 */
	protected function getGroupForAttribute(Mage_Catalog_Model_Resource_Eav_Attribute $attribute) {
		return Df_1C_Const::PRODUCT_ATTRIBUTE_GROUP_NAME;
	}

	/**
	 * @override
	 * @return Df_1C_Model_Cml2_Import_Data_Entity_Product
	 */
	protected function getProduct() {return $this->cfg(self::P__PRODUCT);}

	/** @return Df_1C_Model_Cml2_Import_Data_Entity_Attribute */
	private function getAttributeEntity() {
		/** @var Df_1C_Model_Cml2_Import_Data_Entity_Attribute $result */
		$result =
			$this->getState()->import()->collections()->getAttributes()->findByExternalId(
				$this->getAttributeExternalId()
			)
		;
		if (is_null($result)) {
			df_error(
				'В реестре отсутствует требуемое свойство с внешним идентификатором «%s»'
				,$this->getAttributeExternalId()
			);
		}
		return $result;
	}

	/** @return bool */
	private function isAttributeExistAndBelongToTheProductType() {
		if (!isset($this->{__METHOD__})) {
			/** @var bool $result */
			$result = false;
			/** @var Mage_Eav_Model_Entity_Attribute|null $attribute */
			$attribute = df()->registry()->attributes()->findByExternalId($this->getAttributeExternalId());
			if ($attribute) {
				df_assert($attribute instanceof Mage_Eav_Model_Entity_Attribute);
				// Смотрим, принадлежит ли свойство типу товара
				/** @var Mage_Eav_Model_Resource_Entity_Attribute_Collection $attributes */
				$attributes = Mage::getResourceModel('eav/entity_attribute_collection');
				$attributes->setEntityTypeFilter(rm_eav_id_product());
				$attributes->addSetInfo();
				$attributes->addFieldToFilter('attribute_code', $attribute->getAttributeCode());
				$attributes->load();
				/** @var Mage_Eav_Model_Entity_Attribute $attributeInfo */
				$attributeInfo = null;
				foreach ($attributes as $attributeInfoCurrent) {
					$attributeInfo = $attributeInfoCurrent;
					break;
				}
				df_assert($attributeInfo instanceof Mage_Eav_Model_Entity_Attribute);
				/** @var mixed[] $setsInfo */
				$setsInfo = $attributeInfo->getData('attribute_set_info');
				df_assert_array($setsInfo);
				$result = in_array($this->getProduct()->getAttributeSet()->getId(), array_keys($setsInfo));
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__PRODUCT, Df_1C_Model_Cml2_Import_Data_Entity_Product::_CLASS);
	}
	/** Используется из @see Df_1C_Model_Cml2_Import_Data_Collection_ProductPart_AttributeValues_Custom::getItemClass() */
	const _CLASS = __CLASS__;
	const P__PRODUCT = 'product';
}