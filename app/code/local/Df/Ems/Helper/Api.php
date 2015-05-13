<?php
class Df_Ems_Helper_Api extends Mage_Core_Helper_Abstract {
	/** @return Df_Ems_Model_Api_Locations_Cities */
	public function cities() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Ems_Model_Api_Locations_Cities::i();
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Ems_Model_Api_Locations_Countries */
	public function countries() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Ems_Model_Api_Locations_Countries::i();
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Ems_Model_Api_Locations_Regions */
	public function regions() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Ems_Model_Api_Locations_Regions::i();
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Ems_Helper_Api */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}