<?php
class Df_Localization_Model_Onetime_Dictionary_Db_Table
	extends Df_Core_Model_SimpleXml_Parser_Entity {
	/** @return Df_Localization_Model_Onetime_Dictionary_Db_Columns */
	public function getColumns() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Localization_Model_Onetime_Dictionary_Db_Columns::i($this->e(), $this)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @see Df_Core_Model_SimpleXml_Parser_Entity::getName()
	 * @return string
	 */
	public function getName() {return $this->getAttribute('name');}

	/** Используется из
	 * @see Df_Localization_Model_Onetime_Dictionary_Db_Tables::getItemClass()
	 * @used-by Df_Localization_Model_Onetime_Dictionary_Db_Columns::_construct()
	 */
	const _CLASS = __CLASS__;
}


 