<?php
/**
 * @method Df_Eav_Model_Resource_Entity_Attribute_Set getResource()
 */
class Df_Eav_Model_Entity_Attribute_Set extends Mage_Eav_Model_Entity_Attribute_Set {
	/**
	 * 2015-08-10
	 * Отличия от @see getAttributes():
	 * 1) возвращает только коды и идентификаторы свойств,
	 * поэтому работает значительно быстрее.
	 * 2) работает не только для товарных типов, но и для остальных объектов EAV
	 * (разделов, покупателей т.п.)
	 *
	 * Ключами возвращаемого массива являются идентификаторы свойств.
	 * @return array(int => string)
	 */
	public function attributeCodes() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getResource()->attributeCodes($this->getId());
		}
		return $this->{__METHOD__};
	}

	/**
	 * Используйте только для товарных свойств!
	 * @return array(string => Df_Catalog_Model_Resource_Eav_Attribute)
	 */
	public function getAttributes() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => Df_Catalog_Model_Resource_Eav_Attribute) $result */
			$result = array();
			foreach (rm_attributes() as $attribute) {
				/** @var Df_Catalog_Model_Resource_Eav_Attribute $attribute */
				df_assert($attribute->hasAttributeSetInfo());
				if ($attribute->isInSet($this->getId())) {
					$result[$attribute->getName()] = $attribute;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Добавляет к прикладному типу товаров свойство для учёта внешнего идентификатора товара.
	 * Все требуемые для такого добавления операции выполняются только при необходимости
	 * (свойство добавляется, только если оно ещё не было добавлено ранее).
	 * Внешних идентификаторов у товара может быть несколько:
	 * каждый для синхронизации товара со своей внешней учётной системой.
	 * @param string $code
	 * @param string $label
	 * @param string $groupName
	 * @param int $groupOrdering
	 * @return void
	 */
	public function addExternalIdAttribute($code, $label, $groupName, $groupOrdering) {
		if (!df_a($this->_hasExternalId, $code)) {
			/** @var Df_Catalog_Model_Resource_Eav_Attribute $attribute */
			$attribute = rm_attributes()->createOrUpdate(array(
				'entity_type_id' => rm_eav_id_product()
				,'attribute_code' => $code
				/**
				 * В Magento CE 1.4, если поле «attribute_model» присутствует,
				 * то его значение не может быть пустым
				 * @see Mage_Eav_Model_Config::_createAttribute()
				 */
				,'backend_model' => ''
				,'backend_type' => 'varchar'
				,'backend_table' => null
				,'frontend_model' => null
				,'frontend_input' => 'text'
				,'frontend_label' => $label
				,'frontend_class' => null
				,'source_model' => ''
				,'is_required' => 0
				,'is_user_defined' => 0
				,'default_value' => null
				,'is_unique' => 0
				// В Magento CE 1.4 значением поля «note» не может быть null
				,'note' => ''
				,'frontend_input_renderer' => null
				,'is_global' => 1
				,'is_visible' => 1
				,'is_searchable' => 1
				,'is_filterable' => 1
				,'is_comparable' => 1
				,'is_visible_on_front' => 0
				,'is_html_allowed_on_front' => 0
				,'is_used_for_price_rules' => 0
				,'is_filterable_in_search' => 1
				,/**
				 * Включенность опции «used_in_product_listing»
				 * говорит системе загружать данное товарное свойство
				 * в коллекцию товаров при включенном режиме денормализации товаров.
				 *
				 * Для свойства @see Df_1C_Const::ENTITY_EXTERNAL_ID
				 * эта опция обязательно должна быть включена.
				 */
				'used_in_product_listing' => 1
				,'used_for_sort_by' => 0
				,'is_configurable' => 1
				,'is_visible_in_advanced_search' => 1
				,'position' => 0
				,'is_wysiwyg_enabled' => 0
				,'is_used_for_promo_rules' => 0
			));
			df_h()->catalog()->product()->addGroupToAttributeSetIfNeeded(
				$this->getId(), $groupName, $groupOrdering
			);
			Df_Catalog_Model_Installer_AddAttributeToSet::p(
				$attributeCode = $attribute->getAttributeCode()
				,$this->getId()
				,$groupName = $groupName
				,$ordering = 100
			);
			// Добавляем прикладной тип в реестр.
			// Обратите внимание, что прикладной тип мог уже присутствовать в реестре,
			// и в такой ситуации нам всё равно надо заново его передобавить в реестр,
			// чтобы реестр перестроил карту соответствия внешних идентификаторов
			// прикладым типам.
			df()->registry()->attributeSets()->addEntity($this);
			$this->_hasExternalId[$code] = true;
		}
	}

	/**
	 * @override
	 * @return Df_Eav_Model_Resource_Entity_Attribute_Set_Collection
	 */
	public function getResourceCollection() {return self::c();}

	/**
	 * @override
	 * @return Df_Eav_Model_Resource_Entity_Attribute_Set
	 */
	protected function _getResource() {return Df_Eav_Model_Resource_Entity_Attribute_Set::s();}

	/**
	 * @used-by Df_Dataflow_Model_Registry_Collection_AttributeSets::getEntityClass()
	 * @used-by Df_Eav_Model_Resource_Entity_Attribute_Set_Collection::_construct()
	 * @used-by Df_Localization_Onetime_Dictionary_Rule_Conditions_AttributeSet::getEntityClass()
	 */
	const _C = __CLASS__;
	const P__NAME = 'attribute_set_name';

	/** @var array(string => bool) */
	private $_hasExternalId = array();

	/** @return Df_Eav_Model_Resource_Entity_Attribute_Set_Collection */
	public static function c() {return new Df_Eav_Model_Resource_Entity_Attribute_Set_Collection;}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Eav_Model_Entity_Attribute_Set
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_Eav_Model_Entity_Attribute_Set
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}
}