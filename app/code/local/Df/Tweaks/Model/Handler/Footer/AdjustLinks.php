<?php
/**
 * @method Df_Core_Model_Event_Controller_Action_Layout_GenerateBlocksAfter getEvent()
 */
class Df_Tweaks_Model_Handler_Footer_AdjustLinks extends Df_Core_Model_Handler {
	/**
	 * Метод-обработчик события
	 * @override
	 * @return void
	 */
	public function handle() {
		/**
		 * Обратите внимание, что мы не вынесли условие !is_null($this->getBlock())
		 * вверх, потому что не хотим, чтобы его программный код исполнялся
		 * при отключенных функциях модуля Df_Tweaks
		 */
		if (
				df_cfg()->tweaks()->footer()->removeAdvancedSearchLink()
			&&
				!is_null($this->getBlock())
		) {
			$this->getBlock()
				->removeLinkByUrl(
					df_mage()->catalogSearchHelper()->getAdvancedSearchUrl()
				)
			;
		}
		if (
				df_cfg()->tweaks()->footer()->removeSearchTermsLink()
			&&
				!is_null($this->getBlock())
		) {
			$this->getBlock()
				->removeLinkByUrl(
					df_mage()->catalogSearchHelper()->getSearchTermUrl()
				)
			;
		}
	}

	/**
	 * Класс события (для валидации события)
	 * @override
	 * @return string
	 */
	protected function getEventClass() {
		return Df_Core_Model_Event_Controller_Action_Layout_GenerateBlocksAfter::_C;
	}

	/** @return Df_Page_Block_Template_Links|null */
	private function getBlock() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Page_Block_Template_Links|null $result */
			$result = rm_empty_to_null($this->getEvent()->getLayout()->getBlock('footer_links'));
			if (!($result instanceof Df_Page_Block_Template_Links)) {
				/** Кто-то перекрыл класс @see Mage_Page_Block_Template_Links */
				$result = null;
			}
			$this->{__METHOD__} = rm_n_set($result);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @used-by Df_Tweaks_Observer::controller_action_layout_generate_blocks_after() */
	const _C = __CLASS__;
}