<?php
class Df_Catalog_Model_Resource_Product_Flat_Indexer
	extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Flat_Indexer {
	/**
	 * Цель перекрытия —
	 * кэширование товарных свойств при включенном режиме денормализации.
	 * @override
	 * @return string[]
	 */
	public function getAttributeCodes() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $cacheKey */
			$cacheKey = Df_Eav_Model_Cache::s()->makeKey(__METHOD__);
			/** @var string[]|bool $result */
			$result = Df_Eav_Model_Cache::s()->loadDataArray($cacheKey);
			if (false === $result) {
				$result = parent::getAttributeCodes();
				Df_Eav_Model_Cache::s()->saveDataArray($cacheKey, $result);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}
}


 