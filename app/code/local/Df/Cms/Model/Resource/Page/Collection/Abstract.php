<?php
abstract class Df_Cms_Model_Resource_Page_Collection_Abstract extends Mage_Core_Model_Mysql4_Collection_Abstract {
	/**
	 * Array of admin users in loaded collection
	 * @var array
	 */
	protected $_usersHash = null;

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_map['fields']['user_id'] = 'main_table.user_id';
		$this->_map['fields']['page_id'] = 'main_table.page_id';
	}

	/**
	 * Add filtering by page id.
	 * Parameter $page can be int or cms page object.
	 *
	 * @param mixed $page
	 * @return Df_Cms_Model_Resource_Page_Collection_Abstract
	 */
	public function addPageFilter($page)
	{
		if ($page instanceof Mage_Cms_Model_Page) {
			$page = $page->getId();
		}

		if (is_array($page)) {
			$page = array('in' => $page);
		}

		$this->addFieldToFilter('page_id', $page);
		return $this;
	}

	/**
	 * Adds filter by version access level specified by owner.
	 *
	 * @param mixed $userId
	 * @param mixed $accessLevel
	 * @return Df_Cms_Model_Resource_Page_Collection_Abstract
	 */
	public function addVisibilityFilter($userId, $accessLevel = Df_Cms_Model_Page_Version::ACCESS_LEVEL_PUBLIC)
	{
		$_condition = array();
		if (is_array($userId)) {
			$_condition[]= $this->_getConditionSql(
				$this->_getMappedField('user_id'), array('in' => $userId));
		} else if ($userId){
			$_condition[]= $this->_getConditionSql(
				$this->_getMappedField('user_id'), $userId);
		}

		if (is_array($accessLevel)) {
			$_condition[]= $this->_getConditionSql(
				$this->_getMappedField('access_level'), array('in' => $accessLevel));
		} else {
			$_condition[]= $this->_getConditionSql(
				$this->_getMappedField('access_level'), $accessLevel);
		}

		$this->getSelect()->where(implode(' OR ', $_condition));
		return $this;
	}

	/**
	 * Mapping user_id to user column with additional value for non-existent users
	 * @return Df_Cms_Model_Resource_Page_Collection_Abstract
	 */
	public function addUserColumn()
	{
		$userField = new Zend_Db_Expr('IFnull(main_table.user_id, -1)');
		$this->getSelect()->columns(array('user' => $userField));
		$this->_map['fields']['user'] = $userField;
		return $this;
	}

	/**
	 * Join username from system user table
	 * @return Df_Cms_Model_Resource_Page_Collection_Abstract
	 */
	public function addUserNameColumn()
	{
		if (!$this->getFlag('user_name_column_joined')) {
			$userField = new Zend_Db_Expr('IFnull(ut.username, -1)');
			$this->getSelect()->joinLeft(
				array('ut' => rm_table('admin/user'))
				,'ut.user_id = main_table.user_id'
				,array('username' => $userField)
			);
			$this->setFlag('user_name_column_joined', true);
		}
		return $this;
	}

	/**
	 * Retrieve array of admin users in collection
	 *
	 * @param bool $idAsKey default true if false then name will be used as key and value
	 * @return array
	 */
	public function getUsersArray($idAsKey = true)
	{
		if (!$this->_usersHash) {
			$this->_usersHash = array();
			foreach ($this->_toOptionHash('user_id', 'username') as $userId => $username) {
				if ($userId) {
					if ($idAsKey) {
						$this->_usersHash[$userId] = $username;
					} else {
						$this->_usersHash[$username] = $username;
					}
				} else {
					$this->_usersHash['-1'] = df_h()->cms()->__('[No Owner]');
				}
			}

			ksort($this->_usersHash);
		}
		return $this->_usersHash;
	}

	/**
	 * Add filtering by user id.
	 *
	 * @param int|null $userId
	 * @return Df_Cms_Model_Resource_Page_Collection_Abstract
	 */
	public function addUserIdFilter($userId = null)
	{
		if (is_null($userId)) {
			$condition = array('null' => true);
		} else {
			$condition = $userId;
		}

		$this->addFieldToFilter('user_id', $condition);
		return $this;
	}
}