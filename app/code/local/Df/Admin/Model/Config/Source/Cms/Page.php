<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Admin_Model_Config_Source_Cms_Page extends Df_Admin_Model_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return $this->getPages()->toOptionIdArray();
	}

	/** @return Df_Cms_Model_Resource_Page_Collection */
	private function getPages() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Cms_Model_Page::c();
			$this->{__METHOD__}
				->addFilter(Df_Cms_Model_Page::P__IS_ACTIVE, Df_Cms_Model_Page::STATUS_ENABLED)
				->setOrder(Df_Cms_Model_Page::P__TITLE, Varien_Data_Collection_Db::SORT_ORDER_ASC)
			;
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
	/** @return Df_Admin_Model_Config_Source_Cms_Page */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}