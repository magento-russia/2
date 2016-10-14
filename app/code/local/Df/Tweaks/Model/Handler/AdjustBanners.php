<?php
/** @method Df_Core_Model_Event_Controller_Action_Layout_GenerateBlocksAfter getEvent() */
class Df_Tweaks_Model_Handler_AdjustBanners extends Df_Core_Model_Handler {
	/**
	 * Метод-обработчик события
	 * @override
	 * @return void
	 */
	public function handle() {
		/** @var Df_Tweaks_Model_Settings_Banners_Left $s */
		$s = Df_Tweaks_Model_Settings_Banners_Left::s();
		if ($this->needRemove($s)
			|| $s->removeFromAccount() && df_handle(Df_Core_Model_Layout_Handle::CUSTOMER_ACCOUNT)
		) {
			df_block_remove('left.permanent.callout');
		}
		if ($this->needRemove(Df_Tweaks_Model_Settings_Banners_Right::s())) {
			df_block_remove('right.permanent.callout');
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

	/**
	 * @param Df_Tweaks_Model_Settings_Banners_Abstract $s
	 * @return bool
	 */
	private function needRemove(Df_Tweaks_Model_Settings_Banners_Abstract $s) {
		return
				$s->removeFromAll()
			||
				(
						$s->removeFromFrontpage()
					&&
						df_handle(Df_Core_Model_Layout_Handle::CMS_INDEX_INDEX)
				)
			||
				(
						$s->removeFromCatalogProductList()
					&&
						df_handle(Df_Core_Model_Layout_Handle::CATALOG_CATEGORY_VIEW)
				)
			||
				(
						$s->removeFromCatalogProductView()
					&&
						df_handle(Df_Core_Model_Layout_Handle::CATALOG_PRODUCT_VIEW)
				)
		;
	}

	/** @used-by Df_Tweaks_Observer::controller_action_layout_generate_blocks_after() */
	const _C = __CLASS__;
}