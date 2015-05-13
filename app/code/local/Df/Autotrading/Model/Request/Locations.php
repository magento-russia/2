<?php
class Df_Autotrading_Model_Request_Locations extends Df_Shipping_Model_Request {
	/** @return string[][] */
	public function getLocations() {
		if (!isset($this->{__METHOD__})) {
			/** @var string[][] $result */
			/** @var string $cacheKey */
			$cacheKey = $this->getCache()->makeKey(array($this, __FUNCTION__));
			$result = $this->getCache()->loadDataArray($cacheKey);
			if (!is_array($result)) {
				$result = $this->parseLocations();
				$this->getCache()->saveDataArray($cacheKey, $result);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string[][] */
	public function getLocationsAll() {return df_a($this->getLocations(), self::LOCATIONS__ALL);}

	/** @return string[][] */
	public function getLocationsAllMap() {return df_a($this->getLocations(), self::LOCATIONS__ALL_MAP);}

	/** @return string[][] */
	public function getLocationsGrouped() {return df_a($this->getLocations(), self::LOCATIONS__GROUPED);}

	/**
	 * @param string $location
	 * @return bool
	 */
	public function isLocationAllowed($location) {
		return in_array(mb_strtoupper(df_trim($location)), $this->getLocationsAll());
	}

	/**
	 * @param string $rawLocationName
	 * @return string[]
	 */
	public function parseLocation($rawLocationName) {
		df_param_string($rawLocationName, 0);
		/** @var string $originalName */
		$originalName = $this->removeRegionalCenterFromLocationName($rawLocationName);
		$replacementMap = array(
			' (порт восточный)' => ''
			,' (спец.тариф)' => ''
			,' (газо-конден. промысел)' => ''
			,'Княжпогост (Емва)' => 'Княжпогост'
			,'Курумоч (Береза)' => 'Курумоч'
			,'Матырский (ОЭЗ)' => 'Матырский'
			,'Новоникольское (МН Дружба, Траннефтепродукт)' => 'Новоникольское'
			,'Озерск (до поста ГАИ)' => 'Озерск'
			,'Озерск (только до поста ГАИ)' => 'Озерск'
			,'Снежинск (только до поста ГАИ)' => 'Снежинск'
			,'Трехгорный (до поста ГАИ)' => 'Трехгорный'
			,'Алексеевка (ближняя)' => 'Алексеевка'
			,'Петергоф (Петродворец)' => 'Петергоф'
			,'Пыть-Ях (г. Лянтор)' => 'Пыть-Ях'
			,'Ростилово (КС-17)' => 'Ростилово'
			,'Сосьва (ч/з Серов)' => 'Сосьва'
			,'Спасск (Беднодемьяновск)' => 'Спасск'
			,'Строитель (ДСУ-2)' => 'Строитель'
			,'Химки (Вашутинское шоссе)' => 'Химки'
			,'Хоста (с. «Калиновое озеро»)' => 'Хоста'
			,'Хоста (село «Каштаны»)' => 'Хоста'
			,'Ниж.Новгород' => 'Нижний Новгород'
			,'Ниж.Тагил' => 'Нижний Тагил'
			,'Наб.Челны' => 'Набережные Челны'
			,'Ал-Гай' => 'Александров Гай'
			,'Нов. Уренгой' => 'Новый Уренгой'
		);
		/** @var string $repeatedPart */
		$repeatedPart = strtr($rawLocationName, $replacementMap);
		/** @var string $regionalCenter */
		$regionalCenter = null;
		/** @var string $place */
		$place = null;
		if (!rm_contains($repeatedPart, '(')) {
			$regionalCenter = $repeatedPart;
			$place = $repeatedPart;
		}
		else {
			/** @var string[] $locationParts */
			$locationParts = df_trim(explode('(', $repeatedPart), ') ');
			$regionalCenter = df_a($locationParts, 1);
			df_assert($regionalCenter);
			/** @var string $placeWithSuffix */
			$placeWithSuffix = df_a($locationParts, 0);
			df_assert($placeWithSuffix);
			/** @var string $place */
			$place =
				preg_replace(
					'#(.+)\s+(р\.ц\.|г\.|рп|г\.|п\.|с\.|c\.|мкр|д\.|пгт|снп\.|снп|стц|нп|р\-н|пос\.|ст\-ца|ж\/д ст\.|ст\.|а\/п|кп\.|х\.)#u'
					,'$1'
					,$placeWithSuffix
				)
			;
			df_assert($place);
		}
		df_h()->directory()->normalizeLocationName($place);
		df_h()->directory()->normalizeLocationName($regionalCenter);
		/** @var string[] $result */
		$result = array(
			self::KEY__LOCATION => df_h()->directory()->normalizeLocationName($place)
			,self::KEY__REGIONAL_CENTER => df_h()->directory()->normalizeLocationName($regionalCenter)
			,self::KEY__ORIGINAL_NAME => $originalName
		);
		return $result;
	}

	/**
	 * @param string
	 * @return string
	 */
	private function removeRegionalCenterFromLocationName($locationName) {
		df_param_string($locationName, 0);
		/** @var int|bool $regionalCenterPosition */
		$regionalCenterPosition = mb_strrpos($locationName, '(');
		/** @var string $result */
		$result =
			(false === $regionalCenterPosition)
			? $locationName
			: df_trim(mb_substr($locationName, 0, rm_nat($regionalCenterPosition)))
		;
		df_result_string_not_empty($result);
		return $result;
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getHeaders() {
		return array_merge(parent::getHeaders(), array(
			'Accept' => 'application/json, text/javascript, */*; q=0.01'
			,'Accept-Encoding' => 'gzip, deflate'
			,'Accept-Language' => 'en-us,en;q=0.5'
			,'Connection' => 'keep-alive'
			,'Host' => $this->getQueryHost()
			,'Referer' => 'http://www.ae5000.ru/rates/calculate/'
			,'User-Agent' => Df_Core_Const::FAKE_USER_AGENT
			,'X-Requested-With' => 'XMLHttpRequest'
		));
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryHost() {return 'www.ae5000.ru';}

	/**
	 * @override
	 * @return array
	 */
	protected function getQueryParams() {return array('term' => ' ');}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryPath() {return '/site/autocomplete';}

	/** @return string[][] */
	private function getPeripheralLocations() {
		if (!isset($this->{__METHOD__})) {
			$this->response()->json();
			/** @var string[][] $result */
			$result = array();
			$peripheralLocationsRaw = df_column($this->response()->json(), 'value');
			foreach ($peripheralLocationsRaw as $peripheralLocationRaw) {
				$result[]= $this->parseLocation($peripheralLocationRaw);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string[][] */
	private function parseLocations() {
		/** @var string[][] $result */
		/** @var string[] $all */
		$all = array();
		/** @var mixed[][] $allMap */
		$allMap = array();
		foreach ($this->getPeripheralLocations() as $peripheralLocation) {
			/** @var string $peripheralLocation */
			$all[]= df_a($peripheralLocation, self::KEY__LOCATION);
			$allMap[df_a($peripheralLocation, self::KEY__LOCATION)] =
				df_a($peripheralLocation, self::KEY__ORIGINAL_NAME)
			;
		}
		/**
		 * Раньше мы получали региональные центры отдельным запросом по адресу
		 * http://www.ae5000.ru/api.php?metod=city&type=to,
		 * однако этот адрес перестал работать.
		 */
		foreach ($this->getPeripheralLocations() as $peripheralLocation) {
			/** @var string $peripheralLocation */
			/** @var string $regionalCenter */
			$regionalCenter = df_a($peripheralLocation, self::KEY__REGIONAL_CENTER);
			$all[]= $regionalCenter;
			$allMap[$regionalCenter] = $regionalCenter;
		}
		$all = rm_array_unique_fast($all);
		/** @var mixed[][] $grouped */
		$grouped = array();
		/**
		 * Группируем и индексируем данные по региональным центрам
		 */
		foreach ($this->getPeripheralLocations() as $peripheralLocation) {
			/** @var string[] $peripheralLocation */
			/** @var string $regionalCenter */
			$regionalCenter = df_a($peripheralLocation, self::KEY__REGIONAL_CENTER);
			/** @var string[] $locationsForRegionalCenter */
			$locationsForRegionalCenter = df_a($grouped, $regionalCenter, array());
			$locationsForRegionalCenter[df_a($peripheralLocation, self::KEY__LOCATION)] =
				df_a($peripheralLocation, self::KEY__ORIGINAL_NAME)
			;
			$grouped[$regionalCenter] = $locationsForRegionalCenter;
		}
		$result = array(
			self::LOCATIONS__ALL => $all
			,self::LOCATIONS__ALL_MAP => $allMap
			,self::LOCATIONS__GROUPED => $grouped
		);
		return $result;
	}

	const _CLASS = __CLASS__;
	const KEY__LOCATION = 'location';
	const KEY__ORIGINAL_NAME = 'original_name';
	const KEY__REGIONAL_CENTER = 'regional_center';
	const LOCATIONS__ALL = 'all';
	const LOCATIONS__ALL_MAP = 'all_map';
	const LOCATIONS__GROUPED = 'grouped';
	const P__REGIONAL_CENTER = 'regional_center';

	/** @return Df_Autotrading_Model_Request_Locations */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}