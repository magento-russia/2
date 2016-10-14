<?php
class Df_Customer_Model_Resource_Customer extends Mage_Customer_Model_Entity_Customer {
	/** @return array(array(string => string|int)) */
	public function getGenderOptions() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_tail(
				$this->getAttribute(Df_Customer_Model_Customer::P__GENDER)->getSource()->getAllOptions()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return array(int => string) */
	public function getMapFromGenderIdToGenderName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_options_to_map($this->getGenderOptions());
		}
		return $this->{__METHOD__};
	}
	
	/** @return array(string => int) */
	public function getMapFromGenderNameToGenderId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array_flip($this->getMapFromGenderIdToGenderName());
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Customer_Model_Resource_Customer */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}