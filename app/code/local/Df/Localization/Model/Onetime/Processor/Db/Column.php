<?php
class Df_Localization_Model_Onetime_Processor_Db_Column extends Df_Core_Model_Abstract {
	/** @return void */
	public function process() {
		/** @var string $table */
		$table = rm_table($this->getColumn()->getTable()->getName());
		/** @var string $column */
		$column = $this->getColumn()->getName();
		foreach ($this->getColumn()->getTerms() as $term) {
			/** @var Df_Localization_Model_Onetime_Dictionary_Term $term */
			rm_conn()->update(
				$table
				, array($column => $term->getTo())
				, array("? = {$column}" => $term->getFrom())
			);
		}
	}

	/** @return Df_Localization_Model_Onetime_Dictionary_Db_Column */
	private function getColumn() {return $this->cfg(self::$P__COLUMN);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__COLUMN, Df_Localization_Model_Onetime_Dictionary_Db_Column::_CLASS);
	}
	/** @var string */
	protected static $P__COLUMN = 'column';

	/**
	 * @param Df_Localization_Model_Onetime_Dictionary_Db_Column $column
	 * @return Df_Localization_Model_Onetime_Processor_Db_Column
	 */
	public static function i(Df_Localization_Model_Onetime_Dictionary_Db_Column $column) {
		return new self(array(self::$P__COLUMN => $column));
	}
}