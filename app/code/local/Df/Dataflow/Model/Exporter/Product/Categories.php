<?php
class Df_Dataflow_Model_Exporter_Product_Categories extends Df_Core_Model_Abstract {
	/** @return string */
	public function process() {
		/** @var string $result */
		$result = '';
		try {
			/** @var string $result */
			$result = $this->encodeAsSimple($this->getCategoriesInExportFormat());
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e, true);
		}
		return $result;
	}

	/**
	 * @param mixed $data
	 * @return  string
	 */
	private function encodeAsJson($data) {
		return
			df_text()->adjustCyrillicInJson(
				/**
				 * Zend_Json::encode использует json_encode при наличии расширения PHP JSON
				 * и свой внутренний кодировщик при отсутствии расширения PHP JSON.
				 * @see Zend_Json::encode
				 * @link http://stackoverflow.com/questions/4402426/json-encode-json-decode-vs-zend-jsonencode-zend-jsondecode
				 * Обратите внимание,
				 * что расширение PHP JSON не входит в системные требования Magento.
				 * @link http://www.magentocommerce.com/system-requirements
				 * Поэтому использование Zend_Json::encode выглядит более правильным, чем json_encode.
				 */
				Zend_Json::encode($data)
			)
		;
	}

	/**
	 * @param array $data
	 * @return string
	 */
	private function encodeAsSimple(array $data) {
		df_param_array($data, 0);
		return implode("\r\n,", array_map(array($this, 'encodePathAsSimple'), $data));
	}

	/** @return string[][] */
	private function getCategoriesInExportFormat() {
		/** @var string[][] $result */
		$result = array();
		foreach ($this->getProduct()->getCategoryCollection() as $category) {
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
		$result = array();
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
		/** @var array $pathIds */
		$pathIds = array_reverse(explode(',', $category->getPathInStore()));
		/** @var Df_Catalog_Model_Resource_Category_Collection $result */
		$result = Df_Catalog_Model_Resource_Category_Collection::i();
		$result
			->setStore(Mage::app()->getStore())
			->addAttributeToSelect('name')
			->addAttributeToSelect('url_key')
			->addFieldToFilter('entity_id', array('in'=>$pathIds))
			->addFieldToFilter('is_active', 1)
			->setOrder('level', 'asc')
		;
		return $result;
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @link http://php.net/manual/en/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * @link http://3v4l.org/OipEQ
	 * @param string[] $path
	 * @return string
	 */
	private function encodePathAsSimple(array $path) {
		df_param_array($path, 0);
		return implode('/', array_map(array($this, 'encodePathPartAsSimple'), $path));
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @link http://php.net/manual/en/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * @link http://3v4l.org/OipEQ
	 *
	 * @param string $pathPart
	 * @return string
	 */
	private function encodePathPartAsSimple($pathPart) {
		df_param_string($pathPart, 0);
		return
			str_replace(
				',' ,'\,'
				,str_replace('/', '\/', $pathPart)
			)
		;
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
	const _CLASS = __CLASS__;
	const P__PRODUCT = 'product';
	const P__PRODUCT_TYPE = 'Mage_Catalog_Model_Product';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Dataflow_Model_Exporter_Product_Categories
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}