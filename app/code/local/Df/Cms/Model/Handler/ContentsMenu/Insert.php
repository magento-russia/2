<?php
/**
 * @method Df_Core_Model_Event_Controller_Action_Layout_GenerateBlocksAfter getEvent()
 */
class Df_Cms_Model_Handler_ContentsMenu_Insert extends Df_Core_Model_Handler {
	/**
	 * @override
	 * @return void
	 */
	public function handle() {
		foreach ($this->getContentsMenus()->getPositions() as $position) {
			/** @var Df_Cms_Model_ContentsMenu_Collection $position */
			/**
			 * Убрал df_assert ради ускорения
			 * (метод Df_Cms_Model_Handler_ContentsMenu_Insert::handle
			 * срабатывает при каждой загрузке страницы)
			 */
			foreach ($position as $contentsMenu) {
				/** @var Df_Cms_Model_ContentsMenu $contentsMenu */
				$contentsMenu->insertIntoLayout();
			}
		}
	}

	/** @return Df_Cms_Model_ContentsMenu_Collection */
	private function getContentsMenus() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Cms_Model_ContentsMenu_Collection $result */
			/** @var string $cacheKey */
			$cacheKey =
				Df_Cms_Model_Cache::s()->makeKey(
					__METHOD__, rm_state()->getController()->getFullActionName()
				)
			;
			$result = Df_Cms_Model_Cache::s()->loadDataComplex($cacheKey);
			if (!$result) {
				$result = Df_Cms_Model_ContentsMenu_Collection::i();
				$result->loadItemsForTheCurrentPage();
				Df_Cms_Model_Cache::s()->saveDataComplex($cacheKey, $result);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Класс события (для валидации события)
	 * @override
	 * @return string
	 */
	protected function getEventClass() {
		return Df_Core_Model_Event_Controller_Action_Layout_GenerateBlocksAfter::_CLASS;
	}

	const _CLASS = __CLASS__;
}