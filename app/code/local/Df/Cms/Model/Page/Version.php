<?php
/**
 * @method Df_Cms_Model_Page_Revision|null getLastRevision()
 * @method Df_Cms_Model_Resource_Page_Version getResource()
 * @method Df_Cms_Model_Page_Version setAccessLevel(string $value)
 * @method Df_Cms_Model_Page_Version setLabel(string $value)
 * @method Df_Cms_Model_Page_Version setPageId(int $value)
 * @method Df_Cms_Model_Page_Version setUserId(int $value)
 * @method Df_Cms_Model_Page_Version setInitialRevisionData(array $value)
 */
class Df_Cms_Model_Page_Version extends Df_Core_Model {
	/**
	 * @override
	 * @return Df_Cms_Model_Resource_Page_Version_Collection
	 */
	public function getResourceCollection() {return self::c();}

	/** @return bool */
	public function isPublic() {return self::ACCESS_LEVEL_PUBLIC === $this->getAccessLevel();}

	/**
	 * Loading version with extra access level checking.
	 * @param array|string $accessLevel
	 * @param int $userId
	 * @param int|string $value
	 * @param string|null $field
	 * @return Df_Cms_Model_Page_Version
	 */
	public function loadWithRestrictions($accessLevel, $userId, $value, $field = null) {
		$this->getResource()->loadWithRestrictions($this, $accessLevel, $userId, $value, $field = null);
		$this->_afterLoad();
		$this->setOrigData();
		return $this;
	}

	/**
	 * @override
	 * @return Df_Cms_Model_Page_Version
	 */
	protected function _afterDelete() {
		Df_Cms_Model_Resource_Increment::s()->cleanIncrementRecord(
			Df_Cms_Model_Increment::TYPE_PAGE,$this->getId()
			,Df_Cms_Model_Increment::LEVEL_REVISION
		);
		return parent::_afterDelete();
	}

	/**
	 * @override
	 * @return Df_Cms_Model_Page_Version
	 */
	protected function _afterSave() {
		// If this was a new version we should create initial revision for it
		// from specified revision or from latest for parent version
		if ($this->getOrigData($this->getIdFieldName()) != $this->getId()) {
			/** @var Df_Cms_Model_Page_Revision $revision */
			$revision = Df_Cms_Model_Page_Revision::i();
			// setting data for load
			$userId = $this->getUserId();
			$accessLevel = Df_Cms_Model_Config::s()->getAllowedAccessLevel();
			if ($this->getInitialRevisionData()) {
				$revision->setData($this->getInitialRevisionData());
			} else {
				$revision->loadWithRestrictions($accessLevel, $userId, $this->getOrigData($this->getIdFieldName()), 'version_id');
			}
			$revision->setVersionId($this->getId())
				->setUserId($userId)
				->save();
			$this->setLastRevision($revision);
		}
		parent::_afterSave();
		return $this;
	}

	/**
	 * @override
	 * @return Df_Cms_Model_Page_Version
	 */
	protected function _beforeDelete() {
		$resource = $this->getResource();
		/* @var $resource Df_Cms_Model_Resource_Page_Version */
		if ($this->isPublic()) {
			if ($resource->isVersionLastPublic($this)) {
				Mage::throwException(
					df_h()->cms()->__('Version "%s" could not be removed because it is last public version for its page.', $this->getLabel())
				);
			}
		}
		if ($resource->isVersionHasPublishedRevision($this)) {
			Mage::throwException(
				df_h()->cms()->__('Version "%s" could not be removed because its revision has been published.', $this->getLabel())
			);
		}
		parent::_beforeDelete();
		return $this;
	}

	/**
	 * @override
	 * @return Df_Cms_Model_Page_Version
	 */
	protected function _beforeSave() {
		if (!$this->getId()) {
			$increment = Df_Cms_Model_Increment::i();
			$incrementNumber =
				$increment
					->getNewIncrementId(
						Df_Cms_Model_Increment::TYPE_PAGE
						,$this->getPageId()
						,Df_Cms_Model_Increment::LEVEL_VERSION
					)
			;
			$this->setVersionNumber($incrementNumber);
			$this->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
		}
		if (!$this->getLabel()) {
			Mage::throwException(df_h()->cms()->__('Label for version is required field.'));
		}
		// We can not allow changing access level for some versions
		if ($this->getAccessLevel() != $this->getOrigData('access_level')) {
			if (
					Df_Cms_Model_Page_Version::ACCESS_LEVEL_PUBLIC
				===
					$this->getOrigData('access_level')
			) {
				$resource = $this->getResource();
				/* @var $resource Df_Cms_Model_Resource_Page_Version */

				if ($resource->isVersionLastPublic($this)) {
					Mage::throwException(
						df_h()->cms()->__('Cannot change version access level because it is last public version for its page.')
					);
				}
			}
		}
		parent::_beforeSave();
		return $this;
	}

	/**
	 * @override
	 * @return Df_Cms_Model_Resource_Page_Version
	 */
	protected function _getResource() {return Df_Cms_Model_Resource_Page_Version::s();}

	/** @var string */
	protected $_eventPrefix = 'df_cms_version';
	/** @var string */
	protected $_eventObject = 'version';

	/** @used-by Df_Cms_Model_Resource_Page_Version_Collection::_construct() */

	/**
	 * Access level constants
	 */
	const ACCESS_LEVEL_PRIVATE = 'private';
	const ACCESS_LEVEL_PROTECTED = 'protected';
	const ACCESS_LEVEL_PUBLIC = 'public';
	const P__ID = 'version_id';

	/** @return Df_Cms_Model_Resource_Page_Version_Collection */
	public static function c() {return new Df_Cms_Model_Resource_Page_Version_Collection;}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Cms_Model_Page_Version
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_Cms_Model_Page_Version
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}