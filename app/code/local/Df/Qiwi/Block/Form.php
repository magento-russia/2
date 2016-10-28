<?php
namespace Df\Qiwi\Block;
use Df\Core\Format\MobilePhoneNumber as PhoneNumber;
/** @method \Df\Qiwi\Method method() */
class Form extends \Df\Payment\Block\Form {
	/**
	 * @override
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/qiwi/form.phtml';}

	/**
	 * @used-by app/design/frontend/rm/default/template/df/qiwi/form.phtml
	 * @return string
	 */
	protected function phone() {return dfc($this, function() {
		/** @var string $result */
		$result = $this->method()->qPhone();
		if (!$result) {
			/** @var PhoneNumber $bPhone */
			$bPhone = PhoneNumber::fromQuoteAddress(df_quote_address_billing());
			if ($bPhone->isValid()) {
				$result = $bPhone->getOnlyDigitsWithoutCallingCode();
			}
			else {
				/** @var PhoneNumber $sPhone */
				$sPhone = PhoneNumber::i(df_quote_address_shipping()->getTelephone());
				$result = $sPhone->isValid() ? $sPhone->getOnlyDigitsWithoutCallingCode() : '';
			}
		}
		return $result;
	});}
}