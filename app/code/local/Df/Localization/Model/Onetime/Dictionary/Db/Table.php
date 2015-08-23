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
	public function getName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_table($this->getAttribute('name'));
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-08-23
	 * Первичный ключ должен обязательно существовать,
	 * без этого алгоритм обновления работать не будет.
	 * @used-by Df_Localization_Model_Onetime_Processor_Db_Column::processWithFilters()
	 * @return string
	 */
	public function primaryKey() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_primary_key($this->getName());
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** Используется из
	 * @see Df_Localization_Model_Onetime_Dictionary_Db_Tables::getItemClass()
	 * @used-by Df_Localization_Model_Onetime_Dictionary_Db_Columns::_construct()
	 */
	const _CLASS = __CLASS__;
}


 