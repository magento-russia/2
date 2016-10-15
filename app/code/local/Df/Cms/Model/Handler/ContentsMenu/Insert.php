<?php
/** @method Df_Core_Model_Event_Controller_Action_Layout_GenerateBlocksAfter getEvent() */
class Df_Cms_Model_Handler_ContentsMenu_Insert extends Df_Core_Model_Handler {
	/**
	 * @uses Df_Cms_Model_ContentsMenu_Collection::walk()
	 * @uses Df_Cms_Model_ContentsMenu::insertIntoLayout()
	 * @override
	 * @return void
	 */
	public function handle() {
		$this->getContentsMenus()->getPositions()->walk('walk', array('insertIntoLayout'));
	}

	/** @return Df_Cms_Model_ContentsMenu_Collection */
	private function getContentsMenus() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Cms_Model_ContentsMenu_Collection $result */
			/** @var string $cacheKey */
			$cacheKey = Df_Cms_Model_Cache::s()->makeKey(__METHOD__, df_action_name());
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
		return Df_Core_Model_Event_Controller_Action_Layout_GenerateBlocksAfter::class;
	}

	/** @used-by Df_Cms_Observer::controller_action_layout_generate_blocks_after() */

}