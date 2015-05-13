<?php
class Df_Localization_Model_Realtime_Dictionary_ModulePart_Controller
	extends Df_Core_Model_SimpleXml_Parser_Entity {
	/** @return string|null */
	public function getAction() {return $this->getAttribute('action');}

	/** @return string|null */
	public function getControllerClass() {return $this->getAttribute('class');}

	/**
	 * @override
	 * @return string
	 */
	public function getId() {
		return implode('::', array($this->getControllerClass(), $this->getAction()));
	}

	/** @return Df_Localization_Model_Realtime_Dictionary_ModulePart_Terms */
	public function getTerms() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Localization_Model_Realtime_Dictionary_ModulePart_Terms::i($this->e())
			;
		}
		return $this->{__METHOD__};
	}

	/** Используется из @see Df_Localization_Model_Realtime_Dictionary_ModulePart_Controllers::getItemClass() */
	const _CLASS = __CLASS__;
}