<?php
class Df_Dataflow_Model_Category_Path extends Df_Core_Model {
	/**
	 * The path can be mirrored to several categories
	 * (e.g., with same names but in different tree branches)
	 * @return Df_Catalog_Model_Category[]
	 */
	public function getCategories() {
		/** @var Df_Catalog_Model_Category[] $result */
		$result = $this->findCategories();;
		df_result_array($result);
		if (0 === count($result)) {
			$result = $this->createCategories();
			df_result_array($result);
		}
		return $result;
	}

	/** @return Df_Catalog_Model_Category[] */
	private function createCategories() {
		/** @var mixed[][] $relevancies */
		$relevancies = $this->getTheMostRelevantExistedCategoriesToInsertToInsertNewPath();
		df_assert_array($relevancies);
		/** @var Df_Catalog_Model_Category[] $result */
		$result = array();
		if (0 === count($relevancies)){
			$relevancies[]= array(self::INTERNAL_PARAM__IDENTICAL_PART_LENGTH => 0);
		}
		foreach ($relevancies as $relevancy) {
			/** @var mixed[] $relevancy */
			df_assert_array($relevancy);
			/** @var int $identicalPartLength */
			$identicalPartLength = df_a($relevancy, self::INTERNAL_PARAM__IDENTICAL_PART_LENGTH);
			df_assert_integer($identicalPartLength);
			/** @var Df_Catalog_Model_Category|null $nodeToGrow */
			$nodeToGrow = null;
			/** @var array $pathToAdd */
			$pathToAdd = array();
			if (0 === $identicalPartLength) {
				// Add category to the root
				$pathToAdd = $this->getPathAsNamesArray();
			}
			else {
				/** @var array $identicalPart */
				$identicalPart = df_a($relevancy, self::INTERNAL_PARAM__IDENTICAL_PART);
				df_assert_array($identicalPart);
				$nodeToGrow = df_a($identicalPart, $identicalPartLength - 1);
				if (!is_null($nodeToGrow)) {
					df_assert($nodeToGrow instanceof Df_Catalog_Model_Category);
				}
				/** @var array $pathToAdd */
				$pathToAdd =
					array_slice(
						$this->getPathAsNamesArray()
						,$identicalPartLength
						,$this->getNumParts() - $identicalPartLength
					)
				;
				df_assert_array($pathToAdd);
			}
			foreach ($pathToAdd as $name) {
				/** @var string $name */
				df_assert_string($name);
				/**
				 * Перед созданием и сохранением товарного раздела
				 * надо обязательно надо установить текущим магазином административный,
				 * иначе возникают неприятные проблемы.
				 *
				 * В частности, для успешного сохранения товарного раздела
				 * надо отключить на время сохранения режим денормализации.
				 * Так вот, в стандартном программном коде Magento автоматически отключает
				 * режим денормализации при создании товарного раздела из административного магазина
				 * (в конструкторе товарного раздела).
				 *
				 * А если сохранять раздел,
				 * чей конструктор вызван при включенном режиме денормализации — то произойдёт сбой:
				 * SQLSTATE[23000]: Integrity constraint violation:
				 * 1452 Cannot add or update a child row:
				 * a foreign key constraint fails
				 * (`catalog_category_flat_store_1`,
				 * CONSTRAINT `FK_CAT_CTGR_FLAT_STORE_1_ENTT_ID_CAT_CTGR_ENTT_ENTT_ID`
				 * FOREIGN KEY (`entity_id`) REFERENCES `catalog_category_entity` (`en)
				 */
				$nodeToGrow =
					Df_Catalog_Model_Category::createAndSave(
						array(
							Df_Catalog_Model_Category::P__PATH =>
								!$nodeToGrow
								? $this->getDefaultSystemPath()
								: $nodeToGrow->getDataUsingMethod(
									Df_Catalog_Model_Category::P__PATH
								)
							,Df_Catalog_Model_Category::P__NAME => $name
							,Df_Catalog_Model_Category::P__IS_ACTIVE => true
							,Df_Catalog_Model_Category::P__IS_ANCHOR => true
							,Df_Catalog_Model_Category::P__DISPLAY_MODE =>
								Df_Catalog_Model_Category::DM_MIXED
						)
						,$this->getStore()->getId()
					)
				;
			}
			if (!is_null($result)) {
				$result[]= $nodeToGrow;
			}
		}
		return $result;
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @link http://php.net/manual/en/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * @link http://3v4l.org/OipEQ
	 *
	 * @param string $string
	 * @return string
	 */
	private function escapeSlash($string) {return str_replace(self::PARTS_SEPARATOR, '\/', $string);}

	/** @return Df_Catalog_Model_Category[] */
	private function findCategories() {
		/** @var Df_Catalog_Model_Category[] $result */
		$result = array();
		/** @var Df_Catalog_Model_Resource_Category_Collection $categories */
		$categories = $this->findCategoriesByName($this->getNodeName());
		foreach ($categories as $category) {
			/** @var Df_Catalog_Model_Category $category */
			if ($this->isContains($this->getPathForCategory($category), $this->getPathAsNamesArray())) {
				// Grab the first relevant category
				$result[]= $category;
			}
		}
		return $result;
	}

	/**
	 * Этот метод работает неоптимально.
	 * Вместо того, чтобы делать запрос к БД на каждый вызов метода,
	 * разумнее сделать карту:
	 * <название товарного раздела> => <товарный раздел>
	 * @param string $name
	 * @return Df_Catalog_Model_Resource_Category_Collection
	 */
	private function findCategoriesByName($name) {
		df_param_string($name, 0);
		/** @var Df_Catalog_Model_Resource_Category_Collection $result */
		$result = Df_Catalog_Model_Category::c();
		$result->setStore($this->getStore());
		$result->addAttributeToSelect('*');
		$result->addFieldToFilter('name', $name);
		return $result;
	}

	/** @return string */
	private function getDefaultSystemPath() {
		/** @var int $rootId */
		$rootId = $this->getStore()->getRootCategoryId();
		df_assert_integer($rootId);
		if (0 === $rootId) {
			/** @var Mage_Core_Model_Store $store */
			$store =  Mage::app()->getStore(Mage_Core_Model_App::DISTRO_STORE_ID);
			df_assert($store instanceof Mage_Core_Model_Store);
			$rootId = $store->getRootCategoryId();
		}
		return rm_concat_clean(self::PARTS_SEPARATOR, self::FIRST_PART_FOR_ROOT, $rootId);
	}

	/**
	 * @param Df_Catalog_Model_Category $root
	 * @return array
	 */
	private function getIdenticalPartBetweenRootAndNewPath(Df_Catalog_Model_Category $root) {
		/** @var array $result */
		$result = array();
		if ($root->getName() === df_a($this->getPathAsNamesArray(), 0)) {
			$result[]= $root;
			/** @var int $depth */
			$depth = 1;
			/** @var Df_Catalog_Model_Category $currentNode */
			$currentNode = $root;
			while ($depth < $this->getNumParts()) {
				/**
				 * Обратите внимание, что метод getChildrenCategories
				 * возвращает Df_Catalog_Model_Category[] при включенной денормализации
				 * и Mage_Catalog_Model_Resource_Category_Collection при выключенной денормализации.
				 *
				 * Учитывая, что Российская сборка перекрывает класс Df_Catalog_Model_Category
				 * классом Df_Catalog_Model_Category, то при включенной денормализации
				 * метод getChildrenCategories возвращает Df_Catalog_Model_Category[]
				 *
				 * @var Df_Catalog_Model_Category[]|Mage_Catalog_Model_Resource_Category_Collection $children
				 */
				$children = $currentNode->getChildrenCategories();
				/** @var Df_Catalog_Model_Category|null $relevantChild */
				$relevantChild = null;
				foreach ($children as $child) {
					/** @var Df_Catalog_Model_Category $child */
					if ($child->getName() === df_a($this->getPathAsNamesArray(), $depth)) {
						$relevantChild = $child;
						$result[]= $relevantChild;
						break;
					}
				}
				if (is_null($relevantChild)) {
					break;
				}
				else {
					$currentNode = $relevantChild;
				}
				$depth++;
			}
		}
		return $result;
	}

	/** @return string */
	private function getLocalRootName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_a($this->getPathAsNamesArray(), 0);
			df_result_string($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getNodeName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_a($this->getPathAsNamesArray(), $this->getNumParts() - 1);
			df_result_string($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	private function getNumParts() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = count($this->getPathAsNamesArray());
		}
		return $this->{__METHOD__};
	}

	/** @return string[] */
	private function getPathAsNamesArray() {return $this->cfg(self::P__PATH_AS_NAMES_ARRAY);}

	/** @return string */
	private function getPathAsString() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->pathToString($this->getPathAsNamesArray());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param Df_Catalog_Model_Category $category
	 * @return array
	 */
	private function getPathForCategory(Df_Catalog_Model_Category $category) {
		/** @var array $result */
		$result = array();
		/** @var Df_Catalog_Model_Category $currentCategory */
		$currentCategory = $category;
		while (0 !== $currentCategory->getParentId()) {
			/** @var string $categoryName */
			$categoryName = $category->getName();
			df_assert_string($categoryName);
			$result[]= $categoryName;
			$currentCategory = $currentCategory->getParentCategory();
			df_assert($currentCategory instanceof Df_Catalog_Model_Category);
		}
		return $result;
	}

	/** @return Mage_Core_Model_Store */
	private function getStore() {return $this->cfg(self::P__STORE);}

	/** @return mixed[][] */
	private function getTheMostRelevantExistedCategoriesToInsertToInsertNewPath() {
		/** @var mixed[][] $result */
		$result = array();
		/** @var int $identicalPartLength */
		$identicalPartLength = 0;
		/** @var Df_Catalog_Model_Resource_Category_Collection $categoriesWithSameName */
		$categoriesWithSameName = $this->findCategoriesByName($this->getLocalRootName());
		foreach ($categoriesWithSameName as $category) {
			/** @var Df_Catalog_Model_Category $category */
			/** @var array $currentIdenticalPart */
			$currentIdenticalPart = $this->getIdenticalPartBetweenRootAndNewPath($category);
			df_assert_array($currentIdenticalPart);
			/** @var int $currentIdenticalPartLength */
			$currentIdenticalPartLength = count($currentIdenticalPart);
			if ($identicalPartLength <= $currentIdenticalPartLength) {
				$identicalPartLength = $currentIdenticalPartLength;
				$result[]=
					array(
						self::INTERNAL_PARAM__IDENTICAL_PART_LENGTH => $identicalPartLength
						,self::INTERNAL_PARAM__CATEGORY => $category
						,self::INTERNAL_PARAM__IDENTICAL_PART => $currentIdenticalPart
					)
				;
			}
		}
		return $result;
	}

	/**
	 * @param array $haystack
	 * @param array $needle
	 * @return bool
	 */
	private function isContains(array $haystack, array $needle) {
		df_param_array($haystack, 0);
		df_param_array($needle, 1);
		/** @var bool $result */
		$result = true;
		if (!empty($needle)) {
			/** @var string $needleRoot */
			$needleRoot = df_a($needle, 0);
			df_assert_string($needleRoot);
			/** @var int|bool $indexOfNeedleRootInHaystack */
			$indexOfNeedleRootInHaystack =
				array_search($needleRoot, $haystack)
			;
			if (false !== $indexOfNeedleRootInHaystack) {
				df_assert_integer($indexOfNeedleRootInHaystack);
			}
			if (false === $indexOfNeedleRootInHaystack) {
				$result = false;
			}
			else {
				for($offset = 1; $offset < $this->getNumParts(); $offset++) {
					/** @var int $offset */
					/** @var string|null $currentNeedleValue */
					$currentNeedleValue = df_a($needle, $offset);
					if (!is_null($currentNeedleValue)) {
						df_assert_string($currentNeedleValue);
					}
					/** @var string|null $currentHaystackValue */
					$currentHaystackValue = df_a($haystack, $offset + $indexOfNeedleRootInHaystack);
					if (!is_null($currentHaystackValue)) {
						df_assert_string($currentHaystackValue);
					}
					if ($currentNeedleValue !== $currentHaystackValue) {
						$result = false;
						break;
					}
				}
			}
		}
		return $result;
	}

	/**
	 * @param string[] $path
	 * @return string
	 */
	private function pathToString(array $path) {
		df_param_array($path, 0);
		return implode(self::PARTS_SEPARATOR, array_map(array($this, 'escapeSlash'), $path));
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__STORE, 'Mage_Core_Model_Store')
			->_prop(self::P__PATH_AS_NAMES_ARRAY, self::V_ARRAY)
		;
	}
	const _CLASS = __CLASS__;
	const FIRST_PART_FOR_ROOT = '1';
	const INTERNAL_PARAM__IDENTICAL_PART_LENGTH = 'identicalPartLength';
	const INTERNAL_PARAM__IDENTICAL_PART = 'identicalPart';
	const INTERNAL_PARAM__CATEGORY = 'category';
	const P__STORE = 'store';
	const P__PATH_AS_NAMES_ARRAY = 'pathAsNamesArray';
	const PARTS_SEPARATOR = '/';
	/**
	 * @static
	 * @param string[] $path
	 * @param Mage_Core_Model_Store $store
	 * @return Df_Dataflow_Model_Category_Path
	 */
	public static function i(array $path, Mage_Core_Model_Store $store) {
		return new self(array(self::P__PATH_AS_NAMES_ARRAY => $path, self::P__STORE => $store));
	}
}