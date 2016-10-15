<?php
class Df_Localization_Onetime_Dictionary_Db_Table extends \Df\Xml\Parser\Entity {
	/** @return Df_Localization_Onetime_Dictionary_Db_Columns */
	public function columns() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Localization_Onetime_Dictionary_Db_Columns::i(
				$this->e(), $this
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Entity::getName()
	 * @return string
	 */
	public function getName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_table($this->getAttribute('name'));
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-08-23
	 * Первичный ключ должен обязательно существовать,
	 * без этого алгоритм обновления работать не будет.
	 * @used-by Df_Localization_Onetime_Processor_Db_Column::process()
	 * @return string
	 */
	public function primaryKey() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_primary_key($this->getName());
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by Df_Localization_Onetime_Dictionary_Db_Column::_construct()
	 * @used-by Df_Localization_Onetime_Dictionary_Db_Columns::_construct()
	 * @used-by Df_Localization_Onetime_Dictionary_Db_Tables::itemClass()
	 */
	const _C = __CLASS__;
}


 