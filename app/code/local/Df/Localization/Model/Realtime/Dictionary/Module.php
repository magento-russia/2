<?php
class Df_Localization_Model_Realtime_Dictionary_Module
	extends Df_Core_Model_SimpleXml_Parser_Entity {
	/** @return Df_Localization_Model_Realtime_Dictionary_ModulePart_Blocks */
	public function getBlocks() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Localization_Model_Realtime_Dictionary_ModulePart_Blocks::i($this->e())
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Localization_Model_Realtime_Dictionary_ModulePart_Controllers */
	public function getControllers() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Localization_Model_Realtime_Dictionary_ModulePart_Controllers::i($this->e())
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	public function getId() {return $this->getAttribute('name');}

	/** Используется из @see Df_Localization_Model_Realtime_Dictionary_Modules::getItemClass() */
	const _CLASS = __CLASS__;
}