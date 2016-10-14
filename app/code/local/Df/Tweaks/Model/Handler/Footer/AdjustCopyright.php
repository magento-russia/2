<?php
/** @method Df_Core_Model_Event_Controller_Action_Layout_GenerateBlocksAfter getEvent() */
class Df_Tweaks_Model_Handler_Footer_AdjustCopyright extends Df_Core_Model_Handler {
	/**
	 * Метод-обработчик события
	 * @override
	 * @return void
	 */
	public function handle() {
		if (
				df_cfg()->tweaks()->footer()->needUpdateYearInCopyright()
			&&
				!is_null($this->getBlock())
		) {
			$this->getBlock()
				->setCopyright(
					strtr(
						$this->getBlock()->getCopyright()
						,array('{currentYear}' => df_dts(Zend_Date::now(), Zend_Date::YEAR))
					)
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

	/** @return Mage_Page_Block_Html_Footer|null */
	private function getBlock() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(
				rm_empty_to_null($this->getEvent()->getLayout()->getBlock('footer'))
			);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @used-by Df_Tweaks_Observer::controller_action_layout_generate_blocks_after() */
	const _C = __CLASS__;
}