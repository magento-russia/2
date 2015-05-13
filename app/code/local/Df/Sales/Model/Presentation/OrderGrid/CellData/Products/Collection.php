<?php
class Df_Sales_Model_Presentation_OrderGrid_CellData_Products_Collection
	extends Df_Varien_Data_Collection {
	/**
	 * @override
	 * @param  string $field
	 * @param  string $direction[optional]
	 * @return  Df_Sales_Model_Presentation_OrderGrid_CellData_Products_Collection
	 */
	public function setOrder($field, $direction = self::SORT_ORDER_DESC) {
		df_param_string($field, 0);
		df_param_string($direction, 1);
		parent::setOrder($field, $direction);
		/** @var string $method */
		$method = df_concat('compareBy', df_text()->camelize($field));
		df_assert(
			/**
			 * К сожалению, нельзя здесь для проверки публичности метода
			 * использовать is_callable,
			 * потому что наличие Varien_Object::__call
			 * приводит к тому, что is_callable всегда возвращает true.
			 */
			method_exists($this, $method)
		);
		uasort($this->_items, array($this, $method));
		if (self::SORT_ORDER_DESC === $direction) {
			$this->_items = array_reverse($this->_items, true);
		}
		return $this;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getItemClass() {
		return Df_Sales_Block_Admin_Widget_Grid_Column_RendererDf_Products_Product::_CLASS;
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @link http://php.net/manual/en/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * @link http://3v4l.org/khmlf
	 *
	 * @param Df_Sales_Block_Admin_Widget_Grid_Column_RendererDf_Products_Product $product1
	 * @param Df_Sales_Block_Admin_Widget_Grid_Column_RendererDf_Products_Product $product2
	 * @return int
	 */
	private function compareByName(
		Df_Sales_Block_Admin_Widget_Grid_Column_RendererDf_Products_Product $product1
		,Df_Sales_Block_Admin_Widget_Grid_Column_RendererDf_Products_Product $product2
	) {
		return strcmp($product1->getProductName(), $product2->getProductName());
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @link http://php.net/manual/en/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * @link http://3v4l.org/khmlf
	 *
	 * @param Df_Sales_Block_Admin_Widget_Grid_Column_RendererDf_Products_Product $product1
	 * @param Df_Sales_Block_Admin_Widget_Grid_Column_RendererDf_Products_Product $product2
	 * @return int
	 */
	private function compareByRowTotal(
		Df_Sales_Block_Admin_Widget_Grid_Column_RendererDf_Products_Product $product1
		,Df_Sales_Block_Admin_Widget_Grid_Column_RendererDf_Products_Product $product2
	) {
		/** @var int $result */
		$result = $product1->getRowTotal() - $product2->getRowTotal();
		df_result_integer($result);
		return $result;
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @link http://php.net/manual/en/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * @link http://3v4l.org/khmlf
	 *
	 * @param Df_Sales_Block_Admin_Widget_Grid_Column_RendererDf_Products_Product $product1
	 * @param Df_Sales_Block_Admin_Widget_Grid_Column_RendererDf_Products_Product $product2
	 * @return int
	 */
	private function compareByQty(
		Df_Sales_Block_Admin_Widget_Grid_Column_RendererDf_Products_Product $product1
		,Df_Sales_Block_Admin_Widget_Grid_Column_RendererDf_Products_Product $product2
	) {
		return $product1->getProductQty() - $product2->getProductQty();
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @link http://php.net/manual/en/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * @link http://3v4l.org/khmlf
	 *
	 * @param Df_Sales_Block_Admin_Widget_Grid_Column_RendererDf_Products_Product $product1
	 * @param Df_Sales_Block_Admin_Widget_Grid_Column_RendererDf_Products_Product $product2
	 * @return int
	 */
	private function compareBySku(
		Df_Sales_Block_Admin_Widget_Grid_Column_RendererDf_Products_Product $product1
		,Df_Sales_Block_Admin_Widget_Grid_Column_RendererDf_Products_Product $product2
	) {
		return strcmp($product1->getProductSku(), $product2->getProductSku());
	}

	const _CLASS = __CLASS__;
	const ORDER_BY__NAME = 'name';
	const ORDER_BY__QTY = 'qty';
	const ORDER_BY__ROW_TOTAL = 'row_total';
	const ORDER_BY__SKU = 'sku';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Sales_Model_Presentation_OrderGrid_CellData_Products_Collection
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}