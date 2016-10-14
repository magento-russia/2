<?php
/**
 * @method Df_Cms_Model_Resource_Hierarchy_Lock getResource()
 */
class Df_Cms_Model_Hierarchy_Lock extends Df_Core_Model {
	/** @return int */
	public function getLockLifeTime() {
		$timeout = df_nat0(Mage::getStoreConfig('df_cms/hierarchy/lock_timeout'));
		return ($timeout != 0 && $timeout < 120 ) ? 120 : $timeout;
	}

	/** @return Df_Cms_Model_Hierarchy_Lock */
	public function loadLockData() {
		if (!$this->_dataLoaded) {
			$data = $this->getResource()->getLockData();
			$this->addData($data);
			$this->_dataLoaded = true;
		}
		return $this;
	}
	/** @var bool */
	protected $_dataLoaded = false;

	/** @return bool */
	public function isLocked() {
		return($this->isEnabled() && $this->isActual());
	}

	/** @return bool */
	public function isLockedByMe() {
		return($this->isLocked() && $this->isLockOwner());
	}

	/** @return bool */
	public function isLockedByOther() {
		return($this->isLocked() && !$this->isLockOwner());
	}

	/** @return Df_Cms_Model_Hierarchy_Lock */
	public function revalidate() {
		if (!$this->isEnabled()) {
			return $this;
		}
		if (!$this->isLocked() || $this->isLockedByMe()) {
			$this->lock();
		}
		return $this;
	}

	/** @return bool */
	public function isActual() {
		$this->loadLockData();
		if ($this->hasData('started_at') && $this->_getData('started_at') + $this->getLockLifeTime() > time()) {
			return true;
		}
		return false;
	}

	/** @return bool */
	public function isEnabled() {
		return($this->getLockLifeTime() > 0);
	}

	/** @return bool */
	public function isLockOwner() {
		$this->loadLockData();
		if (
				(
						df_nat0($this->_getData('user_id'))
					===
						df_nat0($this->_getSession()->getUser()->getId())
				)
			&&
				(
						$this->_getData('session_id')
					===
						$this->_getSession()->getSessionId()
				)
		)
		{
			return true;
		}
		return false;
	}

	/** @return Df_Cms_Model_Hierarchy_Lock */
	public function lock() {
		$this->loadLockData();
		if ($this->getId()) {
			$this->delete();
		}
		$this
			->setData(
				array(
					'user_id' => $this->_getSession()->getUser()->getId()
					,'user_name' => $this->_getSession()->getUser()->getName()
					,'session_id' => $this->_getSession()->getSessionId()
					,'started_at' => time()
				)
			)
		;
		$this->save();
		return $this;
	}

	/**
	 * Setter for session instance
	 * @param Mage_Core_Model_Session_Abstract $session
	 * @return Df_Cms_Model_Hierarchy_Lock
	 */
	public function setSession(Mage_Core_Model_Session_Abstract $session) {
		$this->_session = $session;
		return $this;
	}
	/** @var Mage_Admin_Model_Session */
	protected $_session;

	/**
	 * @override
	 * @return Df_Cms_Model_Resource_Hierarchy_Lock
	 */
	protected function _getResource() {return Df_Cms_Model_Resource_Hierarchy_Lock::s();}

	/**
	 * Getter for session instance
	 * @return Mage_Core_Model_Session_Abstract
	 */
	protected function _getSession() {return $this->_session ? $this->_session : rm_admin_session();}

	const _C = __CLASS__;
	const P__ID = 'lock_id';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Cms_Model_Hierarchy_Lock
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_Cms_Model_Hierarchy_Lock
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}
	/** @return Df_Cms_Model_Hierarchy_Lock */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}