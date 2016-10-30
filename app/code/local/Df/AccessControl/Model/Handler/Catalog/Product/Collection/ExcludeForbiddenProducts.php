<?php
/**
 * @method Df_Catalog_Model_Event_Product_Collection_Load_Before getEvent()
 */
class Df_AccessControl_Model_Handler_Catalog_Product_Collection_ExcludeForbiddenProducts
	extends Df_Core_Model_Handler {
	/**
	 * Метод-обработчик события
	 * @override
	 * @return void
	 */
	public function handle() {
		/** @var bool $needHandle */
		static $needHandle;
		if (is_null($needHandle)) {
			$needHandle =
				Df_AccessControl_Settings::s()->getEnabled()
				&& df_h()->accessControl()->getCurrentRole()
			;
		}
		if ($needHandle && df_h()->accessControl()->getCurrentRole()->isModuleEnabled()) {
			// Добавляем фильтр по разрешённым товарным разделам.
			/** @var Df_Catalog_Model_Resource_Product_Collection $collection */
			$collection = $this->getEvent()->getCollection();
			if ($collection instanceof Df_Catalog_Model_Resource_Product_Collection) {
				/** @var Df_Catalog_Model_Resource_Product_Collection $collection */
				$collection->addCategoriesFilter(df_h()->accessControl()->getCurrentRole()->getCategoryIds());
			}
		}
	}

	/**
	 * Класс события (для валидации события)
	 * @override
	 * @return string
	 */
	protected function getEventClass() {return Df_Catalog_Model_Event_Product_Collection_Load_Before::class;}

	/** @used-by Df_AccessControl_Observer::catalog_product_collection_load_before() */

}