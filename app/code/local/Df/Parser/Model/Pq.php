<?php
abstract class Df_Parser_Model_Pq extends Df_Core_Model {
	/** @return phpQueryObject */
	protected function getPq() {
		return $this->cfg(self::P__PQ);
	}

	/**
	 * @param string $selector
	 * @return phpQueryObject
	 */
	protected function pq($selector) {
		return df_pq($selector, $this->getPq());
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__PQ, 'phpQueryObject');
	}
	const _C = __CLASS__;
	const P__PQ = 'pq';
}