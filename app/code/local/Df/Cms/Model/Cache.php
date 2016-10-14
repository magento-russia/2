<?php
class Df_Cms_Model_Cache extends Df_Core_Model_Cache {
	/**
	 * @override
	 * @return string
	 */
	protected function getTags() {return array(self::TAG);}
	/**
	 * @override
	 * @return string
	 */
	protected function getType() {return self::TYPE;}

	/**
	 * @used-by Df_Cms_Model_Registry::cacheTags()
	 * @used-by Df_Cms_Model_Tree::cacheTags()
	 * @used-by getTags()
	 */
	const TAG = 'rm_cms';
	/**
	 * @used-by Df_Cms_Model_Registry::cacheType()
	 * @used-by Df_Cms_Model_Tree::cacheType()
	 * @used-by getType()
	 */
	const TYPE = 'rm_cms';

	/** @return Df_Cms_Model_Cache */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}