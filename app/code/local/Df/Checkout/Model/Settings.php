<?php
class Df_Checkout_Model_Settings extends Df_Core_Model_Settings {
	/** @return Df_Checkout_Model_Settings_Interface */
	public function _interface() {
		return Df_Checkout_Model_Settings_Interface::s();
	}
	/** @return Df_Checkout_Model_Settings_Field_Applicability */
	public function applicabilityBilling() {
		return $this->field()->getApplicabilityByAddressType('billing');
	}
	/** @return Df_Checkout_Model_Settings_Field_Applicability */
	public function applicabilityShipping() {
		return $this->field()->getApplicabilityByAddressType('shipping');
	}
	/** @return Df_Checkout_Model_Settings_Field */
	public function field() {return Df_Checkout_Model_Settings_Field::s();}
	/** @return string */
	public function getTocContent() {
		df_assert($this->getTocEnabled());
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getTocContentPage()->getData('content');
			df_result_string($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return boolean */
	public function getTocEnabled() {return $this->getYesNo('df_checkout/terms_and_conditions/enabled');}
	/** @return Df_Checkout_Model_Settings_OrderComments */
	public function orderComments() {return Df_Checkout_Model_Settings_OrderComments::s();}
	/** @return Df_Checkout_Model_Settings_Other */
	public function other() {return Df_Checkout_Model_Settings_Other::s();}
	/** @return Df_Checkout_Model_Settings_Patches */
	public function patches() {return Df_Checkout_Model_Settings_Patches::s();}
	/** @return Df_Cms_Model_Page */
	private function getTocContentPage() {
		df_assert($this->getTocEnabled());
		if (!isset($this->{__METHOD__})) {
			// Обратите внимание, что в данном случае метод load загружает страницу
			// по её текстовому идентификатору, а не по стандартному числовому
			$this->{__METHOD__} = Df_Cms_Model_Page::ld($this->getTocContentIdentifier());
		}
		return $this->{__METHOD__};
	}

	/**
	 * Возвращает значение поля «identifier» материала.
	 * В административном интерфейсе поле называется «URL Key»
	 * @return string
	 */
	private function getTocContentIdentifier() {
		return  $this->getString('df_checkout/terms_and_conditions/content');
	}

	const DEFAULT_APPLICABILITY_CODE = '1'; // 1 - optional
	/** @return Df_Checkout_Model_Settings */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}