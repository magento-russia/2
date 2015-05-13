<?php
class Df_Chronopay_Block_Standard_Redirect extends Df_Core_Block_Abstract_NoCache {
	/**
	 * @override
	 * @return string
	 */
	protected function _toHtml() {
		/** @var Df_Chronopay_Model_Standard $standard */
		$standard = Df_Chronopay_Model_Standard::i();
		$form = new Varien_Data_Form();
		$form
			->setAction($standard->getChronopayUrl())
			->setId('chronopay_standard_checkout')
			->setName('chronopay_standard_checkout')
			->setMethod(Zend_Http_Client::POST)
			->setUseContainer(true)
		;
		$standard->setOrder($this->getOrder());
		foreach ($standard->getStandardCheckoutFormFields() as $field => $value) {
			$form
				->addField(
					$field
					,Df_Varien_Data_Form_Element_Abstract::TYPE__HIDDEN
					,array('name' => $field, 'value' => $value)
				)
			;
		}
		$html = '<html><body>';
		$html.= $this->__('You will be redirected to ChronoPay in a few seconds.');
		$html.= $form->toHtml();
		$html.= '<script type="text/javascript">document.getElementById("chronopay_standard_checkout").submit();</script>';
		$html.= '</body></html>';
		return $html;
	}

	/** @return Df_Chronopay_Block_Standard_Redirect */
	public static function i() {return df_block(__CLASS__);}
}