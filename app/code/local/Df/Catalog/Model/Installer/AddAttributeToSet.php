<?php
class Df_Catalog_Model_Installer_AddAttributeToSet extends Df_Core_Model {
	/**
	 * Метод возвращает статус операции.
	 * @see Df_Catalog_Model_Resource_Installer_Attribute::addAttributeToSetRm
	 * @return string
	 */
	public function process() {
		// Этот метод добавляет группу только по необходимости (при её отсутствии)
		df_h()->catalog()->product()->addGroupToAttributeSetIfNeeded(
			$this->getSetId(), $this->getGroupName()
		);
		$result =
			Df_Catalog_Model_Resource_Installer_Attribute::s()->addAttributeToSetRm(
				rm_eav_id_product()
				,$this->getSetId()
				,$this->getGroupName()
				,$this->getAttributeCode()
				,$this->getOrdering()
			)
		;
		df_result_string($result);
		return $result;
	}

	/** @return string */
	private function getAttributeCode() {return $this->cfg(self::P__ATTRIBUTE_CODE);}

	/** @return string */
	private function getGroupName() {
		return $this->cfg(self::P__GROUP_NAME, self::GROUP_NAME__GENERAL);
	}

	/** @return int|null */
	private function getOrdering() {return $this->cfg(self::P__ORDERING);}

	/** @return int */
	private function getSetId() {return $this->cfg(self::P__SET_ID);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__GROUP_NAME, self::V_STRING_NE, false)
			->_prop(self::P__ATTRIBUTE_CODE, self::V_STRING_NE)
			->_prop(self::P__SET_ID, self::V_INT)
			->_prop(self::P__ORDERING, self::V_INT, false)
		;
	}

	const _CLASS = __CLASS__;
	const GROUP_NAME__GENERAL = 'General';
	const P__ATTRIBUTE_CODE = 'attribute_code';
	const P__ORDERING = 'ordering';
	const P__SET_ID = 'set_id';
	const P__GROUP_NAME = 'group_name';

	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Catalog_Model_Installer_AddAttributeToSet
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * Метод возвращает статус операции.
	 * @see Df_Catalog_Model_Resource_Installer_Attribute::addAttributeToSetRm
	 *
	 * @param string $attributeCode
	 * @param int $setId
	 * @param string $groupName [optional]
	 * @param int $ordering [optional]
	 * @return string
	 */
	public static function processStatic(
		$attributeCode, $setId, $groupName = self::GROUP_NAME__GENERAL, $ordering = null
	) {
		/** @var string $result */
		$result = Df_Catalog_Model_Resource_Installer_Attribute::ADD_ATTRIBUTE_TO_SET__NOT_CHANGED;
		/** @var mixed[] $cache */
		static $cache = array();
		if (!isset($cache[$setId][$attributeCode][$groupName])) {
			df_param_string($attributeCode, 0);
			df_param_integer($setId, 1);
			if (!is_null($ordering)) {
				df_param_integer($ordering, 3);
			}
			$result =
				self::i(array(
					self::P__GROUP_NAME => $groupName
					,self::P__ATTRIBUTE_CODE => $attributeCode
					,self::P__SET_ID => $setId
					,self::P__ORDERING => $ordering
				))->process()
			;
			$cache[$setId][$attributeCode][$groupName] = true;
		}
		return $result;
	}
}