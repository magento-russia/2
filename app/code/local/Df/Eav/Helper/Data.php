<?php
class Df_Eav_Helper_Data extends Mage_Core_Helper_Abstract {
	/** @return Df_Eav_Helper_Assert */
	public function assert() {return Df_Eav_Helper_Assert::s();}

	/** @return Df_Eav_Helper_Check */
	public function check() {return Df_Eav_Helper_Check::s();}

	/** @return bool */
	public function isPacketUpdate() {return $this->_isPacketUpdate;}

	/** @return void */
	public function packetUpdateBegin() {$this->_isPacketUpdate = true;}

	/** @return void */
	public function packetUpdateEnd() {$this->_isPacketUpdate = false;}

	/** @var bool */
	private $_isPacketUpdate = false;

	/**
	 * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
	 * @return bool
	 */
	public function isAttributeBelongsToProduct(Mage_Eav_Model_Entity_Attribute_Abstract $attribute) {
		return rm_eav_id_product() === intval($attribute->getEntityTypeId());
	}

	/** @return Df_Eav_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}