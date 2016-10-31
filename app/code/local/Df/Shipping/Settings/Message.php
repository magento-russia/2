<?php
namespace Df\Shipping\Settings;
class Message extends \Df_Core_Model_Settings {
	/**
	 * @param \Df_Core_Model_StoreM|int|string|bool|null $s [optional]
	 * @return string
	 */
	public function getFailureGeneral($s = null) {return $this->v('general', $s);}

	/**
	 * @param \Df_Core_Model_StoreM|int|string|bool|null $s [optional]
	 * @return string
	 */
	public function getFailureSameLocation($s = null) {return $this->v('same_location', $s);}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_shipping/message/failure__';}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}