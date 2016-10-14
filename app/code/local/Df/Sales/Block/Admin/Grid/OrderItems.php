<?php
class Df_Sales_Block_Admin_Grid_OrderItems extends Df_Admin_Block_Grid_ColumnRender {
	/** @return string */
	public function getFieldNameWidthAsString() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->formatWidthForCss(
					$this->settings()->getNameWidth()
					* rm_01($this->settings()->needShowName())
					* $this->widthNormalizationRatio()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getFieldQtyWidthAsString() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->formatWidthForCss(
					$this->settings()->getQtyWidth()
					* rm_01($this->settings()->needShowQty())
					* $this->widthNormalizationRatio()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getFieldSkuWidthAsString() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->formatWidthForCss(
					$this->settings()->getSkuWidth()
					* rm_01($this->settings()->needShowSku())
					* $this->widthNormalizationRatio()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Sales_Block_Admin_Grid_OrderItem_Collection */
	public function getProducts() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Sales_Block_Admin_Grid_OrderItem_Collection $result */
			$result = Df_Sales_Block_Admin_Grid_OrderItem_Collection::i();
			foreach ($this->productsData() as $productData) {
				/** @var array(string => string) $productData */
				df_assert_array($productData);
				$result->addItem(Df_Sales_Block_Admin_Grid_OrderItem::i($productData));
			}
			$result->setOrder(
				$this->settings()->getOrderBy(), $this->settings()->getOrderDirection()
			);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/sales/grid/orderItems.phtml';}

	/**
	 * @override
	 * @return bool
	 */
	protected function needToShow() {return parent::needToShow() && $this->getProducts()->hasItems();}

	/**
	 * @param float $widthAsFloat
	 * @return string
	 */
	private function formatWidthForCss($widthAsFloat) {
		df_param_float($widthAsFloat, 0);
		return number_format($widthAsFloat, 2, '.', '');
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @used-by productsData()
	 * http://php.net/manual/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * http://3v4l.org/OipEQ
	 * @param string $key
	 * @return string[]
	 */
	private function parseConcatenatedValues($key) {
		df_param_string($key, 0);
		return df_csv_parse(
			$this->getRowParam($key)
			, Df_Sales_Model_Handler_AdminOrderGrid_AddProductDataToCollection::T_UNIQUE_SEPARATOR
		);
	}

	/** @return array */
	private function productsData() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => array(string => mixed)) $parsedValues */
			$parsedValues =
				df_array_combine(
					Df_Sales_Block_Admin_Grid_OrderItem::getKeysTarget()
					/** @uses parseConcatenatedValues() */
					,array_map(
						array($this, 'parseConcatenatedValues')
						, Df_Sales_Block_Admin_Grid_OrderItem::getKeysSource()
					)
				)
			;
			/** @var array(int => array(string => mixed)) $result */
			$result = array();
			/** @var int $numProducts */
			$numProducts = count(df_a($parsedValues,
				Df_Sales_Block_Admin_Grid_OrderItem::P__PRODUCT_NAME
			));
			for ($productOrdering = 0; $productOrdering < $numProducts; $productOrdering++) {
				/** @var array(string => mixed) $product */
				$product = array();
				foreach (Df_Sales_Block_Admin_Grid_OrderItem::getKeysTarget() as $key) {
					/** @var string $key */
					df_assert_string($key);
					/** @var array(string => mixed) $attributeValues */
					$attributeValues = df_a($parsedValues, $key);
					df_assert_array($attributeValues);
					$product[$key] = df_a($attributeValues, $productOrdering);
				}
				/** @var int $index */
				$index = rm_nat0(df_a($product, Df_Sales_Block_Admin_Grid_OrderItem::P__ORDER_ITEM_ID));
				$result[$index] = $product;
			}
			$result = $this->removeParents($result);
			df_result_array($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param array(int => array(string => mixed)) $products
	 * @return array(int => array(string => mixed))
	 */
	private function removeParents(array $products) {
		/** @var int[] $idsToRemove */
		$idsToRemove = array();
		/** @var array $result */
		$result = array();
		foreach ($products as $id => $product) {
			/** @var int $id */
			/** @var array(string => mixed) $product */
			df_assert_integer($id);
			df_assert_array($product);
			/** @var int $parentId */
			$parentId = rm_nat0(df_a(
				$product, Df_Sales_Block_Admin_Grid_OrderItem::COLLECTION_ITEM_PARAM__PARENT_ID
			));
			if (0 !== $parentId) {
				$idsToRemove[]= $parentId;
			}
		}
		foreach ($products as $id => $product) {
			/** @var int $id */
			/** @var array(string => mixed) $product */
			df_assert_integer($id);
			df_assert_array($product);
			if (!in_array($id, $idsToRemove)) {
				$result[$id]= $product;
			}
		}
		df_result_array($result);
		return $result;
	}

	/** @return Df_Sales_Model_Settings_OrderGrid_ProductColumn */
	private function settings() {return Df_Sales_Model_Settings_OrderGrid_ProductColumn::s();}

	/** @return int */
	private function totalWidthPercent() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->settings()->getNameWidth() * rm_01($this->settings()->needShowName())
				+ $this->settings()->getSkuWidth() * rm_01($this->settings()->needShowSku())
				+ $this->settings()->getQtyWidth() * rm_01($this->settings()->needShowQty())
			;
			df_result_integer($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	private function widthNormalizationRatio() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = 100.0 / rm_float($this->totalWidthPercent());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by Df_Sales_Block_Admin_Grid_OrderItemsWrapper::render()
	 * @param Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract $renderer
	 * @param Varien_Object $row
	 * @return string
	 */
	public static function r(
		Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract $renderer, Varien_Object $row
	) {
		return self::rc(__CLASS__, $renderer, $row);
	}
}