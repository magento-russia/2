<?php
class Df_Sales_Block_Admin_Widget_Grid_Column_RendererDf_Products
	extends Df_Adminhtml_Block_Widget_Grid_Column_RendererDf {
	/** @return string */
	public function getFieldNameWidthAsString() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->formatWidthForCss(
						$this->getSettings()->getNameWidth()
					*
						rm_01($this->getSettings()->needShowName())
					*
						$this->getWidthNormalizationRatio()
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
						$this->getSettings()->getQtyWidth()
					*
						rm_01($this->getSettings()->needShowQty())
					*
						$this->getWidthNormalizationRatio()
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
						$this->getSettings()->getSkuWidth()
					*
						rm_01($this->getSettings()->needShowSku())
					*
						$this->getWidthNormalizationRatio()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Sales_Model_Presentation_OrderGrid_CellData_Products_Collection */
	public function getProducts() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Sales_Model_Presentation_OrderGrid_CellData_Products_Collection $result */
			$result = Df_Sales_Model_Presentation_OrderGrid_CellData_Products_Collection::i();
			foreach ($this->getProductsData() as $productData) {
				/** @var array(string => string) $productData */
				df_assert_array($productData);
				$result->addItem(
					Df_Sales_Block_Admin_Widget_Grid_Column_RendererDf_Products_Product::i(
						$productData
					)
				);
			}
			$result->setOrder(
				$this->getSettings()->getOrderBy()
				,$this->getSettings()->getOrderDirection()
			);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getDefaultTemplate() {return self::DEFAULT_TEMPLATE;}

	/**
	 * @override
	 * @return bool
	 */
	protected function needToShow() {return parent::needToShow() && (0 < count($this->getProducts()));}

	/**
	 * @param float $widthAsFloat
	 * @return string
	 */
	private function formatWidthForCss($widthAsFloat) {
		df_param_float($widthAsFloat, 0);
		return number_format($widthAsFloat, 2, '.', '');
	}
	
	/** @return string[] */
	private function getKeysSource() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array_keys($this->getMapFromSourceKeysToTargetKeys());
		}
		return $this->{__METHOD__};
	}
	
	/** @return mixed[] */
	private function getKeysTarget() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array_values($this->getMapFromSourceKeysToTargetKeys());
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => string) */
	private function getMapFromSourceKeysToTargetKeys() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				array(
					Df_Sales_Model_Handler_AdminOrderGrid_AddProductDataToCollection
						::COLLECTION_ITEM_PARAM__DF_NAMES
						=>
					Df_Sales_Block_Admin_Widget_Grid_Column_RendererDf_Products_Product
						::P__PRODUCT_NAME
					,Df_Sales_Model_Handler_AdminOrderGrid_AddProductDataToCollection
								::COLLECTION_ITEM_PARAM__DF_SKUS
						=>
							Df_Sales_Block_Admin_Widget_Grid_Column_RendererDf_Products_Product
								::P__PRODUCT_SKU
					,Df_Sales_Model_Handler_AdminOrderGrid_AddProductDataToCollection
								::COLLECTION_ITEM_PARAM__DF_QTYS
						=>
							Df_Sales_Block_Admin_Widget_Grid_Column_RendererDf_Products_Product
								::P__PRODUCT_QTY
					,Df_Sales_Model_Handler_AdminOrderGrid_AddProductDataToCollection
								::COLLECTION_ITEM_PARAM__DF_TOTALS
						=>
							Df_Sales_Block_Admin_Widget_Grid_Column_RendererDf_Products_Product
								::P__ROW_TOTAL
					,Df_Sales_Model_Handler_AdminOrderGrid_AddProductDataToCollection
								::COLLECTION_ITEM_PARAM__DF_PRODUCT_IDS
						=>
							Df_Sales_Block_Admin_Widget_Grid_Column_RendererDf_Products_Product
								::P__PRODUCT_ID
					,Df_Sales_Model_Handler_AdminOrderGrid_AddProductDataToCollection
								::COLLECTION_ITEM_PARAM__DF_ORDER_ITEM_IDS
						=>
							Df_Sales_Block_Admin_Widget_Grid_Column_RendererDf_Products_Product
								::P__ORDER_ITEM_ID
					,Df_Sales_Model_Handler_AdminOrderGrid_AddProductDataToCollection
								::COLLECTION_ITEM_PARAM__DF_PARENTS
						=>
							self::COLLECTION_ITEM_PARAM__PARENT_ID
				)			
			;
		}
		return $this->{__METHOD__};
	}

	/** @return array */
	private function getProductsData() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => array(string => mixed)) $parsedValues */
			$parsedValues =
				df_array_combine(
					$this->getKeysTarget()
					,array_map(array($this, 'parseConcatenatedValues'), $this->getKeysSource())
				)
			;
			/** @var array(int => array(string => mixed)) $result */
			$result = array();
			/** @var int $numProducts */
			$numProducts =
				count(
					df_a(
						$parsedValues
						,Df_Sales_Block_Admin_Widget_Grid_Column_RendererDf_Products_Product
							::P__PRODUCT_NAME
					)
				)
			;
			for($productOrdering = 0; $productOrdering < $numProducts; $productOrdering++) {
				/** @var array(string => mixed) $product */
				$product = array();
				foreach ($this->getKeysTarget() as $key) {
					/** @var string $key */
					df_assert_string($key);
					/** @var array(string => mixed) $attributeValues */
					$attributeValues = df_a($parsedValues, $key);
					df_assert_array($attributeValues);
					$product[$key] = df_a($attributeValues, $productOrdering);
				}
				/** @var int $index */
				$index =
					rm_nat0(
						df_a(
							$product
							,Df_Sales_Block_Admin_Widget_Grid_Column_RendererDf_Products_Product
								::P__ORDER_ITEM_ID
						)
					)
				;
				$result[$index] = $product;
			}
			$result = $this->removeParents($result);
			df_result_array($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Sales_Model_Settings_OrderGrid_ProductColumn */
	private function getSettings() {return df_cfg()->sales()->orderGrid()->productColumn();}

	/** @return int */
	private function getTotalWidthPercent() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
						$this->getSettings()->getNameWidth()
					*
						rm_01($this->getSettings()->needShowName())
				+
						$this->getSettings()->getSkuWidth()
					*
						rm_01($this->getSettings()->needShowSku())
				+
						$this->getSettings()->getQtyWidth()
					*
						rm_01($this->getSettings()->needShowQty())
			;
			df_result_integer($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	private function getWidthNormalizationRatio() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = 100.0 / rm_float($this->getTotalWidthPercent());
		}
		return $this->{__METHOD__};
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @link http://php.net/manual/en/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * @link http://3v4l.org/OipEQ
	 *
	 * @param string $key
	 * @return string[]
	 */
	private function parseConcatenatedValues($key) {
		df_param_string($key, 0);
		return df_parse_csv($this->getRowParam($key), Df_Core_Const::T_UNIQUE_SEPARATOR);
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
			$parentId = rm_nat0(df_a($product, self::COLLECTION_ITEM_PARAM__PARENT_ID));
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

	const _CLASS = __CLASS__;
	const COLLECTION_ITEM_PARAM__PARENT_ID = 'parent_id';
	const DEFAULT_TEMPLATE = 'df/sales/widget/grid/column/renderer/products.phtml';
	/**
	 * @param Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract $originalRenderer
	 * @param Varien_Object $row
	 * @return Df_Sales_Block_Admin_Widget_Grid_Column_RendererDf_Products
	 */
	public static function i(
		Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract $originalRenderer, Varien_Object $row
	) {
		return df_block(new self(array(
			self::P__ORIGINAL_RENDERER => $originalRenderer, self::P__ROW => $row
		)));
	}
}