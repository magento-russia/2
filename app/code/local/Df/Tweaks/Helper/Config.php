<?php
class Df_Tweaks_Helper_Config extends Mage_Core_Helper_Abstract {
	/** @return string[] */
	public function getStrategyNames() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = !$this->getNode() ? array() : array_values($this->getNode()->asArray());
		}
		return $this->{__METHOD__};
	}

	/** @return Mage_Core_Model_Config_Element|null */
	private function getNode() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(df()->config()->getNodeByKey('df/tweaks/strategies'));
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return Df_Tweaks_Helper_Config */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}