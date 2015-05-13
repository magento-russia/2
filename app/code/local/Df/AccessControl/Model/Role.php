<?php
/**
 * @method Df_AccessControl_Model_Resource_Role getResource()
 */
class Df_AccessControl_Model_Role extends Df_Core_Model_Abstract {
	/** @return int[] */
	public function getCategoryIds() {
		df_assert($this->isModuleEnabled());
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_parse_csv($this->getCategoryIdsAsString());
		}
		return $this->{__METHOD__};
	}

	/** @return int[] */
	public function getCategoryIdsWithAncestors() {
		df_assert($this->isModuleEnabled());
		if (!isset($this->{__METHOD__})) {
			/** @var int[] $result */
			$result = $this->getCategoryIds();
			foreach ($this->getCategories() as $category) {
				/** @var Df_Catalog_Model_Category $category */
				$result = array_merge($result, $category->getParentIds());
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function isModuleEnabled() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = !!$this->getId();
		}
		return $this->{__METHOD__};
	}

	/**
	 * Говорим системе использовать insert, а не update
	 * @param int $roleId
	 * @return Df_AccessControl_Model_Role
	 */
	public function prepareForInsert($roleId) {
		df_param_integer($roleId, 0);
		$this->getResource()->prepareForInsert();
		$this
			->setId($roleId)
			->isObjectNew(true)
		;
		return $this;
	}

	/**
	 * @param int[] $categoryIds
	 * @return Df_AccessControl_Model_Role
	 */
	public function setCategoryIds(array $categoryIds) {
		df_param_array($categoryIds, 0);
		$this->{__CLASS__ . '::getCategoryIds'} = $categoryIds;
		$this->setDataChanges(true);
		return $this;
	}

	/**
	 * @override
	 * @return Df_Core_Model_Abstract
	 */
	protected function _beforeSave() {
		$this->setData(self::P__CATEGORIES, implode(',', $this->getCategoryIds()));
		parent::_beforeSave();
	}

	/** @return Df_Catalog_Model_Resource_Category_Collection */
	private function getCategories() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Catalog_Model_Resource_Category_Collection $result */
			$result = Df_Catalog_Model_Resource_Category_Collection::i();
			$result->setFlag(
				Df_AccessControl_Model_Handler_Catalog_Category_Collection_ExcludeForbiddenCategories
					::DISABLE_PROCESSING
				,true
			);
			$result->addAttributeToSelect('*');
			$result->addIdFilter($this->getCategoryIds());
			$result->addIsActiveFilter();
			$result->addNameToResult();
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getCategoryIdsAsString() {
		df_assert($this->isModuleEnabled());
		return $this->cfg(self::P__CATEGORIES, '');
	}

	/**
	 * @override
	 * Initialize resource
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_AccessControl_Model_Resource_Role::mf());
	}

	/** @var string */
	protected $_eventPrefix = 'df_access_control_role';
	/** @var string */
	protected $_eventObject = 'role';

	const _CLASS = __CLASS__;
	const P__CATEGORIES = 'categories';
	const P__STORES = 'stores';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_AccessControl_Model_Role
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_AccessControl_Model_Role
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}
	/**
	 * Используется в @see Df_AccessControl_Model_Resource_Role_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
}