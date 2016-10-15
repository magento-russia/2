<?php
/**
 * @param mixed $sku
 * @return bool
 * @throws \Df\Core\Exception
 */
function df_assert_sku($sku) {
	if (df_enable_assertions()) {
		if (!Df_Catalog_Validate_Sku::s()->isValid($sku)) {
			df_error(Df_Catalog_Validate_Sku::s()->getMessage());
		}
	}
}

/**
 * @param mixed $sku
 * @return bool
 */
function df_check_sku($sku) {return Df_Catalog_Validate_Sku::s()->isValid($sku);}

/**
 * @param int $paramValue
 * @param int $paramOrdering
 * @param int $stackLevel [optional]
 * @return void
 * @throws Exception
 */
function df_param_sku($paramValue, $paramOrdering, $stackLevel = 0) {
	if (df_enable_assertions()) {
		\Df\Qa\Method::validateParam(
			Df_Catalog_Validate_Sku::s(), $paramValue, $paramOrdering, $stackLevel + 1
		);
	}
}

/**
 * В качестве $id можно передавать не только идентификатор, но и артикул.
 * @param int|string|array(string => mixed) $id [optional]
 * @param Df_Core_Model_StoreM|int|string|bool|null $storeId [optional]
 * @return Df_Catalog_Model_Product
 */
function df_product($id = 0, $storeId = null) {
	/** @var Df_Catalog_Model_Product $result */
	$result = null;
	if (!$id) {
		$result = Df_Catalog_Model_Product::createNew();
	}
	else if (is_array($id)) {
		$result = Df_Catalog_Model_Product::createNew();
		$result->addData($id);
	}
	else {
		/**
		 * Обратите внимание, что здесь нельзя упрощать код до
		  	$result = Df_Catalog_Model_Product::ld($id, df_store_id($storeId))
		 * потому что нам важно условие !is_null($storeId):
		 * если в качестве $storeId передано null,
		 * то это вовсе не означает, что мы хотим загрузить товар для текущего магазина:
		 * а это означает, что мы хотим загрузить товар с глобальными значениями свойств.
		 */
		if (!is_null($storeId) && !df_check_integer($storeId)) {
			$storeId = df_store_id($storeId);
		}
		$result = Df_Catalog_Model_Product::ld($id, $storeId);
	}
	return $result;
}

/**
 * @param array $resultValue
 * @param int $stackLevel [optional]
 * @return void
 * @throws Exception
 */
function df_result_sku($resultValue, $stackLevel = 0) {
	if (df_enable_assertions()) {
		\Df\Qa\Method::validateResult(Df_Catalog_Validate_Sku::s(), $resultValue, $stackLevel + 1);
	}
}

/**
 * @param string $sku
 * @return string
 */
function df_sku_adapt($sku) {return Df_Catalog_Model_Product_Sku::s()->adapt($sku);}

/**
 * 2015-03-26
 * @param Df_Catalog_Model_Product|int $product
 * @return int
 */
function rm_product_id($product) {
	return
		$product instanceof Df_Catalog_Model_Product
		? $product->getId()
		: df_nat($product)
	;
}

/**
 * 2015-04-13
 * http://magento.stackexchange.com/a/8202/18793
 * @used-by Df_Tax_Setup_3_0_0::deleteDemoTaxClasses()
 * @param array(string => mixed) $data
 * @param int[] $ids [optional]
 * @param mixed[]|Df_Core_Model_StoreM[] $stores [optional]
 * @return void
 */
function rm_products_update(array $data, array $ids = array(), array $stores = array()) {
	df_admin_begin();
	try {
		// Если витрины не указаны — то обновляем свойства для всех витрин.
		if (!$stores) {
			$stores = Mage::app()->getStores($withDefault = true);
			/**
			 * Если мы попадаем сюда в процессе установки/обновления модуля,
			 * то магазины системы могут быть ещё не инициализированы,
			 * однако переменная @see Mage_Core_Model_App::_stores
			 * может быть уже инициализирована в методе @see Mage_Core_Model_App::getStore(),
			 * и тогда @uses Mage_Core_Model_App::getStores(true) вернёт массив из одного элемента.
			 */
			if (2 > count($stores)) {
				Mage::app()->reinitStores();
				$stores = Mage::app()->getStores($withDefault = true);
			}
		}
		else {
			foreach ($stores as &$store) {
				/** @var mixed $store */
				$store = df_store($store);
			}
		}
		/** @var int[] $websiteIds */
		$websiteIds = array();
		foreach ($stores as $store) {
			/** @var Df_Core_Model_StoreM $store */
			$websiteIds[]= $store->getWebsiteId();
		}
		/** @var array(int => int) $mapFromWebsiteToAllProductIds */
		$mapFromWebsiteToAllProductIds = array();
		foreach ($websiteIds as $websiteId) {
			/** @var int $websiteId */
			if (!isset($mapFromWebsiteToAllProductIds[$websiteId])) {
				$mapFromWebsiteToAllProductIds[$websiteId] = df_fetch_col_int(
					'catalog/product_website', 'product_id', 'website_id', $websiteId
				);
			}
		}
		/** @var array(int => int) $mapFromWebsiteToProductIds */
		if (!$ids) {
			// Если товары не указаны — то обновляем свойства для всех товаров.
			$mapFromWebsiteToProductIds = $mapFromWebsiteToAllProductIds;
		}
		else {
			// Если товары указаны, то нам нужно построить карту соответствия товаров сайтам,
			// потому что конкретный товар может быть не привязан к конкретному сайту,
			// и, соответственно, некорректно обновлять свойство данного товара для витрин данного сайта.
			$ids = df_int_simple($ids);
			$mapFromWebsiteToProductIds = array();
			foreach ($ids as $id) {
				/** @var int $id */
				foreach ($websiteIds as $websiteId) {
					/** @var int $websiteId */
					if (in_array($id, $mapFromWebsiteToAllProductIds[$websiteId])) {
						$mapFromWebsiteToProductIds[$websiteId][]= $id;
					}
				}
			}
		}
		/** @var Mage_Catalog_Model_Resource_Product_Action $action */
		$action = Mage::getResourceSingleton('catalog/product_action');
		foreach ($stores as $store) {
			/** @var Df_Core_Model_StoreM $store */
			/** @var int[]|null $productsByStore */
			$productsByStore = dfa($mapFromWebsiteToProductIds, $store->getWebsiteId());
			// Обратите внимание, что подлежащих обработке товаров для конкретной витрины может не быть
			// в том случае, когда программист вызвал функцию без параметра $stores
			// (ведь в этом случае алгоритм проходит в цикле по всем витринам системы).
			if ($productsByStore) {
				$action->updateAttributes($productsByStore, $data, $store->getId());
			}
		}
	}
	catch (Exception $e) {
		df_admin_end();
		df_error($e);
	}
	df_admin_end();
}

/**
 * 2015-03-26
 * @used-by Autostyler_Import_Model_Action::processRow()
 * @param Df_Catalog_Model_Product|int $product
 * @param int $qty
 * @return void
 */
function rm_stock_update($product, $qty) {
	/** @var int $productId */
	$productId = rm_product_id($product);
	$qty = df_nat0($qty);
	/** @var Df_CatalogInventory_Model_Stock_Item $stockItem */
	$stockItem = Df_CatalogInventory_Model_Stock_Item::i();
	$stockItem->loadByProduct($productId);
	if (!$stockItem->getData()) {
		$stockItem->setData(array(
			'manage_stock' => 1
			,'use_config_manage_stock' => 0
			,'stock_id' => 1
			,'product_id' => $productId
		));
	}
	$stockItem->addData(array('is_in_stock' => !!$qty, 'qty' => $qty));
	$stockItem->save();
}