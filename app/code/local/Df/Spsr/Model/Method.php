<?php
class Df_Spsr_Model_Method extends Df_Shipping_Model_Method_CollectedManually {
	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param string $rateTitle
	 * @return Df_Spsr_Model_Method
	 */
	public static function i($rateTitle) {
		return new self(array(self::P__TITLE_BASE => $rateTitle));
	}
}