<?php
/** @method Df_Core_Model_Event_Controller_Action_Layout_GenerateBlocksAfter getEvent() */
class Df_Tweaks_Model_Handler_Header_AdjustLinks extends Df_Core_Model_Handler {
	/**
	 * Метод-обработчик события
	 * @override
	 * @return void
	 */
	public function handle() {
		/** @var Df_Tweaks_Model_Settings_Header $config */
		$config = df_cfg()->tweaks()->header();
		/**
		 * @TODO ЗДЕСЬ НАДО ДОБАВИТЬ В ПОКУПАТЕЛЬСКОЕ МЕНЮ НОВЫЕ ССЫЛКИ,
		 * ОПИСАННЫЕ В НАСТРОЕЧНЫХ ФАЙЛАХ
		 */
		/**
		 * Обратите внимание, что мы не вынесли условие !is_null($this->getBlock()
		 * вверх, потому что не хотим, чтобы его программный код исполнялся
		 * при отключенных функциях модуля Df_Tweaks
		 */
		if (
				$config->hideAccountLinkFromAnonymousShopper()
			&&
				$this->getBlock()
			&&
				!df_customer_logged_in()
		) {
			$this->getBlock()->removeLinkByUrl($this->getBlock()->getUrl('customer/account'));
		}
		if (
				$config->replaceAccountLinkTitleWithCustomerName()
			&&
				$this->getBlock()
			&&
				/**
				 * Данная функция имеет смысл только для авторизованных покупателей.
				 * Отказ от вызова метода
				 * @see Df_Tweaks_Model_Handler_Header_AdjustLinks::replaceAccountLinkTitleWithCustomerName()
				 * для анонимных покупателей ускоряет для таких покупателей работу системы.
				 */
				df_customer_logged_in()
		) {
			$this->replaceAccountLinkTitleWithCustomerName();
		}
		if (
				Df_Admin_Config_Source_HideFromAnonymous::needHide($config->hideWishlistLink())
			&&
				$this->getBlock()
		) {
			$this->getBlock()->removeLinkByBlockType('wishlist/links');
		}
		if ($config->hideCartLink() && $this->getBlock()) {
			$this->getBlock()->removeLinkByUrl($this->getBlock()->getUrl('checkout/cart'));
		}
		if (
				$config->hideCheckoutLink()
			&&
				$this->getBlock()
		) {
			$this->getBlock()->removeLinkByUrl($this->getBlock()->getUrl('checkout'));
		}
	}

	/** @return Df_Tweaks_Model_Handler_Header_AdjustLinks */
	private function replaceAccountLinkTitleWithCustomerName() {
		/** @var string $accountUrl */
		$accountUrl = df_mage()->helper()->getCustomer()->getAccountUrl();
		/** @var string $customerName */
		$customerName =
			df_cfg()->tweaks()->header()->showOnlyFirstName()
			? df_h()->tweaks()->customer()->getFirstNameWithPrefix()
			: df_session_customer()->getCustomer()->getName()
		;
		foreach ($this->getBlock()->getLinks() as $link) {
			/** @var Varien_Object $link */
			if ($accountUrl === $link->getData('url')) {
				$link->addData(array('label' => $customerName, 'title' => $customerName));
			}
		}
		return $this;
	}

	/**
	 * Класс события (для валидации события)
	 * @override
	 * @return string
	 */
	protected function getEventClass() {
		return Df_Core_Model_Event_Controller_Action_Layout_GenerateBlocksAfter::class;
	}

	/** @return Df_Page_Block_Template_Links|null */
	private function getBlock() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Page_Block_Template_Links|null $result */
			$result = $this->getEvent()->getLayout()->getBlock('top.links') ?: null;
			if (!($result instanceof Df_Page_Block_Template_Links)) {
				/** Кто-то перекрыл класс @see Mage_Page_Block_Template_Links */
				$result = null;
			}
			$this->{__METHOD__} = df_n_set($result);
		}
		return df_n_get($this->{__METHOD__});
	}

	/** @used-by Df_Tweaks_Observer::controller_action_layout_generate_blocks_after() */

}