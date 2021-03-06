<?php
class Df_Parser_Helper_Data extends Mage_Core_Helper_Abstract {
	/**
	 * @param string|array(string|int => string) $message
	 * @return Df_Parser_Helper_Data
	 */
	public function log($message) {
		/** @var mixed[] $arguments */
		$arguments = func_get_args();
		Mage::log(rm_sprintf($arguments), null, 'df.parser.log', true);
		return $this;
	}

	/** @return Df_Parser_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}