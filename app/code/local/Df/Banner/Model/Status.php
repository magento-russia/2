<?php
class Df_Banner_Model_Status extends Varien_Object {
	/**
	 * @static
	 * @return array(int => string)
	 */
	static public function getOptionArray(){
		return array(
			self::STATUS_ENABLED => df_h()->banner()->__('Enabled')
			,self::STATUS_DISABLED => df_h()->banner()->__('Disabled')
		);
	}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
	/**
	 * 2015-01-31
	 * обратите внимание, что для «нет» используется идиотское значение «2»
	 * @see Df_Banner_Model_Banner::needShowTitle()
	 * @return array(array(int => string))
	 */
	public static function yesNo() {
		return df_map_to_options(array(self::STATUS_ENABLED => 'да', self::STATUS_DISABLED => 'нет'));
	}

	const STATUS_ENABLED = 1;
	const STATUS_DISABLED = 2;
}