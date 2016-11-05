<?php
class Df_Tweaks_Model_Settings_Header extends Df_Core_Model_Settings {
	/** @return Df_Admin_Config_Font */
	public function getFont() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Admin_Config_Font::i('df_tweaks/header');
		}
		return $this->{__METHOD__};
	}

	/** @return boolean */
	public function hideAccountLinkFromAnonymousShopper() {
		return $this->getYesNo('hide_account_link_from_anonymous_shopper');
	}
	/** @return boolean */
	public function hideCartLink() {return $this->getYesNo('remove_cart_link');}
	/** @return boolean */
	public function hideCheckoutLink() {return $this->getYesNo('remove_checkout_link');}
	/** @return boolean */
	public function hideWelcomeFromLoggedIn() {return $this->getYesNo('remove_welcome_for_logged_in');}
	/**
	 * Обратите внимание, что результат — не boolean
	 * (количество возможных значений этой опции больше 2)
	 * @return string
	 */
	public function hideWishlistLink() {return df_cfg('remove_wishlist_link');}
	/** @return boolean */
	public function replaceAccountLinkTitleWithCustomerName() {
		return $this->getYesNo('replace_account_link_title_with_customer_name');
	}
	/** @return boolean */
	public function showOnlyFirstName() {return $this->getYesNo('show_only_first_name');}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_tweaks/header/';}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}