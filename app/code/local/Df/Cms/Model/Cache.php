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
	 * Эта константа используются не только внутри класса, но и другими классами:
	 * @see Df_Cms_Model_Registry::getCacheTagsRm()
	 * @see Df_Cms_Model_Tree::getCacheTagsRm()
	 */
	const TAG = 'rm_cms';
	/**
	 * Эта константа используются не только внутри класса, но и другими классами:
	 * @see Df_Cms_Model_Registry::getCacheTypeRm()
	 * @see Df_Cms_Model_Tree::getCacheTypeRm()
	 */
	const TYPE = 'rm_cms';

	/** @return Df_Cms_Model_Cache */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}