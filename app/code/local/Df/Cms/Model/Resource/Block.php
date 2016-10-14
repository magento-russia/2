<?php
class Df_Cms_Model_Resource_Block extends Mage_Cms_Model_Mysql4_Block {
	/** @return Df_Cms_Model_Resource_Block_Collection */
	public function findOrphanBlocks() {
		/** @var Df_Cms_Model_Resource_Block_Collection $result */
		$result = Df_Cms_Model_Resource_Block_Collection::i();
		$result->addFieldToFilter('block_id', array('in' => $this->findOrphanIds()));
		return $result;
	}

	/**
	 * Возвращает идентификаторы блоков, не привязанных ни к одной из витрин.
	 * @return int[]
	 */
	public function findOrphanIds() {
		return rm_conn()->fetchCol(
			rm_select()
				->from(array('b' => rm_table('cms_block')), 'block_id')
				->joinLeft(
					array('bs' => rm_table('cms_block_store'))
					,'b.block_id = bs.block_id'
					,array()
				)
				// Отфильтровываем блоки, которые привязаны к ранее удалённым витринам.
				->where(rm_conn()->prepareSqlCondition('bs.store_id', array(
					'nin' => array_keys(Mage::app()->getStores($withDefault = true, $codeKey = false))
				)))
				/**
				 * Это условие всё равно нужно,
				 * потому что условие выше говорит, каким не должно быть store_id у сирот,
				 * а данное условие, напротив, говорит, каким может быть store_id у сирот.
				 */
				->orWhere('bs.store_id IS NULL')
		);
	}

	/** @return Df_Cms_Model_Resource_Block */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}