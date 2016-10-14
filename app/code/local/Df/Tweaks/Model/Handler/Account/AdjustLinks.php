<?php
/** @method Df_Core_Model_Event_Controller_Action_Layout_GenerateBlocksAfter getEvent() */
class Df_Tweaks_Model_Handler_Account_AdjustLinks extends Df_Core_Model_Handler {
	/**
	 * Метод-обработчик события
	 * @override
	 * @return void
	 */
	public function handle() {
		/**
		 * Обратите внимание, что мы не вынесли условие !is_null($this->getBlock()
		 * вверх, потому что не хотим, чтобы его программный код исполнялся
		 * при отключенных функциях модуля Df_Tweaks
		 */
		if (
				df_cfg()->tweaks()->account()->removeSectionApplications()
			&&
				!is_null($this->getBlock())
		) {
			$this->getBlock()->removeLinkByPath('oauth/customer_token');
		}
		if (
				df_cfg()->tweaks()->account()->removeSectionBillingAgreements()
			&&
				!is_null($this->getBlock())
		) {
			$this->getBlock()->removeLinkByPath('sales/billing_agreement/');
		}
		if (
				df_cfg()->tweaks()->account()->removeSectionDownloadableProducts()
			&&
				!is_null($this->getBlock())
		) {
			$this->getBlock()->removeLinkByPath('downloadable/customer/products');
		}
		if (
				df_cfg()->tweaks()->account()->removeSectionNewsletterSubscriptions()
			&&
				!is_null($this->getBlock())
		) {
			$this->getBlock()
				->removeLinkByPath('newsletter/manage/')
				->removeLinkByPath('newsletter/manage')
			;
		}
		if (
				df_cfg()->tweaks()->account()->removeSectionProductReviews()
			&&
				!is_null($this->getBlock())
		) {
			$this->getBlock()->removeLinkByPath('review/customer');
		}
		if (
				df_cfg()->tweaks()->account()->removeSectionRecurringProfiles()
			&&
				!is_null($this->getBlock())
		) {
			$this->getBlock()->removeLinkByPath('sales/recurring_profile/');
		}
		if (
				df_cfg()->tweaks()->account()->removeSectionTags()
			&&
				!is_null($this->getBlock())
		) {
			$this->getBlock()->removeLinkByPath('tag/customer/');
		}
		if (
				df_cfg()->tweaks()->account()->removeSectionWishlist()
			&&
				!is_null($this->getBlock())
		) {
			$this->getBlock()->removeLinkByPath('wishlist/');
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

	/** @return Df_Customer_Block_Account_Navigation|null */
	private function getBlock() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Customer_Block_Account_Navigation|null $result */
			$result = $this->getEvent()->getLayout()->getBlock('customer_account_navigation');
			if (
				/**
				 * Раньше тут просто стояла проверка на равенство false.
				 * Однако в магазине могут быть установлены сторонние модули,
				 * которые перекрывают класс Mage_Customer_Block_Account_Navigation
				 * своим классом вместо нашего Df_Customer_Block_Account_Navigation,
				 * и нам в этом случае вовсе необязательно падать:
				 * вместо этого функция просто будет отключена.
				 */
				!($result instanceof Df_Customer_Block_Account_Navigation)
			) {
				$result = null;
			}
			$this->{__METHOD__} = rm_n_set($result);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @used-by Df_Tweaks_Observer::controller_action_layout_generate_blocks_after() */
	const _C = __CLASS__;
}