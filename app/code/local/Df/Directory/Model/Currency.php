<?php
/** @method Mage_Directory_Model_Resource_Currency getResource() */
class Df_Directory_Model_Currency extends Mage_Directory_Model_Currency {
	/**
	 * Родительский метод работает некорректно в нескольких случаях:
	 * 1) если $toCurrency — строка
	 * 2) когда нет прямого курса конвертации, но есть обратный.
	 * 3) когда обе валюты не являются базовой,
	 * и по этой причине нет ни прямого курса конвертации, ни обратного,
	 * но возможна конвартация через базовую (третью) валюту: http://magento-forum.ru/topic/4335/
	 * @param float $price
	 * @param string|Df_Directory_Model_Currency $toCurrency
	 * @override
	 * @return float|null
	 * @throws Exception
	 */
	public function convert($price, $toCurrency = null) {
		if (!is_string($toCurrency)) {
			df_assert($toCurrency instanceof Df_Directory_Model_Currency);
		}
		/** @var float $result */
		$result = null;
		if (!is_null($toCurrency)) {
			/** @var float|null $rate */
			$rate = $this->getRate($toCurrency);
			if (!$rate) {
				// Прямого курса нет, однако вполне может существовать обратный.
				// Родительский метод это тоже не учитывает!
				$toCurrency = is_object($toCurrency) ? $toCurrency : self::ld($toCurrency);
				$rate = $toCurrency->getRate($this);
				if ($rate) {
					$rate = 1.0 / $rate;
				}
			}
			if (!$rate) {
				// Итак, у нас нет ни прямого курса обмена, ни обратного.
				// Однако иногда в такой ситуации обмен всё-таки возможен!
				// Если ни одна из обеих валют не является базовой,
				// то вполне естественно, что нет ни прямого курса обмена, ни обратного,
				// но зато запросто можно выполнить конвертацию через промежуточное посредничество
				// базовой (третьей) валюты.
				/** @var Df_Directory_Model_Currency $baseCurrency */
				$baseCurrency = df_store()->getBaseCurrency();
				if ($this->getCode() !== $baseCurrency->getCode()) {
					$toCurrency = is_object($toCurrency) ? $toCurrency : self::ld($toCurrency);
					if ($toCurrency->getCode() !== $baseCurrency->getCode()) {
						// Вот это именно тот случай,
						// когда можно выполнить конвертацию
						// через промежуточное посредничество базовой (третьей) валюты
						/** @var float|null $rateFromBaseToSource */
						$rateFromBaseToSource = $baseCurrency->getRate($this);
						if ($rateFromBaseToSource) {
							/** @var float|null $rateFromBaseToTarget */
							$rateFromBaseToTarget = $baseCurrency->getRate($toCurrency);
							if ($rateFromBaseToTarget) {
								// Пример:
								// USD -> KZT = 182
								// USD -> RUB = 36
								// RUB -> KZT = (USD -> KZT) / (USD -> RUB)
								$rate = $rateFromBaseToTarget / $rateFromBaseToSource;
							}
						}
					}
				}
			}
			if ($rate) {
				$result = $price * $rate;
			}
			else {
				/**
				 * Делаем диагностическое сообщение
				 * более правильным и понятным для русскоязычного администратора.
				 * Обратите внимание, что валюта $toCurrency в данной точке программы
				 * гарантированно существует
				 * (если она не существует, то @see Mage_Directory_Model_Currency::getRate() выше
				 * возбудило бы соответствующую исключительную ситуацию).
				 */
				$toCurrency = is_string($toCurrency) ? self::ld($toCurrency) : $toCurrency;
				/**
				 * В диагностических сообщениях используем «->» вместо «→»,
				 * потому что интерактивный журнал «1C: Управление торговлей»
				 * не воспроизводит символ «→» (заменяет его символом «?»).
				 */
				/** @var Df_Directory_Model_Currency[] $notAvailableCurrencies */
				$notAvailableCurrencies = array();
				if (!$this->isAvailable()) {
					$notAvailableCurrencies[]= $this;
				}
				if (!$toCurrency->isAvailable()) {
					$notAvailableCurrencies[]= $toCurrency;
				}
				if ($notAvailableCurrencies) {
					df_error(
						'Администратор магазина должен добавить %s в перечень разрешённых к использованию валют'
						. ' в административном разделе'
						. ' «Система» -> «Настройки» -> «Общие» -> «Валюты» ->'
						. " «Разрешённые к использованию валюты»,\nа затем указать курс обмена %s на %s"
						.' в административном разделе «Система» -> «Валюты» -> «Курсы».'
						,implode(' и ', df_each($notAvailableCurrencies, 'getNameInCaseAccusative'))
						,$this->getNameInFormOrigin()
						,$toCurrency->getNameInFormDestination()
					);
				}
				else {
					df_error(
						'Администратор магазина должен указать курс обмена %s на %s'
						.' в административном разделе «Система» -> «Валюты» -> «Курсы».'
						,$this->getNameInFormOrigin()
						,$toCurrency->getNameInFormDestination()
					);
				}
			}
		}
		return $result;
	}

	/**
	 * @override
	 * @param string|int|float $price
	 * @param array(string => mixed) $options [optional]
	 * @param bool $includeContainer [optional]
	 * @param bool $addBrackets [optional]
	 * @return string
	 */
	public function format($price, $options=array(), $includeContainer = true, $addBrackets = false) {
		return
			rm_loc()->needHideDecimals()
			? $this->formatDf($price, $options, $includeContainer, $addBrackets)
			: parent::format($price, $options, $includeContainer, $addBrackets)
		;
	}

	/**
	 * @override
	 * @param string|int|float $price
	 * @param array $options
	 * @return string
	 */
	public function formatTxt($price, $options=array()) {
		return
			rm_loc()->needHideDecimals()
			? $this->formatTxtDf($price, $options)
			: parent::formatTxt($price, $options)
		;
	}

	/** @return Df_Localization_Morpher_Response|null */
	public function getMorpher() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_n_set(
				Df_Localization_Morpher::s()->getResponseSilent($this->getName())
			);
		}
		return df_n_get($this->{__METHOD__});
	}
	
	/**
	 * К сожалению, морфер не всегда правильно склоняет двухсловные названия валют:
		<где>в украинской гривне</где>
		<куда>в украинскую гривну</куда>
		<откуда>из украинской гривны</откуда>

		<где>в казахском тенге</где>
		<куда>в казахского тенге</куда>
		<откуда>из казахского тенге</откуда>
	 *
	 * Для устранения этого недостатка создаём второй, упрощённый морфер,
	 * который будет склонять только последнее слово названия валюты.
	 *
	 * Дополнение от 2014-03-21
	 * Однако и упрощённая форма с использованием последнего слова не вполне корректна!
	 * Например, для валюты «Доллар США» последним словом будет «США», и система выдаёт фразы типа:
	 * «Укажите курс обмена сша на тенге»
	 *
	 * @return Df_Localization_Morpher_Response|null
	 */
	public function getMorpherShort() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_n_set(
				Df_Localization_Morpher::s()->getResponseSilent(
					df_last(explode(' ', $this->getName()))
				)
			);
		}
		return df_n_get($this->{__METHOD__});
	}

	/** @return string */
	public function getName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_t()->lcfirst(rm_currency_name($this->getCode()));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $case
	 * @param string $defaultTemplate
	 * @return string
	 */
	public function getNameInCase($case, $defaultTemplate) {
		if (!isset($this->{__METHOD__}[$case])) {
			/** @var string $result */
			$this->{__METHOD__}[$case] =
				$this->getMorpher()
				? $this->getMorpher()->getInCase($case)
				: sprintf('%s «%s»', $defaultTemplate, $this->getName())
			;
		}
		return $this->{__METHOD__}[$case];
	}

	/** @return string */
	public function getNameInCaseAccusative() {return $this->getNameInCase('accusative', 'валюту');}

	/** @return string */
	public function getNameInCaseDative() {return $this->getNameInCase('dative', 'валюте');}

	/** @return string */
	public function getNameInCaseGenitive() {return $this->getNameInCase('genitive', 'валюты');}

	/** @return string */
	public function getNameInCaseInstrumental() {return $this->getNameInCase('instrumental', 'валютой');}

	/** @return string */
	public function getNameInFormDestination() {
		return
			!is_null($this->getMorpher())
			// Раньше тут вместо $this->getMorpher() использовалась упрощённая форма $this->getMorpherShort(),
			// которая оставляет и склоняет только последнее слово, однако это не вполне корректно!
			// Например, для валюты «Доллар США» последним словом будет «США», и система выдаёт фразы типа:
			// «Укажите курс обмена сша на тенге»
			? str_replace('в ', '', $this->getMorpher()->getInFormDestination())
			: $this->getNameInCaseAccusative()
		;
	}

	/** @return string */
	public function getNameInFormOrigin() {
		return
			!is_null($this->getMorpher())
			// Раньше тут вместо $this->getMorpher() использовалась упрощённая форма $this->getMorpherShort(),
			// которая оставляет и склоняет только последнее слово, однако это не вполне корректно!
			// Например, для валюты «Доллар США» последним словом будет «США», и система выдаёт фразы типа:
			// «Укажите курс обмена сша на тенге»
			? str_replace('из ', '', $this->getMorpher()->getInFormOrigin())
			: $this->getNameInCaseGenitive()
		;
	}

	/**
	 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
	 * @return bool
	 */
	public function isAvailable($store = null) {
		return in_array($this->getCode(), df_store($store)->getAvailableCurrencyCodes());
	}

	/**
	 * @param string|int|float $price
	 * @param array $options
	 * @param bool $includeContainer
	 * @param bool $addBrackets
	 * @return string
	 */
	private function formatDf($price, $options = array(), $includeContainer = true, $addBrackets = false) {
		return $this->formatPrecision(
			$price, rm_currency_precision(), $options, $includeContainer, $addBrackets
		);
	}

	/**
	 * @param string|int|float $price
	 * @param array $options
	 * @return string
	 */
	private function formatTxtDf($price, $options = array()) {
		return parent::formatTxt($price, array('precision' => rm_currency_precision()) + $options);
	}


	const BYR = 'BYR';
	const RUB = 'RUB';
	const KZT = 'KZT';
	const UAH = 'UAH';
	const USD = 'USD';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Directory_Model_Currency
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param string $code
	 * @return Df_Directory_Model_Currency
	 */
	public static function ld($code) {
		/** @var array(string => Df_Directory_Model_Currency) $cache */
		static $cache;
		if (!isset($cache[$code])) {
			$cache[$code] = df_load(self::i(), $code);
		}
		return $cache[$code];
	}
}