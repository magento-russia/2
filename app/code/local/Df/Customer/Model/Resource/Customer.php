<?php
class Df_Customer_Model_Resource_Customer extends Mage_Customer_Model_Entity_Customer {
	/** @return array(array(string => string|int)) */
	public function getGenderOptions() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				rm_tail(
					$this->getAttribute(Df_Customer_Model_Customer::P__GENDER)
						->getSource()->getAllOptions()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return array(int => string) */
	public function getMapFromGenderIdToGenderName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				array_combine(
					df_clean(rm_int(df_column($this->getGenderOptions(), 'value')))
					,df_clean(df_column($this->getGenderOptions(), 'label'))
				)
			;
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
	
	const _CLASS = __CLASS__;
	/**
	 * @see Df_Customer_Model_Customer::_construct()
	 * @see Df_Customer_Model_Resource_Customer_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Customer_Model_Resource_Customer */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}