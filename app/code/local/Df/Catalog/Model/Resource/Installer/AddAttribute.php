<?php
class Df_Catalog_Model_Resource_Installer_AddAttribute extends Df_Catalog_Model_Resource_Installer_Attribute {
	/**
	 * Этот метод отличается от родительского метода
	 * Mage_Eav_Model_Entity_Setup::addAttribute тем,
	 * что не привязывает свойство ни к одному прикладному типу товаров
	 * @param string $attributeCode
	 * @param array $attributeData
	 * @return Df_Catalog_Model_Resource_Eav_Attribute
	 */
	public function addAttributeRm($attributeCode, array $attributeData) {
		df_param_string($attributeCode, 0);
		$attributeData =
			array_merge(
				array(
					'entity_type_id' => rm_eav_id_product()
					,'attribute_code' => $attributeCode
					,'note' => ''
				)
				,$attributeData
			)
		;
		// В Magento CE 1.4 значением поля «note» не может быть null
		$attributeData['note'] = df_nts(df_a($attributeData, 'note'));
		/**
		 * В Magento CE 1.4, если поле «attribute_model» присутствует,
		 * то его значение не может быть пустым
		 * @see Mage_Eav_Model_Config::_createAttribute
		 */
		if (!df_a($attributeData, Df_Eav_Model_Entity_Attribute::P__ATTRIBUTE_MODEL)) {
			unset($attributeData[Df_Eav_Model_Entity_Attribute::P__ATTRIBUTE_MODEL]);
		}
		/**
		 * Метод _validateAttributeData отсутствует в Magento 1.4.0.1.
		 * Обратите внимание, что проверять наличие метода _validateAttributeData
		 * надо посредством method_exists, а не is_callable, по двум причинам:
		 * 1) метод _validateAttributeData — защищённый
		 * (is_callable для защищённых методов не работает и всегда возвращает false)
		 * 2) Наличие Varien_Object::__call
		 * приводит к тому, что is_callable всегда возвращает true
		 */
		if (method_exists($this, '_validateAttributeData')) {
			$this->_validateAttributeData($attributeData);
		}
		/** @var int|null $sortOrder */
		$sortOrder = df_a($attributeData, 'sort_order');
		$attributeId = $this->getAttribute(rm_eav_id_product(), $attributeCode, 'attribute_id');
		if (!$attributeId) {
			$this->_insertAttribute($attributeData);
			rm_eav_reset();
		}
		else {
			$this->updateAttribute(
				rm_eav_id_product()
				,$attributeId
				,$attributeData
				,$value = null
				,$sortOrder
			);
		}
		/** @var array|null $options */
		$options = df_a($attributeData, 'option');
		if (!is_null($options)) {
			df_assert_array($options);
			$options['attribute_id'] = $this->getAttributeId(rm_eav_id_product(), $attributeCode);
			$this->addAttributeOption($options);
		}
		df_h()->catalog()->product()->getResource()->loadAllAttributes();
		/** @var Df_Catalog_Model_Resource_Eav_Attribute $result */
		$result = Df_Catalog_Model_Resource_Eav_Attribute::i();
		$result->loadByCode(rm_eav_id_product(), $attributeCode);
		return $result;
	}

	/**
	 * @override
	 * @param array $attr
	 * @return array
	 */
	protected function _prepareValues($attr) {return $attr;}

	const _CLASS = __CLASS__;

	/**
	 * @static
	 * @return Df_Catalog_Model_Resource_Installer_AddAttribute
	 */
	public static function s() {
		/** @var Df_Catalog_Model_Resource_Installer_AddAttribute $result */
		static $result;
		if (!isset($result)) {
			$result = new Df_Catalog_Model_Resource_Installer_AddAttribute('df_catalog_setup');
		}
		return $result;
	}
}