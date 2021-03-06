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
		if (!isset($needHandle)) {
			$needHandle =
					df_enabled(Df_Core_Feature::ACCESS_CONTROL)
				&&
					df_cfg()->admin()->access_control()->getEnabled()
				&&
					df_h()->accessControl()->getCurrentRole()
			;
		}
		if ($needHandle && df_h()->accessControl()->getCurrentRole()->isModuleEnabled()) {
			// Добавляем фильтр по разрешённым товарным разделам.
			/** @var Df_Catalog_Model_Resource_Product_Collection|Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection */
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
	protected function getEventClass() {return Df_Catalog_Model_Event_Product_Collection_Load_Before::_CLASS;}

	const _CLASS = __CLASS__;
}