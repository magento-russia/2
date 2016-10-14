<?php
class Df_Localization_Realtime_Dictionary_Layout extends Df_Core_Xml_Parser_Entity {
	/**
	 * @used-by Df_Localization_Realtime_Dictionary::handleForLayout()
	 * @return Df_Localization_Realtime_Dictionary_ModulePart_Terms
	 */
	public function terms() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Localization_Realtime_Dictionary_ModulePart_Terms::i($this->e());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by Df_Localization_Realtime_Dictionary::layout()
	 * @param Df_Core_Sxe|string $e
	 * @return Df_Localization_Realtime_Dictionary_Layout
	 */
	public static function i($e) {return self::entity($e, __CLASS__);}
}