<?php
class Df_Eav_Model_Resource_Entity_Attribute_Set extends Mage_Eav_Model_Mysql4_Entity_Attribute_Set {
	/**
	 * 2015-08-10
	 * @used-by Df_Eav_Model_Entity_Attribute_Set::attributeCodes()
	 * @param int $setId
	 * @return array(int => string)
	 * Ключами возвращаемого массива являются идентификаторы свойств.
	 */
	public function attributeCodes($setId) {return df_eav_cache($this, __FUNCTION__, $setId);}

	/**
	 * 2015-08-10
	 * @see attributeCodes()
	 * @param int $setId
	 * @return array(int => string)
	 * Ключами возвращаемого массива являются идентификаторы свойств.
	 */
	public function attributeCodes_($setId) {
		df_param_integer($setId, 0);
		/** @var Zend_Db_Select $select */
		$select = df_select()
			->from(
				array('a' => df_table('eav/attribute'))
				, array('attribute_id', 'attribute_code')
			)
			->joinInner(
				array('ea' => df_table('eav/entity_attribute'))
				, 'a.attribute_id = ea.attribute_id'
				, null
			)
			->where('? = ea.attribute_set_id', $setId)
		;
		/** @var Zend_Db_Statement_Pdo $query */
		$query = df_conn()->query($select);
		/** @var array(array(string => string)) $rows */
		$rows = $query->fetchAll($style = Zend_Db::FETCH_ASSOC);
		return array_column($rows, 'attribute_code', 'attribute_id');
	}

	/**
	 * 2015-08-10
	 * Возвращает идентификатор прикладного типа объектов (по умолчанию — товаров)
	 * по имени.
	 * @used-by Df_Dataflow_Model_Import_Product_Row::getAttributeSetId()
	 * @param string $name
	 * @param int|null $type [optional]
	 * @return int
	 */
	public function idByName($name, $type = null) {
		df_param_string_not_empty($name, 0);
		return dfa($this->mapFromNameToId($type ? $type : df_eav_id_product()), $name);
	}

	/**
	 * 2015-08-10
	 * @used-by idByName()
	 * @param int $type
	 * @return array(string => int)
	 * Ключами возвращаемого массива являются идентификаторы свойств.
	 */
	public function mapFromNameToId($type) {return df_eav_cache($this, __FUNCTION__, $type);}

	/**
	 * 2015-08-10
	 * @see mapFromNameToId()
	 * @param int $type
	 * @return array(string => int)
	 * Ключами возвращаемого массива являются идентификаторы свойств.
	 */
	public function mapFromNameToId_($type) {
		/** @var Zend_Db_Select $select */
		$select = df_select()
			->from(
				array('s' => df_table('eav/attribute_set'))
				, array('attribute_set_id', 'attribute_set_name')
			)
			->where('? = s.entity_type_id', $type)
		;
		/** @var Zend_Db_Statement_Pdo $query */
		$query = df_conn()->query($select);
		/** @var array(array(string => string)) $rows */
		$rows = $query->fetchAll($style = Zend_Db::FETCH_ASSOC);
		return array_column($rows, 'attribute_set_id', 'attribute_set_name');
	}

	/** @return Df_Eav_Model_Resource_Entity_Attribute_Set */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}