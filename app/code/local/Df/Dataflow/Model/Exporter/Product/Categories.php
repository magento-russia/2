<?php
class Df_Dataflow_Model_Exporter_Product_Categories extends Df_Core_Model {
	/** @return string */
	public function process() {
		/** @var string $result */
		$result = '';
		try {
			/** @var string $result */
			$result = $this->encodeAsSimple($this->getCategoriesInExportFormat());
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e, true);
		}
		return $result;
	}

	/**
	 * @param mixed $data
	 * @return  string
	 */
	private function encodeAsJson($data) {return df_json_encode_pretty($data);}

	/**
	 * @param array $data
	 * @return string
	 */
	private function encodeAsSimple(array $data) {
		/** @uses encodePathAsSimple() */
		return implode("\n,", array_map(array($this, 'encodePathAsSimple'), $data));
	}

	/** @return string[][] */
	private function getCategoriesInExportFormat() {
		/** @var string[][] $result */
		$result = [];
		foreach ($this->getProduct()->getCategoryCollection() as $category) {
			/** @var Df_Catalog_Model_Category $category */
			$result[]= $this->getCategoryInExportFormat($category);
		}
		return $result;
	}

	/**
	 * @param Df_Catalog_Model_Category $category
	 * @return string[]
	 */
	private function getCategoryInExportFormat(Df_Catalog_Model_Category $category) {
		/** @var string[] $result */
		$result = [];
		foreach ($this->getParentCategories($category) as $ancestor) {
			/** @var Df_Catalog_Model_Category $ancestor */
			/** @var string $ancestorName */
			$ancestorName = $ancestor->getName();
			if (is_null($ancestorName)) {
				// В магазине мир-пряжи.рф встретился безымянный товарный раздел
				continue;
			}
			df_assert_string($ancestorName);
			$result[]= $ancestorName;
		}
		return $result;
	}

	/**
	 * В отличие от стандартного метода Df_Catalog_Model_Category::getParentCategories(),
	 * данный метод упорядочивает родительские разделы в соответствии с их иерархией.
	 * @param Df_Catalog_Model_Category $category
	 * @return Df_Catalog_Model_Resource_Category_Collection
	 */
	private function getParentCategories(Df_Catalog_Model_Category $category) {
		/** @var int[] $pathIds */
		$pathIds = array_reverse(df_csv_parse_int($category->getPathInStore()));
		/** @var Df_Catalog_Model_Resource_Category_Collection $result */
		$result = Df_Catalog_Model_Category::c();
		$result
			->setStore(df_store())
			->addAttributeToSelect('name')
			->addAttributeToSelect('url_key')
			->addFieldToFilter('entity_id', array('in' => $pathIds))
			->addFieldToFilter('is_active', 1)
			->setOrder('level', 'asc')
		;
		return $result;
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @used-by encodeAsSimple()
	 * http://php.net/manual/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * http://3v4l.org/OipEQ
	 * @param string[] $path
	 * @return string
	 */
	private function encodePathAsSimple(array $path) {
		/** @uses encodePathPartAsSimple() */
		return implode('/', array_map(array($this, 'encodePathPartAsSimple'), $path));
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @used-by encodePathAsSimple()
	 * http://php.net/manual/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * http://3v4l.org/OipEQ
	 * @param string $pathPart
	 * @return string
	 */
	private function encodePathPartAsSimple($pathPart) {
		return strtr($pathPart, array('/' => '\/', ',' => '\,'));
	}

	/** @return Mage_Catalog_Model_Product */
	private function getProduct() {return $this->cfg(self::P__PRODUCT);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__PRODUCT, self::P__PRODUCT_TYPE);
	}

	const P__PRODUCT = 'product';
	const P__PRODUCT_TYPE = 'Mage_Catalog_Model_Product';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Dataflow_Model_Exporter_Product_Categories
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}