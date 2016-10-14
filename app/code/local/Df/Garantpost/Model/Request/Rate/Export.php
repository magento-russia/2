<?php
class Df_Garantpost_Model_Request_Rate_Export extends Df_Garantpost_Model_Request_Rate {
	/** @return int */
	public function getResult() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_nat($this->response()->pq('input[name="i_tariff_1"]')->val());
		}
		return $this->{__METHOD__};
	}

	/** @return mixed[] */
	protected function getPostParameters() {
		return array_merge(parent::getPostParameters(),array(
			'calc_type' => 'world'
			// term-term — от терминала до терминала
			// door-term — от двери до терминала
			// term-door — от терминала до двери
			// door-door — от двери до двери
			,self::POST_PARAM__SERVICE => 2
			,self::POST_PARAM__DESTINATION_COUNTRY_ID => $this->getDestinationCountryId()
			,self::POST_PARAM__WEIGHT => $this->getWeight()
		));
	}

	/** @return int */
	private function getDestinationCountryId() {
		/** @var int $result */
		$result =
			df_a(
				Df_Garantpost_Model_Request_Countries_ForRate::s()
					->getResponseAsArray()
				,$this->getDestinationCountryIso2()
				,0
			)
		;
		if (0 === $result) {
			df_error(
				'Служба Гарантпост не доставляет грузы в %s'
				,rm_country($this->getDestinationCountryIso2())->getNameInCaseAccusative()
			);
		}
		df_result_integer($result);
		return $result;
	}

	/**
	 * Возвращает 2-буквенный код страны по стандарту ISO 3166-1 alpha-2.
	 * https://ru.wikipedia.org/wiki/ISO_3166-1
	 * @return string
	 */
	private function getDestinationCountryIso2() {return $this->cfg(self::P__DESTINATION_COUNTRY_ISO2);}

	/** @return string */
	private function getWeight() {return $this->cfg(self::P__WEIGHT);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__DESTINATION_COUNTRY_ISO2, RM_V_ISO2)
			->_prop(self::P__WEIGHT, RM_V_FLOAT)
		;
	}
	const _C = __CLASS__;
	const P__DESTINATION_COUNTRY_ISO2  = 'destination_country_iso2';
	const P__WEIGHT = 'weight';
	const POST_PARAM__DESTINATION_COUNTRY_ID = 'i_to_1';
	const POST_PARAM__SERVICE = 'i_service_1';
	const POST_PARAM__WEIGHT = 'i_weight_1';
	/**
	 * @static
	 * @param string $destinationCountryIso2Code
	 * @param float $weight
	 * @return Df_Garantpost_Model_Request_Rate_Export
	 */
	public static function i($destinationCountryIso2Code, $weight) {
		df_param_iso2($destinationCountryIso2Code, 0);
		return new self(array(
			self::P__DESTINATION_COUNTRY_ISO2 => $destinationCountryIso2Code
			, self::P__WEIGHT => $weight
		));
	}
}