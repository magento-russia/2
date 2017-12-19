<?php
final class Df_RussianPost_Model_RussianPostCalc_Method extends Df_Shipping_Model_Method {
	/**
	 * @override
	 * @return float
	 */
	public function getCost() {
		if (!isset($this->{__METHOD__})) {
			// 2017-12-19 http://russianpostcalc.ru/api-devel.php#calc
			$this->{__METHOD__} = $this->convertFromRoublesToBase($this['cost']);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	public function getMethodTitle() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				rm_sprintf(
					'%s: %d %s,'
					,dftr($this['type'], array('rp_1class' => 'первый класс', 'rp_main' => 'стандартная'))
					// 2017-12-19 http://russianpostcalc.ru/api-devel.php#calc
					,$d = $this['days']
					,$this->getTimeOfDeliveryNounForm($d)
				)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return bool
	 * @throws Exception
	 */
	public function isApplicable() {
		/** @var bool $result */
		$result = parent::isApplicable();
		if ($result) {
			try {
				$this
					->checkCountryDestinationIsRussia()
					->checkCountryOriginIsRussia()
					->checkWeightIsLE(31.5)
				;
			}
			catch(Exception $e) {
				if ($this->needDisplayDiagnosticMessages()) {throw $e;} else {$result = false;}
			}
		}
		return $result;
	}
}