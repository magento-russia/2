<?php
/**
 * @method Df_Core_Model_Event_Controller_Action_Layout_GenerateBlocksAfter getEvent()
 */
class Df_Tweaks_Model_Handler_AdjustBanners extends Df_Core_Model_Handler {
	/**
	 * Метод-обработчик события
	 * @override
	 * @return void
	 */
	public function handle() {
		/** @var Df_Tweaks_Model_Settings_Banners_Left $settingsLeft */
		$settingsLeft = df_cfg()->tweaks()->banners()->left();
		if (
				$this->needRemove($settingsLeft)
			||
				(
						$settingsLeft->removeFromAccount()
					&&
						rm_handle_presents(Df_Core_Model_Layout_Handle::CUSTOMER_ACCOUNT)
				)
		) {
			df()->layout()->removeBlock('left.permanent.callout');
		}
		if ($this->needRemove(df_cfg()->tweaks()->banners()->right())) {
			df()->layout()->removeBlock('right.permanent.callout');
		}
	}

	/**
	 * Класс события (для валидации события)
	 * @override
	 * @return string
	 */
	protected function getEventClass() {
		return Df_Core_Model_Event_Controller_Action_Layout_GenerateBlocksAfter::_CLASS;
	}

	/**
	 * @param Df_Tweaks_Model_Settings_Banners_Abstract $settings
	 * @return bool
	 */
	private function needRemove(Df_Tweaks_Model_Settings_Banners_Abstract $settings) {
		return
				$settings->removeFromAll()
			||
				(
						$settings->removeFromFrontpage()
					&&
						rm_handle_presents(Df_Core_Model_Layout_Handle::CMS_INDEX_INDEX)
				)
			||
				(
						$settings->removeFromCatalogProductList()
					&&
						rm_handle_presents(Df_Core_Model_Layout_Handle::CATALOG_CATEGORY_VIEW)
				)
			||
				(
						$settings->removeFromCatalogProductView()
					&&
						rm_handle_presents(Df_Core_Model_Layout_Handle::CATALOG_PRODUCT_VIEW)
				)
		;
	}

	const _CLASS = __CLASS__;
}