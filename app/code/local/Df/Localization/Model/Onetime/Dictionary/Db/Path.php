<?php
class Df_Localization_Model_Onetime_Dictionary_Db_Path
	extends Df_Core_Model_SimpleXml_Parser_Entity {
	/** @return string */
	public function value() {return $this->getAttribute('value');}

	/** @return Df_Localization_Model_Onetime_Dictionary_Terms */
	public function terms() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Localization_Model_Onetime_Dictionary_Terms::i($this->e());
		}
		return $this->{__METHOD__};
	}

	/** Используется из @see Df_Localization_Model_Onetime_Dictionary_Db_Paths::getItemClass() */
	const _CLASS = __CLASS__;
}


 