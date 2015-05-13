<?php
class Df_Chronopay_Block_Gate_Info extends Mage_Payment_Block_Info_Cc {
	/**
	 * @override
	 * @return string
	 */
	public function getArea() {
		return Df_Core_Const_Design_Area::FRONTEND;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->setTemplate(self::DF_TEMPLATE);
	}

	const DF_TEMPLATE = 'df/chronopay/gate/info.phtml';
}