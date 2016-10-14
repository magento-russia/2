<?php
class Df_TargetRule_Block_Checkout_Cart_Crosssell extends Enterprise_TargetRule_Block_Checkout_Cart_Crosssell {
	/**
	 * Это свойство используется родительским классом без предварительной инициализации,
	 * что в Российской сборке Magento ведёт к сбою:
	 * «Notice: Undefined property: Enterprise_TargetRule_Block_Checkout_Cart_Crosssell::$_index»
	 * @used-by Enterprise_TargetRule_Block_Checkout_Cart_Crosssell::_getTargetRuleIndex()
	 * @var Enterprise_TargetRule_Model_Index|null
	 */
	protected $_index = null;
}