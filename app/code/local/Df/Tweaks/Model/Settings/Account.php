<?php
class Df_Tweaks_Model_Settings_Account extends Df_Core_Model_Settings {
	/** @return boolean */
	public function removeSectionApplications() {
		return $this->getYesNo('remove_section_applications');
	}
	/** @return boolean */
	public function removeSectionBillingAgreements() {
		return $this->getYesNo('remove_section_billing_agreements');
	}
	/** @return boolean */
	public function removeSectionDownloadableProducts() {
		return $this->getYesNo('remove_section_downloadable_products');
	}
	/** @return boolean */
	public function removeSectionNewsletterSubscriptions() {
		return $this->getYesNo('remove_section_newsletter_subscriptions');
	}
	/** @return boolean */
	public function removeSectionProductReviews() {return df_cfg('remove_section_product_reviews');}
	/** @return boolean */
	public function removeSectionRecurringProfiles() {
		return $this->getYesNo('remove_section_recurring_profiles');
	}
	/** @return boolean */
	public function removeSectionTags() {
		return $this->getYesNo('remove_section_tags');
	}
	/** @return boolean */
	public function removeSectionWishlist() {
		return $this->getYesNo('remove_section_wishlist');
	}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_tweaks/account/';}
	/** @return Df_Tweaks_Model_Settings_Account */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}