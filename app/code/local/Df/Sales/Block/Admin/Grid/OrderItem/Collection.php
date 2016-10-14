<?php
class Df_Sales_Block_Admin_Grid_OrderItem_Collection extends Df_Varien_Data_Collection {
	/**
	 * @override
	 * @param  string $field
	 * @param  string $direction [optional]
	 * @return  Df_Sales_Block_Admin_Grid_OrderItem_Collection
	 */
	public function setOrder($field, $direction = self::SORT_ORDER_DESC) {
		df_param_string($field, 0);
		df_param_string($direction, 1);
		parent::setOrder($field, $direction);
		/** @var string $method */
		$method = df_concat('compareBy', df_t()->camelize($field));
		/**
		 * К сожалению, нельзя здесь для проверки публичности метода использовать @see is_callable(),
		 * потому что наличие @see Varien_Object::__call()
		 * приводит к тому, что @see is_callable всегда возвращает true.
		 * Обратите внимание, что @uses method_exists(), в отличие от @see is_callable(),
		 * не гарантирует публичную доступность метода:
		 * т.е. метод может у класса быть, но вызывать его всё равно извне класса нельзя,
		 * потому что он имеет доступность private или protected.
		 * Пока эта проблема никак не решена.
		 */
		df_assert(method_exists($this, $method));
		/**
		 * @uses compareByName()
		 * @uses compareByRowTotal()
		 * @uses compareByQty()
		 * @uses compareBySku()
		 */
		uasort($this->_items, array($this, $method));
		if (self::SORT_ORDER_DESC === $direction) {
			$this->_items = array_reverse($this->_items, true);
		}
		return $this;
	}

	/**
	 * @override
	 * @see Df_Varien_Data_Collection::itemClass()
	 * @used-by Df_Varien_Data_Collection::addItem()
	 * @return string
	 */
	protected function itemClass() {return Df_Sales_Block_Admin_Grid_OrderItem::_C;}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @used-by setOrder()
	 * @used-by uasort()
	 * http://php.net/manual/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * http://3v4l.org/khmlf
	 * @param Df_Sales_Block_Admin_Grid_OrderItem $product1
	 * @param Df_Sales_Block_Admin_Grid_OrderItem $product2
	 * @return int
	 */
	private function compareByName(
		Df_Sales_Block_Admin_Grid_OrderItem $product1
		,Df_Sales_Block_Admin_Grid_OrderItem $product2
	) {
		return strcmp($product1->getProductName(), $product2->getProductName());
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @used-by setOrder()
	 * @used-by uasort()
	 * http://php.net/manual/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * http://3v4l.org/khmlf
	 * @param Df_Sales_Block_Admin_Grid_OrderItem $product1
	 * @param Df_Sales_Block_Admin_Grid_OrderItem $product2
	 * @return int
	 */
	private function compareByRowTotal(
		Df_Sales_Block_Admin_Grid_OrderItem $product1
		,Df_Sales_Block_Admin_Grid_OrderItem $product2
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
	 * @used-by setOrder()
	 * @used-by uasort()
	 * http://php.net/manual/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * http://3v4l.org/khmlf
	 * @param Df_Sales_Block_Admin_Grid_OrderItem $product1
	 * @param Df_Sales_Block_Admin_Grid_OrderItem $product2
	 * @return int
	 */
	private function compareByQty(
		Df_Sales_Block_Admin_Grid_OrderItem $product1
		,Df_Sales_Block_Admin_Grid_OrderItem $product2
	) {
		return $product1->getProductQty() - $product2->getProductQty();
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @used-by setOrder()
	 * @used-by uasort()
	 * http://php.net/manual/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * http://3v4l.org/khmlf
	 * @param Df_Sales_Block_Admin_Grid_OrderItem $product1
	 * @param Df_Sales_Block_Admin_Grid_OrderItem $product2
	 * @return int
	 */
	private function compareBySku(
		Df_Sales_Block_Admin_Grid_OrderItem $product1
		,Df_Sales_Block_Admin_Grid_OrderItem $product2
	) {
		return strcmp($product1->getProductSku(), $product2->getProductSku());
	}

	const _C = __CLASS__;
	const ORDER_BY__NAME = 'name';
	const ORDER_BY__QTY = 'qty';
	const ORDER_BY__ROW_TOTAL = 'row_total';
	const ORDER_BY__SKU = 'sku';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Sales_Block_Admin_Grid_OrderItem_Collection
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}