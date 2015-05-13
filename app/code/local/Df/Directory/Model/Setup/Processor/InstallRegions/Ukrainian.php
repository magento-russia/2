<?php
class Df_Directory_Model_Setup_Processor_InstallRegions_Ukrainian
	extends Df_Directory_Model_Setup_Processor_InstallRegions {
	/**
	 * @override
	 * @return string
	 */
	protected function getCountryIso2Code() {return 'UA';}

	/**
	 * @override
	 * @return string
	 */
	protected function getLocaleCode() {return 'uk_UA';}

	/**
	 * @override
	 * @return string[][]
	 */
	protected function getRegionsDataRaw() {
		/** @var string[][] $result */
		$result = array(
			array('Крым Автономная Республика', 'Крим Автономна республіка', 'Симферополь', 'Сімферополь', 'UA-43')
			, array('Винницкая область', 'Вінницька область', 'Винница', 'Вінниця', 'UA-05')
			, array('Волынская область', 'Волинська область', 'Луцк', 'Луцьк', 'UA-07')
			, array('Днепропетровская область', 'Дніпропетровська область', 'Днепропетровск', 'Дніпропетровськ', 'UA-12')
			, array('Донецкая область', 'Донецька область', 'Донецк', 'Донецьк', 'UA-14')
			, array('Житомирская область', 'Житомирська область', 'Житомир', 'Житомир', 'UA-18')
			, array('Закарпатская область', 'Закарпатська область', 'Ужгород', 'Ужгород', 'UA-21')
			, array('Запорожская область', 'Запорізька область', 'Запорожье', 'Запоріжжя', 'UA-23')
			, array('Ивано-Франковская область', 'Івано-Франківська область', 'Ивано-Франковск', 'Івано-Франківськ', 'UA-26')
			, array('Киевская область', 'Київська область', 'Киев', 'Київ', 'UA-32')
			, array('Кировоградская область', 'Кіровоградська область', 'Кировоград', 'Кіровоград', 'UA-35')
			, array('Луганская область', 'Луганська область', 'Луганск', 'Луганськ', 'UA-09')
			, array('Львовская область', 'Львівська область', 'Львов', 'Львів', 'UA-46')
			, array('Николаевская область', 'Миколаївська область', 'Николаев', 'Миколаїв', 'UA-48')
			, array('Одесская область', 'Одеська область', 'Одесса', 'Одеса', 'UA-51')
			, array('Полтавская область', 'Полтавська область', 'Полтава', 'Полтава', 'UA-53')
			, array('Ровненская область', 'Рівненська область', 'Ровно', 'Рівне', 'UA-19')
			, array('Сумская область', 'Сумська область', 'Сумы', 'Суми', 'UA-59')
			, array('Тернопольская область', 'Тернопільська область', 'Тернополь', 'Тернопіль', 'UA-61')
			, array('Харьковская область', 'Харківська область', 'Харьков', 'Харків', 'UA-63')
			, array('Херсонская область', 'Херсонська область', 'Херсон', 'Херсон', 'UA-65')
			, array('Хмельницкая область', 'Хмельницька область', 'Хмельницкий', 'Хмельницький', 'UA-68')
			, array('Черкасская область', 'Черкаська область', 'Черкассы', 'Черкаси', 'UA-71')
			, array('Черниговская область', 'Чернігівська область', 'Чернигов', 'Чернігів', 'UA-74')
			, array('Черновицкая область', 'Чернівецька область', 'Черновцы', 'Чернівці', 'UA-77')
			, array('Киев', 'Київ', 'Киев', 'Київ', 'UA-30')
			, array('Севастополь', 'Севастополь', 'Севастополь', 'Севастополь', 'UA-40')
		);
		return $result;
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param Df_Core_Model_Resource_Setup $setup
	 * @return Df_Directory_Model_Setup_Processor_InstallRegions_Ukrainian
	 */
	public static function i(Df_Core_Model_Resource_Setup $setup) {
		return new self(array(self::P__INSTALLER => $setup));
	}
}