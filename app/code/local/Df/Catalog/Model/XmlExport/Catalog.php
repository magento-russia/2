<?php
abstract class Df_Catalog_Model_XmlExport_Catalog extends \Df\Xml\Generator\Document {
	/** @return string */
	abstract protected function getProcessorClass_products();

	/**
	 * Нельзя называть этот метод getAttributes,
	 * потому что одноимённый метод уже есть: @see getAttributes()
	 * @return array(string => Df_Catalog_Model_Resource_Eav_Attribute)
	 */
	public function getCatalogAttributes() {return $this->getProducts()->getAttributes();}

	/**
	 * @used-by \Df\YandexMarket\Yml\Processor\Offer::getYandexMarketCategoryName()
	 * @return Df_Catalog_Model_Resource_Category_Collection
	 */
	public function getCategories() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Catalog_Model_Resource_Category_Collection $result */
			$result = Df_Catalog_Model_Category::c();
			$result->setStore($this->store());
			$result->addIdFilter($this->getProducts()->getCategoryIds());
			$result->addAttributeToSelect('*');
			$result->setDisableFlat($this->needUpdateCategories());
			/**
			 * Вызов выше метода
			 * @uses Mage_Catalog_Model_Resource_Category_Collection::addIdFilter()
			 * может отсечь товарный раздел,
			 * который хоть и не содержит товаров непосредственно,
			 * но является родительским для товарного раздела, содержащего товары,
			 * и тогда свойство parentId товарного раздела, содержащего товары,
			 * будет ссылаться на отсутствующий в файле YML товарный раздел.
			 * http://magento-forum.ru/topic/4572/
			 * По этой причине нам надо добавить к коллекции разделы-предки.
			 */
			$result->addAncestors();
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Catalog_Model_Category[] */
	public function getCategoriesAsTree() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Catalog_Model_Category[] $result */
			$result = array();
			/** @var int $rootCategoryId */
			$rootCategoryId = (int)$this->store()->getRootCategoryId();
			foreach ($this->getCategories() as $category) {
				/** @var Df_Catalog_Model_Category $category */
				/** @var int $parentId */
				$parentId = $category->getParentId();
				if ($rootCategoryId === $parentId) {
					$result[]= $category;
				}
				else if ($parentId) {
					/** @var Df_Catalog_Model_Category $parent */
					$parent = $this->getCategories()->getItemById($parentId);
					/** @var Df_Catalog_Model_Category[] $children */
					$children = df_nta($parent->getData(self::CATEGORY__CHILDREN));
					$children[]= $category;
					$parent->setData(self::CATEGORY__CHILDREN, $children);
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by Df_Catalog_Model_XmlExport_Product::getConfugurableParents()
	 * @return Df_Catalog_Model_Resource_Product_Collection
	 */
	public function getProducts() {return $this->cfg(self::$P__PRODUCTS);}

	/**
	 * @used-by \Df\C1\Cml2\Export\Processor\Catalog\Attribute\Url::getЗначение()
	 * @see \Df\C1\Cml2\Export\Processor\Catalog\Attribute
	 * @param Df_Catalog_Model_Product $product
	 * @return Df_Catalog_Model_XmlExport_Product
	 */
	public function getProcessorForProduct(Df_Catalog_Model_Product $product) {
		if (!isset($this->{__METHOD__}[$product->getId()])) {
			$this->{__METHOD__}[$product->getId()] = Df_Catalog_Model_XmlExport_Product::ic(
				$this->getProcessorClass_products(), $product, $this
			);
		}
		return $this->{__METHOD__}[$product->getId()];
	}

	/** @return string */
	protected function getProcessorClass_attributes() {df_abstract($this); return null;}

	/**
	 * Класс-потомок должен вернуть true, если он намерен обновлять товарные разделы.
	 * Для включения возможности обновления надо надо перед загрузкой коллекции
	 * отключить режим денормализации:
	 * @used-by Df_Catalog_Model_XmlExport_Catalog::getCategories()
	 * @return bool
	 */
	protected function needUpdateCategories() {return false;}

	/** @return array(array(string => mixed)) */
	protected function getOutput_Products() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(array(string => mixed)) $result  */
			$result = array();
			$this->log('Рассматривается товаров: %d.', $this->getProducts()->count());
			foreach ($this->getProducts() as $product) {
				/** @var Df_Catalog_Model_Product $product */
				/** @var Df_Catalog_Model_XmlExport_Product $processor */
				$processor = $this->getProcessorForProduct($product);
				if ($processor->isEligible()) {
					$result[]= $processor->getResult();
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__PRODUCTS, Df_Catalog_Model_Resource_Product_Collection::class);
	}
	/**
	 * @used-by Df_Catalog_Model_XmlExport_Category::_construct()
	 * @used-by Df_Catalog_Model_XmlExport_Product::_construct()
	 */

	const CATEGORY__CHILDREN = 'rm_xml_export__children';

	/** @var string */
	private static $P__PRODUCTS = 'products';

	/**
	 * @used-by \Df\C1\Cml2\Export\Document\Catalog::i()
	 * @param string $class
	 * @param Df_Catalog_Model_Resource_Product_Collection $products
	 * @return Df_Catalog_Model_XmlExport_Catalog
	 */
	protected static function ic($class, Df_Catalog_Model_Resource_Product_Collection $products) {
		return df_ic($class, __CLASS__, array(self::$P__PRODUCTS => $products));
	}
}