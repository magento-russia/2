<?php
/**
 * @method Df_Catalog_Model_Event_Category_Collection_Load_Before getEvent()
 */
class Df_AccessControl_Model_Handler_Catalog_Category_Collection_ExcludeForbiddenCategories
	extends Df_Core_Model_Handler {
	/**
	 * Метод-обработчик события
	 * @override
	 * @return void
	 */
	public function handle() {
		if (
				df_enabled(Df_Core_Feature::ACCESS_CONTROL)
			&&
				df_cfg()->admin()->access_control()->getEnabled()
			&&
				!is_null(df_h()->accessControl()->getCurrentRole())
		) {
			if (df_h()->accessControl()->getCurrentRole()->isModuleEnabled()) {
				/**
				 * Добавляем фильтр по разрешённым товарным разделам.
				 */
				/** @var Mage_Catalog_Model_Resource_Category_Collection|Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection $collection */
				$collection = $this->getEvent()->getCollection();
				if (
					!$collection
						->hasFlag(
							Df_AccessControl_Model_Handler_Catalog_Category_Collection_ExcludeForbiddenCategories
								::DISABLE_PROCESSING
						)
				) {
					$collection
						->addIdFilter(
						df_h()->accessControl()->getCurrentRole()->getCategoryIdsWithAncestors()
						)
					;
				}
			}
		}
	}

	/**
	 * Класс события (для валидации события)
	 * @override
	 * @return string
	 */
	protected function getEventClass() {
		return Df_Catalog_Model_Event_Category_Collection_Load_Before::_CLASS;
	}

	const _CLASS = __CLASS__;
	const DISABLE_PROCESSING = 'disable_processing';
}