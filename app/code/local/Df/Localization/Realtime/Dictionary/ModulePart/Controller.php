<?php
class Df_Localization_Realtime_Dictionary_ModulePart_Controller extends \Df\Xml\Parser\Entity {
	/** @return string|null */
	public function getAction() {return $this->getAttribute('action');}

	/** @return string|null */
	public function getControllerClass() {return $this->getAttribute('class');}

	/**
	 * @override
	 * @see Df_Core_Model::getId()
	 * @return string
	 */
	public function getId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = "{$this->getControllerClass()}::{$this->getAction()}";
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Localization_Realtime_Dictionary_ModulePart_Terms */
	public function terms() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Localization_Realtime_Dictionary_ModulePart_Terms::i($this->e());
		}
		return $this->{__METHOD__};
	}

	/** @used-by Df_Localization_Realtime_Dictionary_ModulePart_Controllers::itemClass() */
	const _C = __CLASS__;
}