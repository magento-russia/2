<?php
/**
 * @method Df_Core_Model_Resource_Config_Data getResource()
 */
class Df_Core_Model_Config_Data extends Mage_Core_Model_Config_Data {
	/**
	 * При наличии у объекта значения поля _cacheTag
	 * система будет автоматически удалять кэш после сохранения объекта.
	 * Странно, что родительский класс не указывает _cacheTag.
	 * @see Mage_Core_Model_Abstract::cleanModelCache()
	 * @var string
	 */
	protected $_cacheTag = 'CONFIG';

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Core_Model_Resource_Config_Data::mf());
	}
	const _CLASS = __CLASS__;
	/** @var string */
	private static $P__PATH = 'path';
	/** @var string */
	private static $P__SCOPE = 'scope';
	/** @var string */
	private static $P__SCOPE_ID = 'scope_id';
	/** @var string */
	private static $P__VALUE = 'value';

	/** @return Mage_Core_Model_Resource_Config_Data_Collection */
	public static function c() {return self::s()->getCollection();}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Core_Model_Config_Data
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_Core_Model_Config_Data
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}
	/**
	 * @see Df_Core_Model_Resource_Config_Data_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}

	/**
	 * @static
	 * @param string $path
	 * @param string $value
	 * @return void
	 */
	public static function saveInDefaultScope($path, $value) {
		// глобальная область всего имеет именно этот код и идентификатор
		self::saveInScope($path, $value, 'default', 0);
	}

	/**
	 * @static
	 * @param string $path
	 * @param string $value
	 * @param string $scopeType
	 * @param int $scopeId
	 * @return void
	 */
	public static function saveInScope($path, $value, $scopeType, $scopeId) {
		/** @var Df_Core_Model_Config_Data $entry */
		$entry = self::i(array(
			self::$P__PATH => $path
			,self::$P__VALUE => $value
			,self::$P__SCOPE => $scopeType
			,self::$P__SCOPE_ID => $scopeId
		));
		$entry->setDataChanges(true);
		$entry->save();
	}

	/** @return Df_Core_Model_Config_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}