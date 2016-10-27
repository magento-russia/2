<?php
class Df_Chronopay_Block_Standard_Form extends \Df\Payment\Block\Form {
	/**
	 * @override
	 * @return string
	 */
	public function getDescription() {
		return df_nts(Mage::getStoreConfig('df_payment/chronopay_standard/description'));
	}
}