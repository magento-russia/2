<?php
class Df_Pd4_Helper_Data extends Mage_Core_Helper_Abstract {
	/** @return \Df\Pd4\Request\Document\View */
	public function getDocumentViewAction() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = \Df\Pd4\Request\Document\View ::i();
		}
		return $this->{__METHOD__};
	}
	/** @var \Df\Pd4\Request\Document\View  */
	private $_documentViewAction;	

	/** @return Df_Pd4_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}