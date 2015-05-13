<?php
class Df_Catalog_Model_Cache extends Df_Core_Model_Cache {
	/**
	 * @override
	 * @return string
	 */
	protected function getTags() {return array('rm_catalog');}
	/**
	 * @override
	 * @return string
	 */
	protected function getType() {return 'rm_catalog';}

	/** @return Df_Catalog_Model_Cache */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}