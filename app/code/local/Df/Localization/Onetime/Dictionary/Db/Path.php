<?php
class Df_Localization_Onetime_Dictionary_Db_Path extends Df_Core_Xml_Parser_Entity {
	/** @return string */
	public function value() {return $this->getAttribute('value');}

	/** @return Df_Localization_Onetime_Dictionary_Terms */
	public function terms() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Localization_Onetime_Dictionary_Terms::i($this->e());
		}
		return $this->{__METHOD__};
	}

	/** @used-by Df_Localization_Onetime_Dictionary_Db_Paths::itemClass() */
	const _C = __CLASS__;
}


 