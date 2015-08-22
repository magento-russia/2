<?php
class Df_Localization_Model_Onetime_Dictionary_Db_Column
	extends Df_Core_Model_SimpleXml_Parser_Entity {
	/**
	 * @override
	 * @see Df_Core_Model_SimpleXml_Parser_Entity::getName()
	 * @return string
	 */
	public function getName() {return $this->getAttribute('name');}

	/** @return Df_Localization_Model_Onetime_Dictionary_Db_Table */
	public function getTable() {return $this[self::P__TABLE];}

	/** @return Df_Localization_Model_Onetime_Dictionary_Terms */
	public function getTerms() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Localization_Model_Onetime_Dictionary_Terms::i($this->e())
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__TABLE, Df_Localization_Model_Onetime_Dictionary_Db_Table::_CLASS);
	}
	/** Используется из @see Df_Localization_Model_Onetime_Dictionary_Db_Columns::getItemClass() */
	const _CLASS = __CLASS__;
	/** @used-by Df_Localization_Model_Onetime_Dictionary_Db_Columns::getItemParamsAdditional() */
	const P__TABLE = 'table';
}


 