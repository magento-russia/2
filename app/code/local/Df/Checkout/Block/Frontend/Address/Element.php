<?php
abstract class Df_Checkout_Block_Frontend_Address_Element extends Df_Core_Block_Template {
	/** @return Mage_Checkout_Block_Onepage_Abstract */
	protected function getAddressBlock() {return $this->cfg(self::P__ADDRESS_BLOCK);}
	const _CLASS = __CLASS__;
	const P__ADDRESS_BLOCK = 'address_block';
}