<?php
class Df_Cms_Model_Resource_Page_Version extends Mage_Core_Model_Mysql4_Abstract {
	/**
	 * Checking if version id not last public for its page
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return bool
	 */
	public function isVersionLastPublic(Mage_Core_Model_Abstract $object)
	{
		$select = $this->_getReadAdapter()->select();
		$select->from($this->getMainTable(), 'count(*)')
			->where('page_id = ?', $object->getPageId())
			->where('access_level = ?', Df_Cms_Model_Page_Version::ACCESS_LEVEL_PUBLIC)
			->where('version_id <> ? ', $object->getVersionId());
		return !$this->_getReadAdapter()->fetchOne($select);
	}

	/**
	 * Checking if Version does not contain published revision
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return bool
	 */
	public function isVersionHasPublishedRevision(Mage_Core_Model_Abstract $object)
	{
		$select = $this->_getReadAdapter()->select();
		$select->from(array('p' => rm_table('cms/page')), array())
			->where('p.page_id = ?', $object->getPageId())
			->join(array('r' => rm_table('df_cms/page_revision')),'r.revision_id = p.published_revision_id', array('r.version_id'));
		$result = $this->_getReadAdapter()->fetchOne($select);
		return $result === $object->getVersionId();
	}

	/**
	 * Add access restriction filters to allow load only by granted user.
	 *
	 * @param Zend_Db_Select $select
	 * @param int $accessLevel
	 * @param int $userId
	 * @return Zend_Db_Select
	 */
	protected function _addAccessRestrictionsToSelect($select, $accessLevel, $userId)
	{
		$conditions = array('user_id = ' . $userId);
		if (is_array($accessLevel) && !empty($accessLevel)) {
			$conditions[]= 'access_level in ("' . implode('","', $accessLevel) . '")';
		} else if ($accessLevel) {
			$conditions[]= 'access_level = "' . $accessLevel . '"';
		} else {
			$conditions[]= 'access_level = ""';
		}

		$conditions = implode(' OR ', $conditions);
		$select->where($conditions);
		return $select;
	}

	/**
	 * Loading data with extra access level checking.
	 *
	 * @param Df_Cms_Model_Page_Version $object
	 * @param array|string $accessLevel
	 * @param int $userId
	 * @param int|string $value
	 * @param string|null $field
	 * @return Df_Cms_Model_Page_Version
	 */
	public function loadWithRestrictions($object, $accessLevel, $userId, $value, $field = null)
	{
		if (is_null($field)) {
			$field = $this->getIdFieldName();
		}

		$read = $this->_getReadAdapter();
		if ($read && $value) {
			$select = $this->_getLoadSelect($field, $value, $object);
			$select = $this->_addAccessRestrictionsToSelect($select, $accessLevel, $userId);
			$data = $read->fetchRow($select);
			if ($data) {
				$object->setData($data);
			}
		}
		$this->_afterLoad($object);
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		/**
		 * Нельзя вызывать parent::_construct(),
		 * потому что это метод в родительском классе — абстрактный.
		 * @see Mage_Core_Model_Resource_Abstract::_construct()
		 */
		$this->_init(self::TABLE_NAME, Df_Cms_Model_Page_Version::P__ID);
	}
	const _CLASS = __CLASS__;
	const TABLE_NAME = 'df_cms/page_version';
	/**
	 * @see Df_Cms_Model_Page_Version::_construct()
	 * @see Df_Cms_Model_Resource_Page_Version_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Cms_Model_Resource_Page_Version */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}