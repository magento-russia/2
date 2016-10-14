<?php
class Df_Localization_Realtime_Dictionary_ModulePart_Term extends Df_Core_Xml_Parser_Entity {
	/**
	 * @override
	 * @see Df_Core_Model::getId()
	 * @return string
	 */
	public function getId() {return $this->original();}

	/** @return string */
	public function original() {return $this->leafSne(Mage_Core_Model_Locale::DEFAULT_LOCALE);}

	/** @return string|null */
	public function translated() {return $this->leaf(df_locale());}

	/** @used-by Df_Localization_Realtime_Dictionary_ModulePart_Terms::itemClass() */
	const _C = __CLASS__;
}