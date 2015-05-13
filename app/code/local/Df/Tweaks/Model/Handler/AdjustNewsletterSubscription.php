<?php
/**
 * @method Df_Core_Model_Event_Controller_Action_Layout_GenerateBlocksAfter getEvent()
 */
class Df_Tweaks_Model_Handler_AdjustNewsletterSubscription extends Df_Core_Model_Handler {
	/**
	 * Метод-обработчик события
	 * @override
	 * @return void
	 */
	public function handle() {
		if (
				df_cfg()->tweaks()->newsletter()->subscription()->removeFromAll()
			||
				(
						df_cfg()->tweaks()->newsletter()->subscription()->removeFromFrontpage()
					&&
						rm_handle_presents(Df_Core_Model_Layout_Handle::CMS_INDEX_INDEX)
				)
			||
				(
						df_cfg()->tweaks()->newsletter()->subscription()->removeFromCatalogProductList()
					&&
						rm_handle_presents(Df_Core_Model_Layout_Handle::CATALOG_CATEGORY_VIEW)
				)
			||
				(
						df_cfg()->tweaks()->newsletter()->subscription()->removeFromCatalogProductView()
					&&
						rm_handle_presents(Df_Core_Model_Layout_Handle::CATALOG_PRODUCT_VIEW)
				)
			||
				(
						df_cfg()->tweaks()->newsletter()->subscription()->removeFromAccount()
					&&
						rm_handle_presents(Df_Core_Model_Layout_Handle::CUSTOMER_ACCOUNT)
				)
		) {
			df()->layout()
				->removeBlock('left.newsletter')
				->removeBlock('newsletter')
			;
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

	const _CLASS = __CLASS__;
}