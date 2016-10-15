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
		$attributeData = array_merge(array(
			'entity_type_id' => df_eav_id_product()
			,'attribute_code' => $attributeCode
			,'note' => ''
		), $attributeData);
		// В Magento CE 1.4 значением поля «note» не может быть null
		$attributeData['note'] = df_nts(dfa($attributeData, 'note'));
		/**
		 * В Magento CE 1.4, если поле «attribute_model» присутствует,
		 * то его значение не может быть пустым
		 * @see Mage_Eav_Model_Config::_createAttribute()
		 */
		if (!dfa($attributeData, Df_Eav_Model_Entity_Attribute::P__ATTRIBUTE_MODEL)) {
			unset($attributeData[Df_Eav_Model_Entity_Attribute::P__ATTRIBUTE_MODEL]);
		}
		/**
		 * Метод @uses _validateAttributeData() отсутствует в Magento 1.4.0.1.
		 * Обратите внимание, что проверять наличие метода @uses _validateAttributeData()
		 * надо посредством @uses method_exists, а не @uses is_callable(), по двум причинам:
		 * 1) метод @uses _validateAttributeData() — защищённый
		 * (@see is_callable() для защищённых методов не работает и всегда возвращает false)
		 * 2) Наличие @see Varien_Object::__call()
		 * приводит к тому, что @see is_callable() всегда возвращает true
		 */
		if (method_exists($this, '_validateAttributeData')) {
			$this->_validateAttributeData($attributeData);
		}
		/** @var int|null $sortOrder */
		$sortOrder = dfa($attributeData, 'sort_order');
		$attributeId = $this->getAttribute(df_eav_id_product(), $attributeCode, 'attribute_id');
		if (!$attributeId) {
			$this->_insertAttribute($attributeData);
			df_eav_reset();
		}
		else {
			$this->updateAttribute(
				df_eav_id_product()
				,$attributeId
				,$attributeData
				,$value = null
				,$sortOrder
			);
		}
		/** @var array|null $options */
		$options = dfa($attributeData, 'option');
		if (!is_null($options)) {
			df_assert_array($options);
			$options['attribute_id'] = $this->getAttributeId(df_eav_id_product(), $attributeCode);
			$this->addAttributeOption($options);
		}
		df_h()->catalog()->product()->getResource()->loadAllAttributes();
		/** @var Df_Catalog_Model_Resource_Eav_Attribute $result */
		$result = Df_Catalog_Model_Resource_Eav_Attribute::i();
		$result->loadByCode(df_eav_id_product(), $attributeCode);
		return $result;
	}

	/**
	 * @override
	 * @param array $attr
	 * @return array
	 */
	protected function _prepareValues($attr) {return $attr;}

	/**
	 * @static
	 * @return Df_Catalog_Model_Resource_Installer_AddAttribute
	 */
	public static function s() {static $r; return $r ? $r : $r = new self('df_catalog_setup');}
}