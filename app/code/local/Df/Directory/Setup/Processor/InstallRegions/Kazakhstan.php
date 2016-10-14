<?php
class Df_Directory_Setup_Processor_InstallRegions_Kazakhstan
	extends Df_Directory_Setup_Processor_InstallRegions {
	/**
	 * @override
	 * @return string
	 */
	protected function getCountryIso2Code() {return 'KZ';}

	/**
	 * @override
	 * @return string
	 */
	protected function getLocaleCode() {return 'kz_KZ';}

	/** @return array */
	protected function getRegionsDataRaw() {
		/** @var array $result */
		$result =
			array(
				array('Алматы', 'Алматы', 'Алматы', 'Алматы')
				, array('Астана', 'Астана', 'Астана', 'Астана')
				, array('Байконур', 'Байқоңыр', 'Байконур', 'Байқоңыр')
				, array('Акмолинская область', 'Ақмола облысы', 'Кокшетау', 'Көкшетау')
				, array('Актюбинская область', 'Ақтөбе облысы', 'Актобе', 'Ақтөбе')
				, array('Алматинская область', 'Алматы облысы', 'Талдыкорган', 'Талдықорған')
				, array('Атырауская область', 'Атырау облысы', 'Атырау', 'Атырау')
				, array('Восточно-Казахстанская область', 'Шығыс Қазақстан облысы', 'Усть-Каменогорск', 'Өскемен')
				, array('Жамбылская область', 'Жамбыл облысы', 'Тараз', 'Тараз')
				, array('Западно-Казахстанская область', 'Батыс Қазақстан облысы', 'Уральск', 'Орал')
				, array('Карагандинская область', 'Қарағанды облысы', 'Караганда', 'Қарағанды')
				, array('Костанайская область', 'Қостанай облысы', 'Костанай', 'Қостанай')
				, array('Кызылординская область', 'Қызылорда облысы', 'Кызылорда', 'Қызылорда')
				, array('Мангистауская область', 'Маңғыстау облысы', 'Актау', 'Ақтау')
				, array('Павлодарская область', 'Павлодар облысы', 'Павлодар', 'Павлодар')
				, array('Северо-Казахстанская область', 'Солтүстік Қазақстан облысы', 'Петропавловск', 'Петропавл')
				, array('Южно-Казахстанская область', 'Оңтүстік Қазақстан облысы', 'Шымкент', 'Шымкент')
			)
		;
		return $result;
	}

	const _C = __CLASS__;
	/**
	 * @static
	 * @param Df_Core_Model_Resource_Setup $setup
	 * @return Df_Directory_Setup_Processor_InstallRegions_Kazakhstan
	 */
	public static function i(Df_Core_Model_Resource_Setup $setup) {
		return new self(array(self::P__INSTALLER => $setup));
	}
}