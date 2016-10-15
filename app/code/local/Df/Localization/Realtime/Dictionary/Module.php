<?php
class Df_Localization_Realtime_Dictionary_Module
	extends \Df\Xml\Parser\Entity {
	/** @return Df_Localization_Realtime_Dictionary_ModulePart_Blocks */
	public function getBlocks() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Localization_Realtime_Dictionary_ModulePart_Blocks::i($this->e())
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Localization_Realtime_Dictionary_ModulePart_Controllers */
	public function getControllers() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Localization_Realtime_Dictionary_ModulePart_Controllers::i($this->e())
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Перекрываем этот метод, чтобы использовать функциональность
	 * @see Df_Localization_Realtime_Dictionary_Modules::findById()
	 * Эта функциональность используется методами:
	 * @see Df_Localization_Realtime_Dictionary::handleForController()
	 * @see Df_Localization_Realtime_Dictionary::handleForBlock()
	 * @override
	 * @see Df_Core_Model::getId()
	 * @return string
	 */
	public function getId() {return $this->getAttribute('name');}

	/** @used-by Df_Localization_Realtime_Dictionary_Modules::itemClass() */
	const _C = __CLASS__;
}