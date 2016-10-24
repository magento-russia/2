<?php
class Df_Exline_Collector extends Df_Shipping_Collector_Kz {
	/**
	 * @used-by _collect()
	 * @used-by Df_Shipping_Collector::call()
	 * @param int $id
	 * @param string $methodCode
	 * @param int $methodName
	 * @return void
	 */
	protected function _addRate($id, $methodCode, $methodName) {
		/** @var Zend_Date[] $time */
		$time = $this->time($id);
		$this->addRate($this->rate($id), $methodCode, $methodName, df_first($time), df_last($time));
	}

	/**
	 * @override
	 * @see Df_Shipping_Collector::_collect()
	 * @used-by Df_Shipping_Collector::collect()
	 * @return void
	 */
	protected function _collect() {$this->call(function() {
		$this->_addRate(1, 'express', 'экспресс');
		$this->_addRate(2, 'standard', 'стандартный');
	});}

	/**
	 * @see Df_Shipping_Collector::allowedOrigIso2Additional()
	 * @used-by Df_Shipping_Collector::collect()
	 * @return string|string[]
	 */
	protected function allowedOrigIso2Additional() {return 'RU';}

	/**
	 * @override
	 * @see Df_Shipping_Collector::feePercentOfDeclaredValue()
	 * @used-by Df_Shipping_Collector::addRate()
	 * «Сумма расчета не включает в себя сбор в размере 0.8% от суммы объявленной стоимости.
	 * Минимальная сумма объявленной стоимости составляет 15000 тенге.»
	 * http://www.exline.kz/ru/calculations
	 * Цитированное сообщение отображается после расчёта тарифа.
	 * @return int|float
	 */
	protected function feePercentOfDeclaredValue() {return 0.8;}

	/**
	 * @used-by rate()
	 * @used-by time()
	 * @param string $uriSuffix
	 * @param int $serviceId
	 * @param string $jsonPath
	 * @param array(string => string) $additional [optional]
	 * @return array(string => string)
	 */
	private function json($uriSuffix, $serviceId, $jsonPath, array $additional = array()) {
		/** @var array(string => string) $result */
		$result = Df_Exline_Request::i($uriSuffix, $additional + array(
			'origin' => $this->locationOrigId()
			, 'destination' => $this->locationDestId()
			, 'service_id' => $serviceId
		))->response()->json($jsonPath);
		df_result_array($result);
		return $result;
	}

	/**
	 * @used-by json()
	 * @return int
	 */
	private function locationDestId() {
		if (!isset($this->{__METHOD__})) {
			/** @var int $result */
			$result = (int)Df_Exline_Locator::findD($this->cityDestUc());
			if (0 >= $result) {
				$this->errorInvalidCityDest();
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by json()
	 * @return int
	 */
	private function locationOrigId() {
		if (!isset($this->{__METHOD__})) {
			/** @var int $result */
			switch ($this->countryOrigIso2()) {
				case 'RU':
					$result = (int)Df_Exline_Locator::findO('РОССИЯ');
					df_assert_gt0($result);
					break;
				case 'KZ':
					$result = (int)Df_Exline_Locator::findO($this->cityOrigUc());
					if (0 >= $result) {
						$this->errorInvalidCityOrig();
					}
					break;
				default:
					$this->errorInvalidCountryDest();
					break;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by _addRate()
	 * @param int $id
	 * @return float
	 */
	private function rate($id) {
		$response = $this->json('calculate', $id, 'calculation', array('weight' => $this->weightKgS()));
		/** @var float $resultS */
		$result = df_float_positive(dfa($response, 'price'));
		/** @var string $топливнаяНадбавка */
		$топливнаяНадбавка = dfa($response, 'fuel_surplus');
		if ($топливнаяНадбавка) {
			$result += df_float_positive($топливнаяНадбавка, $allow0 = true);
		}
		return $result;
	}

	/**
	 * @used-by _addRate()
	 * @param int $id
	 * @return Zend_Date[]
	 */
	private function time($id) {
		/** @var Zend_Date[] $result */
		try {
			/** @var array(string => mixed) $a */
			$a = $this->json('deadline', $id, 'trail');
			$result = array(self::date(dfa($a, 'min_date')), self::date(dfa($a, 'max_date')));
		}
		catch (Exception $e) {
			$result = array();
		}
		return $result;
	}

	/**
	 * @used-by time()
	 * @param string $value
	 * @return Zend_Date
	 */
	private static function date($value) {
		df_param_string_not_empty($value, 0);
		return new Zend_Date($value, 'yyyy-MM-dd');
	}
}