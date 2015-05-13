<?php
class Df_Themes_Model_Infortis_Dataporter_Source_Cfgporter_Packagemodules
	extends Infortis_Dataporter_Model_Source_Cfgporter_Packagemodules {
	/**
	 * Цель перекрытия —
	 * перевод названий модулей, для которых возможен экспорт/импорт настроек.
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