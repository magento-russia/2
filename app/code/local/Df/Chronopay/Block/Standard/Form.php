<?php
class Df_Chronopay_Block_Standard_Form extends \Df\Payment\Block\Form {
	/**
	 * @override
	 * @return string
	 */
	public function getDescription() {return df_nts(df_cfg('df_payment/chronopay_standard/description'));}
}