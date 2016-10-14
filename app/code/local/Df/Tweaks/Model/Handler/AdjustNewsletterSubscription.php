<?php
/** @method Df_Core_Model_Event_Controller_Action_Layout_GenerateBlocksAfter getEvent() */
class Df_Tweaks_Model_Handler_AdjustNewsletterSubscription extends Df_Core_Model_Handler {
	/**
	 * Метод-обработчик события
	 * @override
	 * @return void
	 */
	public function handle() {
		/** @var Df_Tweaks_Model_Settings_NewsletterSubscription $s */
		$s = Df_Tweaks_Model_Settings_NewsletterSubscription::s();
		if (
				$s->removeFromAll()
			||
				(
						$s->removeFromFrontpage()
					&&
						rm_handle_presents(Df_Core_Model_Layout_Handle::CMS_INDEX_INDEX)
				)
			||
				(
						$s->removeFromCatalogProductList()
					&&
						rm_handle_presents(Df_Core_Model_Layout_Handle::CATALOG_CATEGORY_VIEW)
				)
			||
				(
						$s->removeFromCatalogProductView()
					&&
						rm_handle_presents(Df_Core_Model_Layout_Handle::CATALOG_PRODUCT_VIEW)
				)
			||
				(
						$s->removeFromAccount()
					&&
						rm_handle_presents(Df_Core_Model_Layout_Handle::CUSTOMER_ACCOUNT)
				)
		) {
			rm_block_remove('left.newsletter', 'newsletter');
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

	/** @used-by Df_Tweaks_Observer::controller_action_layout_generate_blocks_after() */
	const _C = __CLASS__;
}