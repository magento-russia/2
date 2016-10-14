<?php
class Df_Megapolis_Model_Method extends Df_Shipping_Model_Method_Russia {
	/**
	 * @override
	 * @return void
	 * @throws Exception
	 */
	protected function checkApplicability() {
		parent::checkApplicability();
		$this
			->checkCountryDestinationIsRussia()
			->checkCountryOriginIsRussia()
			->checkWeightIsLE(31.5)
			->checkOriginIsMoscow()
			->checkCityDestinationIsNotEmpty()
		;
	}

	/**
	 * @override
	 * @used-by Df_Shipping_Model_Method::_getCost()
	 * @return float
	 */
	protected function getCost() {return rm_nat0($this->getApiRate()->getRate());}

	/**
	 * @override
	 * @used-by Df_Shipping_Model_Method::_getDeliveryTime()
	 * @return int|int[]
	 */
	protected function getDeliveryTime() {
		/** @var int|int[] $result */
		$result = 0;
		/** @var array(string => string|mixed[]) $regionData */
		$regionData = df_a($this->_deliveryTimeMap, $this->rr()->getDestinationRegionName());
		df_assert_array($regionData);
		/** @var string $capital */
		$capital = df_a($regionData, 'capital');
		df_assert_string($capital);
		/** @var array(string => int) $timeToCapital */
		$timeToCapital = df_a($regionData, 'time_to_capital');
		df_assert_array($timeToCapital);
		/** @var string $destinationCityUppercased */
		$destinationCityUppercased = mb_strtoupper($this->rr()->getDestinationCity());
		df_assert_string($destinationCityUppercased);
		if ($destinationCityUppercased === mb_strtoupper($capital)) {
			$result = $timeToCapital;
		}
		else {
			/** @var array(string => array(string => int))|null $specialConditions */
			$specialConditions = df_a($regionData, 'special_conditions');
			if (!is_null($specialConditions)) {
				df_assert_array($specialConditions);
				$result = df_a($specialConditions, $destinationCityUppercased);
			}
			if (!$result) {
				/** @var array(string => int) $timeFromCapitalToAnotherLocation */
				$timeFromCapitalToAnotherLocation =
					df_a($regionData, 'time_from_capital_to_another_location')
				;
				df_assert_array($timeFromCapitalToAnotherLocation);
				$result = array(
					df_a($timeToCapital, 'min') +  df_a($timeFromCapitalToAnotherLocation, 'min')
					,df_a($timeToCapital, 'max') + df_a($timeFromCapitalToAnotherLocation, 'max')
				);
			}
		}
		return $result;
	}

	/** @return Df_Megapolis_Model_Request_Rate */
	private function getApiRate() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Megapolis_Model_Request_Rate::i(
				$this->rr()->getLocatorDestination()->getResult()
				,$this->rr()->getWeightInKilogrammes()
				// Объявленная стоимость не может превышать 50000 рублей
				,min(50000, $this->rr()->getDeclaredValueInRoubles())
			);
		}
		return $this->{__METHOD__};
	}

	/** @var array(string => mixed[]) */
	private $_deliveryTimeMap =
		/**
		 * Использованные регулярные выражения:
		 * ([^\t\-\r\n]+)\t([^\t]+)\t([\d\-]+)\t([\d\-]+)\t([\d\-]+)\t([\d\-]+)\r\n
		 *
			'$1' =>
				array(
					'capital' => '$2'
					,'time_to_capital' => array('min' => $3, 'max' => $4)
					,'time_from_capital_to_another_location' => array('min' => $5, 'max' => $6)
					,'special_conditions' => array('$2' => array('min' => $3, 'max' => $4))
				)
			, */
		array(
			'Алтайский' =>
				array(
					'capital' => 'Барнаул'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 6)
				)
			,'Амурская' =>
				array(
					'capital' => 'Благовещенск'
					,'time_to_capital' => array('min' => 3, 'max' => 10)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 6)
					,'special_conditions' => array('ТЫНДА' => array('min' => 5, 'max' => 14))
				)
			,'Архангельская' =>
				array(
					'capital' => 'Архангельск'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 6)
				)
			,'Астраханская' =>
				array(
					'capital' => 'Астрахань'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Белгородская' =>
				array(
					'capital' => 'Белгород'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Брянская' =>
				array(
					'capital' => 'Брянск'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Владимирская' =>
				array(
					'capital' => 'Владимир'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Волгоградская' =>
				array(
					'capital' => 'Волгоград'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Вологодская' =>
				array(
					'capital' => 'Вологда'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
					,'special_conditions' => array('ЧЕРЕПОВЕЦ' => array('min' => 3, 'max' => 10))
				)
			,'Воронежская' =>
				array(
					'capital' => 'Воронеж '
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Еврейская автономная' =>
				array(
					'capital' => 'Биробиджан'
					,'time_to_capital' => array('min' => 3, 'max' => 12)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 6)
				)
			,'Забайкальский' =>
				array(
					'capital' => 'Чита'
					,'time_to_capital' => array('min' => 3, 'max' => 8)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Ивановская' =>
				array(
					'capital' => 'Иваново'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 6)
				)
			,'Иркутская' =>
				array(
					'capital' => 'Иркутск'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 6)
				)
			,'Кабардино-Балкарская' =>
				array(
					'capital' => 'Нальчик'
					,'time_to_capital' => array('min' => 3, 'max' => 8)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Калининградская' =>
				array(
					'capital' => 'Калининград'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Калужская' =>
				array(
					'capital' => 'Калуга'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Камчатский' =>
				array(
					'capital' => 'Петропавловск-Камчатский'
					,'time_to_capital' => array('min' => 2, 'max' => 8)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 6)
					,'special_conditions' => array('ЕЛИЗОВО' => array('min' => 3, 'max' => 12))				)
			,'Карачаево-Черкесская' =>
				array(
					'capital' => 'Черкесск'
					,'time_to_capital' => array('min' => 3, 'max' => 8)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Кемеровская' =>
				array(
					'capital' => 'Кемерово'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
					,'special_conditions' => array('НОВОКУЗНЕЦК' => array('min' => 2, 'max' => 6))
				)
			,'Кировская' =>
				array(
					'capital' => 'Киров'
					,'time_to_capital' => array('min' => 2, 'max' => 8)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Костромская' =>
				array(
					'capital' => 'Кострома'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Краснодарский' =>
				array(
					'capital' => 'Краснодар'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
					,'special_conditions' =>
						array(
							'АНАПА' => array('min' => 3, 'max' => 8)
							,'НОВОРОССИЙСК' => array('min' => 3, 'max' => 8)
							,'СОЧИ' => array('min' => 2, 'max' => 6)
						)
				)
			,'Красноярский' =>
				array(
					'capital' => 'Красноярск'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 6)
					,'special_conditions' =>
						array(
							'НОРИЛЬСК' => array('min' => 2, 'max' => 8)
							,'ДУДИНКА' => array('min' => 4, 'max' => 10)
						)
				)
			,'Курганская' =>
				array(
					'capital' => 'Курган'
					,'time_to_capital' => array('min' => 3, 'max' => 10)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Курская' =>
				array(
					'capital' => 'Курск'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Ленинградская' =>
				array(
					'capital' => 'Санкт-Петербург'
					,'time_to_capital' => array('min' => 1, 'max' => 4)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Липецкая' =>
				array(
					'capital' => 'Липецк'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Магаданская' =>
				array(
					'capital' => 'Магадан'
					,'time_to_capital' => array('min' => 2, 'max' => 8)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 6)
				)
			,'Москва' =>
				array(
					'capital' => 'Москва'
					,'time_to_capital' => array('min' => 1, 'max' => 10)
					,'time_from_capital_to_another_location' => array('min' => 0, 'max' => 0)
				)			
			,'Московская' =>
				array(
					'capital' => 'Мытищи'
					,'time_to_capital' => array('min' => 1, 'max' => 4)
					,'time_from_capital_to_another_location' => array('min' => 0, 'max' => 0)
				)
			,'Мурманская' =>
				array(
					'capital' => 'Мурманск'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Ненецкий' =>
				array(
					'capital' => 'Нарьян-Мар'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 6)
				)
			,'Нижегородская' =>
				array(
					'capital' => 'Нижний Новгород'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Новгородская' =>
				array(
					'capital' => 'Великий Новгород'
					,'time_to_capital' => array('min' => 3, 'max' => 8)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Новосибирская' =>
				array(
					'capital' => 'Новосибирск'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 6)
				)
			,'Омская' =>
				array(
					'capital' => 'Омск'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Оренбургская' =>
				array(
					'capital' => 'Оренбург'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Орловская' =>
				array(
					'capital' => 'Орел '
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Пензенская' =>
				array(
					'capital' => 'Пенза'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Пермский' =>
				array(
					'capital' => 'Пермь'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Приморский' =>
				array(
					'capital' => 'Владивосток'
					,'time_to_capital' => array('min' => 3, 'max' => 8)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 6)
				)
			,'Псковская' =>
				array(
					'capital' => 'Псков'
					,'time_to_capital' => array('min' => 3, 'max' => 8)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Адыгея' =>
				array(
					'capital' => 'Майкоп'
					,'time_to_capital' => array('min' => 3, 'max' => 10)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Алтай' =>
				array(
					'capital' => 'Горно-Алтайск'
					,'time_to_capital' => array('min' => 3, 'max' => 10)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 6)
				)
			,'Башкортостан' =>
				array(
					'capital' => 'Уфа'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Бурятия' =>
				array(
					'capital' => 'Улан-Удэ'
					,'time_to_capital' => array('min' => 2, 'max' => 8)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 6)
				)
			,'Дагестан' =>
				array(
					'capital' => 'Махачкала'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 6)
				)
			,'Ингушетия' =>
				array(
					'capital' => 'Назрань'
					,'time_to_capital' => array('min' => 3, 'max' => 12)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Калмыкия' =>
				array(
					'capital' => 'Элиста'
					,'time_to_capital' => array('min' => 2, 'max' => 8)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Карелия' =>
				array(
					'capital' => 'Петрозаводск'
					,'time_to_capital' => array('min' => 3, 'max' => 8)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 6)
				)
			,'Карелия' =>
				array(
					'capital' => 'Костомукша'
					,'time_to_capital' => array('min' => 4, 'max' => 10)
					,'time_from_capital_to_another_location' => array('min' => null, 'max' => null)
				)
			,'Коми' =>
				array(
					'capital' => 'Сыктывкар'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 6)
					,'special_conditions' =>
						array(
							'ВОРКУТА' => array('min' => 4, 'max' => 10)
							,'УСИНСК' => array('min' => 4, 'max' => 10)
							,'УХТА' => array('min' => 2, 'max' => 6)
						)
				)
			,'Марий Эл' =>
				array(
					'capital' => 'Йошкар-Ола'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Мордовия' =>
				array(
					'capital' => 'Саранск'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Саха (Якутия)' =>
				array(
					'capital' => 'Якутск'
					,'time_to_capital' => array('min' => 2, 'max' => 8)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 6)
					,'special_conditions' =>
						array(
							'МИРНЫЙ' => array('min' => 3, 'max' => 12)
							,'НЕРЮНГРИ' => array('min' => 3, 'max' => 12)
						)
				)
			,'Северная Осетия — Алания' =>
				array(
					'capital' => 'Владикавказ'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Татарстан' =>
				array(
					'capital' => 'Казань'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
					,'special_conditions' => array('НАБЕРЕЖНЫЕ ЧЕЛНЫ' => array('min' => 3, 'max' => 10))
				)
			,'Тыва (Тува)' =>
				array(
					'capital' => 'Кызыл'
					,'time_to_capital' => array('min' => 4, 'max' => 12)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Хакасия' =>
				array(
					'capital' => 'Абакан'
					,'time_to_capital' => array('min' => 3, 'max' => 10)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Ростовская' =>
				array(
					'capital' => 'Ростов-на-Дону'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Рязанская' =>
				array(
					'capital' => 'Рязань '
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Самарская' =>
				array(
					'capital' => 'Самара'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
					,'special_conditions' => array('ТОЛЬЯТТИ' => array('min' => 3, 'max' => 8))
				)
			,'Саратовская' =>
				array(
					'capital' => 'Саратов'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Сахалинская' =>
				array(
					'capital' => 'Южно-Сахалинск'
					,'time_to_capital' => array('min' => 3, 'max' => 8)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 6)
					,'special_conditions' => array('ХОЛМСК' => array('min' => 4, 'max' => 12))
				)
			,'Свердловская' =>
				array(
					'capital' => 'Екатеринбург'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Смоленская' =>
				array(
					'capital' => 'Смоленск'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Ставропольский' =>
				array(
					'capital' => 'Ставрополь'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
					,'special_conditions' => array('МИНЕРАЛЬНЫЕ ВОДЫ' => array('min' => 2, 'max' => 6))
				)
			,'Тамбовская' =>
				array(
					'capital' => 'Тамбов'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Тверская' =>
				array(
					'capital' => 'Тверь'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Томская' =>
				array(
					'capital' => 'Томск'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 6)
					,'special_conditions' => array('СТРЕЖЕВОЙ' => array('min' => 4, 'max' => 10))
				)
			,'Тульская' =>
				array(
					'capital' => 'Тула'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Тюменская' =>
				array(
					'capital' => 'Тюмень'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 6)
				)
			,'Удмуртская' =>
				array(
					'capital' => 'Ижевск'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Ульяновская' =>
				array(
					'capital' => 'Ульяновск'
					,'time_to_capital' => array('min' => 3, 'max' => 10)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Хабаровский' =>
				array(
					'capital' => 'Хабаровск'
					,'time_to_capital' => array('min' => 2, 'max' => 8)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 6)
				)
			,'Ханты-Мансийский' =>
				array(
					'capital' => 'Ханты-Мансийск'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 6)
					,'special_conditions' =>
						array(
							'НЕФТЕЮГАНСК' => array('min' => 4, 'max' => 12)
							,'НИЖНЕВАРТОВСК' => array('min' => 2, 'max' => 6)
							,'СУРГУТ' => array('min' => 2, 'max' => 6)
						)
				)
			,'Челябинская' =>
				array(
					'capital' => 'Челябинск'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
					,'special_conditions' => array('МАГНИТОГОРСК' => array('min' => 2, 'max' => 6))
				)
			,'Чеченская' =>
				array(
					'capital' => 'Грозный'
					,'time_to_capital' => array('min' => 3, 'max' => 8)
					,'time_from_capital_to_another_location' => array('min' => null, 'max' => null)
					,)
			,'Чувашская' =>
				array(
					'capital' => 'Чебоксары'
					,'time_to_capital' => array('min' => 3, 'max' => 8)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
			,'Чукотский' =>
				array(
					'capital' => 'Анадырь'
					,'time_to_capital' => array('min' => 3, 'max' => 12)
					,'time_from_capital_to_another_location' => array('min' => 2, 'max' => 6)
				)
			,'Ямало-Ненецкий' =>
				array(
					'capital' => 'Салехард'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 6)
					,'special_conditions' =>
						array(
							'НАДЫМ' => array('min' => 3, 'max' => 10)
							,'НОВЫЙ УРЕНГОЙ' => array('min' => 2, 'max' => 6)
							,'НОЯБРЬСК' => array('min' => 2, 'max' => 6)
						)
				)
			,'Ярославская' =>
				array(
					'capital' => 'Ярославль'
					,'time_to_capital' => array('min' => 2, 'max' => 6)
					,'time_from_capital_to_another_location' => array('min' => 1, 'max' => 4)
				)
		)
	;

	/** @used-by Df_Megapolis_Model_Collector::getMethods() */
	const _C = __CLASS__;
}