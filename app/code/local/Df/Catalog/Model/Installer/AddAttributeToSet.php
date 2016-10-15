<?php
class Df_Catalog_Model_Installer_AddAttributeToSet extends Df_Core_Model {
	/**
	 * Метод возвращает статус операции.
	 * @used-by p()
	 * @uses Df_Catalog_Model_Resource_Installer_Attribute::addAttributeToSetRm()
	 * @return string
	 */
	private function _process() {
		// Этот метод добавляет группу только по необходимости (при её отсутствии)
		df_h()->catalog()->product()->addGroupToAttributeSetIfNeeded(
			$this->getSetId(), $this->getGroupName()
		);
		return Df_Catalog_Model_Resource_Installer_Attribute::s()->addAttributeToSetRm(
			df_eav_id_product()
			, $this->getSetId()
			, $this->getGroupName()
			, $this->getAttributeCode()
			, $this->getOrdering()
		);
	}

	/** @return string */
	private function getAttributeCode() {return $this->cfg(self::$P__ATTRIBUTE_CODE);}

	/** @return string */
	private function getGroupName() {return $this->cfg(self::$P__GROUP_NAME, self::$GROUP_NAME__GENERAL);}

	/** @return int|null */
	private function getOrdering() {return $this->cfg(self::$P__ORDERING);}

	/** @return int */
	private function getSetId() {return $this->cfg(self::$P__SET_ID);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__GROUP_NAME, DF_V_STRING_NE, false)
			->_prop(self::$P__ATTRIBUTE_CODE, DF_V_STRING_NE)
			->_prop(self::$P__SET_ID, DF_V_INT)
			->_prop(self::$P__ORDERING, DF_V_INT, false)
		;
	}
	/** @var string */
	private static $GROUP_NAME__GENERAL = 'General';
	/** @var string */
	private static $P__ATTRIBUTE_CODE = 'attribute_code';
	/** @var string */
	private static $P__GROUP_NAME = 'group_name';
	/** @var string */
	private static $P__ORDERING = 'ordering';
	/** @var string */
	private static $P__SET_ID = 'set_id';

	/**
	 * Метод возвращает статус операции.
	 * @uses Df_Catalog_Model_Resource_Installer_Attribute::addAttributeToSetRm()
	 * @param string $attributeCode
	 * @param int $setId
	 * @param string|null $groupName [optional]
	 * @param int $ordering [optional]
	 * @return string
	 */
	public static function p(
		$attributeCode, $setId, $groupName = null, $ordering = null
	) {
		if (!$groupName) {
			$groupName = self::$GROUP_NAME__GENERAL;
		}
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
			/** @var Df_Catalog_Model_Installer_AddAttributeToSet $i */
			$i = new self(array(
				self::$P__GROUP_NAME => $groupName
				,self::$P__ATTRIBUTE_CODE => $attributeCode
				,self::$P__SET_ID => $setId
				,self::$P__ORDERING => $ordering
			));
			$result = $i->_process();
			$cache[$setId][$attributeCode][$groupName] = true;
		}
		return $result;
	}
}