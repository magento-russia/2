<?php
class Df_Dataflow_Helper_Import extends Mage_Core_Helper_Abstract {
	/** @return Df_Dataflow_Model_Import_Config */
	public function getConfig() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Dataflow_Model_Import_Config::i();
		}
		return $this->{__METHOD__};
	}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}