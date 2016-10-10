<?php
/**
 * @param mixed $sku
 * @return bool
 * @throws Df_Core_Exception_Internal
 */
function df_assert_sku($sku) {
	if (df_enable_assertions()) {
		if (!Df_Catalog_Validate_Sku::s()->isValid($sku)) {
			df_error_internal(Df_Catalog_Validate_Sku::s()->getMessage());
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
		_df_qa_method()->validateParam(
			Df_Catalog_Validate_Sku::s(), $paramValue, $paramOrdering, $stackLevel + 1
		);
	}
}

/**
 * В качестве $id можно передавать не только идентификатор, но и артикул.
 * @param int|string|array(string => mixed) $id [optional]
 * @param int|Mage_Core_Model_Store|string|null $storeId [optional]
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
		  	$result = Df_Catalog_Model_Product::ld($id, rm_store_id($storeId))
		 * потому что нам важно условие !is_null($storeId):
		 * если в качестве $storeId передано null,
		 * то это вовсе не означает, что мы хотим загрузить товар для текущего магазина:
		 * а это означает, что мы хотим загрузить товар с глобальными значениями свойств.
		 */
		if (!is_null($storeId) && !df_check_integer($storeId)) {
			$storeId = rm_store($storeId);
		}
		$result = Df_Catalog_Model_Product::ld($id, $storeId);
	}
	return $result;
}

/**
 * @param array $resultValue
 * @param int $stackLevel[optional]
 * @return void
 * @throws Exception
 */
function df_result_sku($resultValue, $stackLevel = 0) {
	if (df_enable_assertions()) {
		_df_qa_method()->validateResult(Df_Catalog_Validate_Sku::s(), $resultValue, $stackLevel + 1);
	}
}

/**
 * @param string $sku
 * @return string
 */
function df_sku_adapt($sku) {return Df_Catalog_Model_Product_Sku::s()->adapt($sku);}