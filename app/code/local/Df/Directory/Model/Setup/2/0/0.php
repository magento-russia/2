<?php
class Df_Directory_Model_Setup_2_0_0 extends Df_Core_Model_Setup {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		try {
			df_notify_me('Российская сборка Magento установлена', $doLog = false);
			/** @var string $tableName */
			$tableName = rm_table(Df_Directory_Model_Resource_Region::TABLE__PRIMARY);
			/**
			 * Обратите внимание, что нам не страшно, если колонки df_type и df_capital
			 * уже присутствуют в таблице directory_country_region:
			 * исключительную ситуацию мы тут же гасим.
			 */
			$this->getSetup()->run("
				ALTER TABLE {$tableName}
					ADD COLUMN `df_type` INT(4) DEFAULT null
					,ADD COLUMN `df_capital` VARCHAR(255) CHARACTER SET utf8 DEFAULT null
				;
			");
			/**
			 * После изменения структуры базы данных надо удалить кэш,
			 * потому что Magento кэширует структуру базы данных
			 */
			rm_cache_clean();
		}
		catch(Exception $e) {
			// Думаю, никакой обработки тут не требуется.
		}
		rm_cache_clean();
		$this->writeRussianRegionsToDb();
		rm_cache_clean();
	}

	/**
	 * Обратите внимание, что коды регионов ниже — это коды ГИБДД.
	 * Именно их удобнее всего использоать на практике,
	 * потому что именно их чаще всего используют службы доставки.
	 * @return Df_Directory_Model_Setup_Entity_Region[]
	 */
	private function getRussianRegions() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(array(string|int|null)) $dtoRegions */
			$dtoRegions = array(
				array('Адыгея','Майкоп','01',1)
				,array('Алтай','Горно-Алтайск','04',1)
				,array('Алтайский','Барнаул','22',2)
				,array('Амурская','Благовещенск','28',3)
				,array('Архангельская','Архангельск','29',3)
				,array('Астраханская','Астрахань','30',3)
				,array('Башкортостан','Уфа','02',1)
				,array('Белгородская','Белгород','31',3)
				,array('Брянская','Брянск','32',3)
				,array('Бурятия','Улан-Удэ','03',1)
				,array('Владимирская','Владимир','33',3)
				,array('Волгоградская','Волгоград','34',3)
				,array('Вологодская','Вологда','35',3)
				,array('Воронежская','Воронеж','36',3)
				,array('Дагестан','Махачкала','05',1)
				,array('Еврейская','Биробиджан','79',5)
				,array('Забайкальский','Чита','75',2)
				,array('Ивановская','Иваново','37',3)
				,array('Ингушетия','Магас','06',1)
				,array('Иркутская','Иркутск','38',3)
				,array('Кабардино-Балкарская','Нальчик','07',1)
				,array('Калининградская','Калининград','39',3)
				,array('Калмыкия','Элиста','08',1)
				,array('Калужская','Калуга','40',3)
				,array('Камчатский','Петропавловск-Камчатский','41',2)
				,array('Карачаево-Черкесская','Черкесск','09',1)
				,array('Карелия','Петрозаводск','10',1)
				,array('Кемеровская','Кемерово','42',3)
				,array('Кировская','Киров','43',3)
				,array('Коми','Сыктывкар','11',1)
				,array('Костромская','Кострома','44',3)
				,array('Краснодарский','Краснодар','23',2)
				,array('Красноярский','Красноярск','24',2)
				,array('Курганская','Курган','45',3)
				,array('Курская','Курск','46',3)
				,array('Ленинградская',null,'47',3)
				,array('Липецкая','Липецк','48',3)
				,array('Магаданская','Магадан','49',3)
				,array('Марий Эл','Йошкар-Ола','12',1)
				,array('Мордовия','Саранск','13',1)
				,array('Москва','Москва','77',4)
				,array('Московская',null,'50',3)
				,array('Мурманская','Мурманск','51',3)
				,array('Ненецкий','Нарьян-Мар','83',6)
				,array('Нижегородская','Нижний Новгород','52',3)
				,array('Новгородская','Великий Новгород','53',3)
				,array('Новосибирская','Новосибирск','54',3)
				,array('Омская','Омск','55',3)
				,array('Оренбургская','Оренбург','56',3)
				,array('Орловская','Орёл','57',3)
				,array('Пензенская','Пенза','58',3)
				,array('Пермский','Пермь','59',2)
				,array('Приморский','Владивосток','25',2)
				,array('Псковская','Псков','60',3)
				,array('Ростовская','Ростов-на-Дону','61',3)
				,array('Рязанская','Рязань','62',3)
				,array('Самарская','Самара','63',3)
				,array('Санкт-Петербург','Санкт-Петербург','78',4)
				,array('Саратовская','Саратов','64',3)
				,array('Саха (Якутия)','Якутск','14',1)
				,array('Сахалинская','Южно-Сахалинск','65',3)
				,array('Свердловская','Екатеринбург','66',3)
				,array('Северная Осетия — Алания','Владикавказ','15',1)
				,array('Смоленская','Смоленск','67',3)
				,array('Ставропольский','Ставрополь','26',2)
				,array('Тамбовская','Тамбов','68',3)
				,array('Татарстан','Казань','16',1)
				,array('Тверская','Тверь','69',3)
				,array('Томская','Томск','70',3)
				,array('Тульская','Тула','71',3)
				,array('Тыва (Тува)','Кызыл','17',1)
				,array('Тюменская','Тюмень','72',3)
				,array('Удмуртская','Ижевск','18',1)
				,array('Ульяновская','Ульяновск','73',3)
				,array('Хабаровский','Хабаровск','27',2)
				,array('Хакасия','Абакан','19',1)
				,array('Ханты-Мансийский','Ханты-Мансийск','86',6)
				,array('Челябинская','Челябинск','74',3)
				,array('Чеченская','Грозный','20',1)
				,array('Чувашская','Чебоксары','21',1)
				,array('Чукотский','Анадырь','87',6)
				,array('Ямало-Ненецкий','Салехард','89',6)
				,array('Ярославская','Ярославль','76',3)
			);
			/** @var Df_Directory_Model_Setup_Entity_Region[] $result */
			$result = array();
			foreach ($dtoRegions as $dtoRegion) {
				/** @var array(string|int|null) $dtoRegion */
				$result[]= call_user_func_array('Df_Directory_Model_Setup_Entity_Region::i', $dtoRegion);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return void */
	private function writeRussianRegionsToDb() {
		foreach ($this->getRussianRegions() as $russianRegion) {
			/** @var Df_Directory_Model_Setup_Entity_Region $russianRegion */
			Df_Directory_Model_Setup_Processor_Region::i($russianRegion, $this->getSetup())->process();
		}
	}

	/**
	 * @static
	 * @param Df_Core_Model_Resource_Setup $setup
	 * @return Df_Directory_Model_Setup_2_0_0
	 */
	public static function i(Df_Core_Model_Resource_Setup $setup) {return self::ic($setup, __CLASS__);}
}