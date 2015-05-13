<?php
class Df_Themes_Model_Infortis_Dataporter_Source_Cfgporter_Packagepresets
	extends Infortis_Dataporter_Model_Source_Cfgporter_Packagepresets {
	/**
	 * Цель перекрытия —
	 * перевод названий магазинов, для которых имеются демо-данные для импорта.
	 * @override
	 * @param string|null $package [optional]
	 * @return array(array(string => string))
	 */
	public function toOptionArray($package = null) {
		return Df_Themes_Model_Infortis_Dataporter::translateOptions(
			parent::toOptionArray($package)
		);
	}
}