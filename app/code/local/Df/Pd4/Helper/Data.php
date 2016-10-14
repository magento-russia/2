<?php
class Df_Pd4_Helper_Data extends Mage_Core_Helper_Abstract {
	/** @return Df_Pd4_Model_Request_Document_View */
	public function getDocumentViewAction() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Pd4_Model_Request_Document_View::i();
		}
		return $this->{__METHOD__};
	}
	/** @var Df_Pd4_Model_Request_Document_View */
	private $_documentViewAction;	

	const _C = __CLASS__;

	/** @return Df_Pd4_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}