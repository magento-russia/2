<?php
class Df_Banner_Model_Status extends Varien_Object {
	const STATUS_ENABLED	= 1;
	const STATUS_DISABLED	= 2;
	/**
	 * @static
	 * @return array(string => string)
	 */
	static public function getOptionArray(){
		return array(
			self::STATUS_ENABLED => df_h()->banner()->__('Enabled')
			,self::STATUS_DISABLED => df_h()->banner()->__('Disabled')
		);
	}
	/** @return Df_Banner_Model_Status */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}