<?php
class Df_NovaPoshta_Collector extends Df_Shipping_Collector_Ua {
	/**
	 * @used-by _collect()
	 * @used-by Df_Shipping_Collector::call()
	 * @param bool $toHome
	 * @param string $methodCode
	 * @param int $methodName
	 * @return void
	 */
	protected function _addRate($toHome, $methodCode, $methodName) {
		$this->addRate($this->rate($toHome), $methodCode, $methodName, $this->date());
	}

	/**
	 * @override
	 * @see Df_Shipping_Collector::_collect()
	 * @used-by Df_Shipping_Collector::collect()
	 * @return void
	 */
	protected function _collect() {
		$this->checkCountryDestIs('UA');
		/**
		 * «Параметры посылок, которые принимаются к отправке:
		 * максимальная сторона – не более 150 см
		 * максимальный вес посылки – не более 30 кг»
		 * http://novaposhta.ua/posulku
		 */
		$this->checkWeightIsLE(30);
		$this->checkCityDest();
		$this->call(function() {
			$this->_addRate(true, 'to-home', 'до дома');
			$this->_addRate(false, 'to-warehouse', 'до пункта выдачи');
		});
	}

	/**
	 * @used-by _addRate()
	 * @return Zend_Date
	 */
	private function date() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_NovaPoshta_Request $request */
			$request = new Df_NovaPoshta_Request(array(
				Df_NovaPoshta_Request::P__QUERY_PATH => '/onlineorder/estimatedate'
				,Df_NovaPoshta_Request::P__REQUEST_METHOD => Zend_Http_Client::POST
				,Df_NovaPoshta_Request::P__PARAMS_POST => array('EstimateDateForm' => array(
					'date' => Zend_Date::now()->toString('dd.MM.yyyy')
					,'recipientCityId' => $this->locationDestId()
					,'recipientCity' => $this->locationDestName()
					,'senderCityId' => $this->locationOrigId()
					,'senderCity' => $this->locationOrigName()
				))
			));
			/**
			 * «24 марта 2015»
			 * @var string $dateS
			 */
			$dateS = df_trim($request->response()->pq('.highlight > b')->text());
			df_assert_string_not_empty($dateS);
			/** @var array(string|int) $matches */
			$matches = df_preg_match('#(\d{1,2}) (\w+) (\d{4})#u', $dateS);
			df_assert_eq(3, count($matches));
			/** @var int $month */
			$month = 1 + dfa(array_flip(array(
				'января', 'февраля', 'марта', 'апреля', 'мая', 'июня'
				, 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декаюря'
			)), $matches[1]);
			$this->{__METHOD__} = df_date_create(df_nat($matches[2]), $month, df_nat($matches[0]));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by locationDestId()
	 * @used-by locationDestName()
	 * @return string[]
	 */
	private function locationDest() {
		if (!isset($this->{__METHOD__})) {
			/** @var string[] $result */
			$result = Df_NovaPoshta_Locator::findD($this->cityDestUc());
			if (!$result) {
				$this->errorInvalidCityDest();
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by date()
	 * @used-by rate()
	 * @return string
	 */
	private function locationDestId() {return df_first($this->locationDest());}

	/**
	 * @used-by date()
	 * @used-by rate()
	 * @return string
	 */
	private function locationDestName() {return df_last($this->locationDest());}

	/**
	 * @used-by locationOrigId()
	 * @used-by locationOrigName()
	 * @return string[]
	 */
	private function locationOrig() {
		if (!isset($this->{__METHOD__})) {
			/** @var string[] $result */
			$result = Df_NovaPoshta_Locator::findO($this->cityOrigUc());
			if (!$result) {
				$this->errorInvalidCityOrig();
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by date()
	 * @used-by rate()
	 * @return string
	 */
	private function locationOrigId() {return df_first($this->locationOrig());}

	/**
	 * @used-by date()
	 * @used-by rate()
	 * @return string
	 */
	private function locationOrigName() {return df_last($this->locationOrig());}

	/**
	 * @used-by _collect()
	 * @param bool $toHome
	 * @return float
	 */
	private function rate($toHome) {return dfc($this, function($toHome) {
		/**
		 * Двери-Двери => 1
		 * Двери-Склад => 2
		 * Склад-Двери => 3
		 * Склад-Склад => 4
		 * @var int $mode
		 */
		$mode = 1 + (int)!$toHome + 2 * (int)!$this->приезжатьНаСкладМагазина();
		/** @var Df_NovaPoshta_Request $request */
		$request = new Df_NovaPoshta_Request([
			Df_NovaPoshta_Request::P__QUERY_PATH => '/ru/delivery'
			,Df_NovaPoshta_Request::P__REQUEST_METHOD => Zend_Http_Client::POST
			,Df_NovaPoshta_Request::P__PARAMS_POST => ['DeliveryForm' => [
				'TimeIntervals' => 0
				,'backDelivery' => 0
				,'cargoType' => 'Cargo'
				,'deliveryTechnology_id' => $mode
				,'depth' => $this->rr()->getLengthRoughInCentimeters()
				,'height' => $this->rr()->getHeightRoughInCentimeters()
				,'width' => $this->rr()->getWidthRoughInCentimeters()
				,'is_agent' => 1
				,'packing_service' => 0
				,'palletizing' => 0
				,'places_count' => 1
				,'publicPrice' => $this->declaredValue()
				,'recipientCity' => $this->locationDestName()
				,'recipientCity_id' => $this->locationDestId()
				,'saturdayDelivery' => 0
				,'senderCity' => $this->locationOrigName()
				,'senderCity_id' => $this->locationOrigId()
				/**
				 * http://novaposhta.ua/posulku
				 * «(Длина(см)×Ширина(см)×Высота(см)) / 4000, или объем груза, м³×250.»
				 */
				,'volume_weight' => df_f2(250 * $this->rr()->getVolumeInCubicMetres())
				,'weight' => df_f2($this->weightKg())
			]]
		]);
		/**
		 * «Итого: 200.00 грн *»
		 * @var string $rateS
		 */
		$rateS = $request->response()->pq('.final')->text();
		$rateS = df_trim_text_left($rateS, 'Итого: ');
		$rateS = df_trim_text_right($rateS, ' грн *');
		return df_float_positive($rateS);
	}, func_get_args());}
}