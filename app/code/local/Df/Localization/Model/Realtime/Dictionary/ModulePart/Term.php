<?php
class Df_Localization_Model_Realtime_Dictionary_ModulePart_Term
	extends Df_Core_Model_SimpleXml_Parser_Entity {
	/**
	 * @override
	 * @return string
	 */
	public function getId() {return $this->getTextOriginal();}

	/** @return string|null */
	public function getTextOriginal() {
		return $this->getEntityParam(Mage_Core_Model_Locale::DEFAULT_LOCALE);
	}

	/** @return string|null */
	public function getTextTranslated() {
		return $this->getEntityParam(Mage::app()->getLocale()->getLocaleCode());
	}

	/** Используется из @see Df_Localization_Model_Realtime_Dictionary_ModulePart_Terms::getItemClass() */
	const _CLASS = __CLASS__;
}