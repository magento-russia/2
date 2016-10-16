<?php
/** @method Df_AccessControl_Model_Resource_Role getResource() */
class Df_AccessControl_Model_Role extends Df_Core_Model {
	/** @return int[] */
	public function getCategoryIds() {
		df_assert($this->isModuleEnabled());
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_csv_parse_int($this->getCategoryIdsAsString());
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
			/**
			 * 2015-02-06
			 * Т.к. ключи массива — целочисленные, то результат применения @uses array_merge()
			 * может содержать повторяющиеся элементы,
			 * которые мы удаляем посредством @uses dfa_unique_fast().
			 * http://php.net/manual/function.array-merge.php
			 * «If, however, the arrays contain numeric keys,
			 * the later value will not overwrite the original value, but will be appended.»
			 */
			$this->{__METHOD__} = dfa_unique_fast($result);
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
	 * @return Df_AccessControl_Model_Resource_Role
	 */
	protected function _getResource() {return Df_AccessControl_Model_Resource_Role::s();}

	/**
	 * @override
	 * @return Df_AccessControl_Model_Role
	 */
	protected function _beforeSave() {
		$this->setData(self::P__CATEGORIES, df_csv_parse_int($this->getCategoryIds()));
		parent::_beforeSave();
		return $this;
	}

	/** @return Df_Catalog_Model_Resource_Category_Collection */
	private function getCategories() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Catalog_Model_Resource_Category_Collection $result */
			$result = Df_Catalog_Model_Category::c();
			Df_AccessControl_Helper_Data::disable($result, true);
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

	/** @used-by Df_AccessControl_Model_Resource_Role::tableCreate() */
	const P__CATEGORIES = 'categories';
	/** @used-by Df_AccessControl_Model_Resource_Role::tableCreate() */
	const P__ID = Df_Admin_Model_Role::P__ID;
	/** @used-by Df_AccessControl_Model_Resource_Role::tableCreate() */
	const P__STORES = 'stores';

	/**
	 * 2015-03-11
	 * Обратите внимание, что идентификатор загружаемого объекта может отсутствовать в запросе HTTP,
	 * и тогда метод вернёт просто пустой объект.
	 * Обратите внимание, что метод возвращает объект-одиночку,
	 * потому что запрос не меняется в течение жизненного цикла его обработки интерпретатором PHP.
	 * @return Df_AccessControl_Model_Role
	 */
	public static function fromRequest() {
		/** @var Df_AccessControl_Model_Role $result */
		static $result;
		if (!isset($result)) {
			/** @var Df_AccessControl_Model_Role $result */
			$result = new self;
			/** @var int|null $id */
			$id = df_request('rid');
			if ($id) {
				$result->load($id);
			}
		}
		return $result;
	}

	/**
	 * @used-by Df_AccessControl_Model_Handler_Permissions_Role_Saverole_UpdateCatalogAccessRights::getRole()
	 * @used-by Df_AccessControl_Block_Admin_Tab::getRole()
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_AccessControl_Model_Role
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}

	/**
	 * @used-by Df_AccessControl_Block_Admin_Tab_Tree::role()
	 * @used-by Df_AccessControl_Helper_Data::getCurrentRole()
	 * @param int $id
	 * @return Df_AccessControl_Model_Role
	 */
	public static function ld($id) {
		/** @var Df_AccessControl_Model_Role $result */
		$result = new self;
		/**
		 * Обратите внимание,
		 * что объект @see Df_AccessControl_Model_Role может отсутствовать в БД.
		 * Видимо, это дефект моего программирования 2011 года.
		 * Но даже в этом случае вызов @uses Mage_Core_Model_Abstract::load()
		 * не возбуждает исключительную ситуацию, в отличие от @see df_load().
		 */
		$result->load($id);
		return $result;
	}
}