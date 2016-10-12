<?php
/**
 * @method Df_Cms_Model_Resource_Page_Revision getResource()
 */
class Df_Cms_Model_Page_Revision extends Df_Core_Model {
	/** @return string[] */
	public function getCacheIdTags() {
		$tags = parent::getCacheIdTags();
		if ($tags && $this->getPageId()) {
			$tags[]= Mage_Cms_Model_Page::CACHE_TAG.'_'.$this->getPageId();
		}
		return $tags;
	}

	/**
	 * Loading revision with empty data which is under
	 * control and with other data from version and page.
	 * Also apply extra access level checking.
	 * @param int $versionId
	 * @param int $pageId
	 * @param array|string $accessLevel
	 * @param int $userId
	 * @return Df_Cms_Model_Page_Revision
	 */
	public function loadByVersionPageWithRestrictions($versionId, $pageId, $accessLevel, $userId) {
		$this->getResource()->loadByVersionPageWithRestrictions($this, $versionId, $pageId, $accessLevel, $userId);
		$this->_afterLoad();
		$this->setOrigData();
		return $this;
	}

	/**
	 * Loading revision with extra access level checking.
	 *
	 * @param array|string $accessLevel
	 * @param int $userId
	 * @param int|string $value
	 * @param string|null $field
	 * @return Df_Cms_Model_Page_Revision
	 */
	public function loadWithRestrictions($accessLevel, $userId, $value, $field = null) {
		$this->getResource()->loadWithRestrictions($this, $accessLevel, $userId, $value, $field);
		$this->_afterLoad();
		$this->setOrigData();
		return $this;
	}

	/**
	 * @throws Exception
	 * @return Df_Cms_Model_Page_Revision
	 */
	public function publish() {
		$this->getResource()->beginTransaction();
		try {
			$data = $this->_prepareDataForPublish($this);
			/** @var Df_Cms_Model_Page_Revision $object */
			$object = Df_Cms_Model_Page_Revision::i($data);
			$this->getResource()->publish($object, $this->getPageId());
			$this->getResource()->commit();
		} catch (Exception $e){
			$this->getResource()->rollBack();
			throw $e;
		}
		$this->cleanModelCache();
		return $this;
	}

	/**
	 * @override
	 * @return Df_Cms_Model_Page_Revision
	 */
	protected function _beforeDelete() {
		$resource = $this->getResource();
		/* @var $resource Df_Cms_Model_Resource_Page_Revision */
		if ($resource->isRevisionPublished($this)) {
			Mage::throwException(
				df_h()->cms()->__('Revision #%s could not be removed because it is published.', $this->getRevisionNumber())
			);
		}
	}

	/**
	 * @override
	 * @return Df_Cms_Model_Page_Revision
	 */
	protected function _beforeSave() {
		/*
		 * Reseting revision id this revision should be saved as new.
		 * Bc data was changed or original version id not equals to new version id.
		 */
		if ($this->_revisionedDataWasModified() || $this->getVersionId() != $this->getOrigData('version_id')) {
			$this->unsetData($this->getIdFieldName());
			$this->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
			$increment = Df_Cms_Model_Increment::i();
			$incrementNumber =
				$increment
					->getNewIncrementId(
						Df_Cms_Model_Increment::TYPE_PAGE,$this->getVersionId()
						,Df_Cms_Model_Increment::LEVEL_REVISION
					)
			;
			$this->setRevisionNumber($incrementNumber);
		}
		return parent::_beforeSave();
	}

	/** @return mixed[] */
	protected function _prepareDataForPublish() {
		$data = array();
		$attributes = $this->_config->getPageRevisionControledAttributes();
		foreach ($this->getData() as $key => $value) {
			if (in_array($key, $attributes)) {
				$this->unsetData($key);
				$data[$key] = $value;
			}
		}
		$data['published_revision_id'] = $this->getId();
		return $data;
	}

	/** @return bool */
	protected function _revisionedDataWasModified() {
		$attributes = $this->_config->getPageRevisionControledAttributes();
		foreach ($attributes as $attr) {
			$value = $this->_getData($attr);
			if ($this->getOrigData($attr) !== $value) {
				if (
						(
								is_null($this->getOrigData($attr))
							&&
								('' === $value)
						)
					||
						is_null($value)
				) {
					continue;
				}
				return true;
			}
		}
		return false;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Cms_Model_Resource_Page_Revision::mf());
		$this->_config = Df_Cms_Model_Config::s();
	}

	/** @var string */
	protected $_cacheTag = 'CMS_REVISION';
	/** @var Df_Cms_Model_Config */
	protected $_config;
	/** @var string */
	protected $_eventObject = 'revision';
	/** @var string */
	protected $_eventPrefix = 'df_cms_revision';

	const _CLASS = __CLASS__;
	const P__ID = 'revision_id';

	/** @return Df_Cms_Model_Resource_Page_Revision_Collection */
	public static function c() {return self::s()->getCollection();}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Cms_Model_Page_Revision
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_Cms_Model_Page_Revision
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}
	/**
	 * @see Df_Cms_Model_Resource_Page_Revision_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
	/** @return Df_Cms_Model_Page_Revision */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}