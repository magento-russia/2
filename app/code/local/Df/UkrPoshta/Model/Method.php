<?php
abstract class Df_UkrPoshta_Model_Method extends Df_Shipping_Model_Method_Ukraine {
	/** @return string */
	abstract protected function getApiRateClass();

	/**
	 * Метод публичен, потому что используется классом Df_UkrPoshta_Model_Collector
	 *
	 * @abstract
	 * @return bool
	 */
	abstract public function needDeliverToHome();

	/** @return array(string => string|float) */
	protected function getQueryParams() {
		return array(
			'declaredValue' => $this->getRequest()->getDeclaredValueInHryvnias()
			,'withForm' => rm_bts($this->getRmConfig()->service()->makeAccompanyingForms())
		);
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
				/**
				 * Похоже, что УкрПочта отправляет грузы только из Украины.
				 * По крайней мере, стоимость отправки из других стран
				 * официальный калькулятор не рассчитывает.
				 * http://services.ukrposhta.com/CalcUtil/PostalMails.aspx
				 * Как ни странно, тарифы УкрПочты не зависят от городов отправления и назначения.
				 * http://services.ukrposhta.com/CalcUtil/PostalMails.aspx
				 */
				$this->checkCountryOriginIsUkraine();
			}
			catch(Exception $e) {
				if ($this->needDisplayDiagnosticMessages()) {throw $e;} else {$result = false;}
			}
		}
		return $result;
	}

	/**
	 * @override
	 * @return float
	 */
	protected function getCostInHryvnias() {
		return $this->getApiRate()->getResult();
	}

	/** @return Df_UkrPoshta_Model_Request_Rate */
	private function getApiRate() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_model($this->getApiRateClass(), array(
				Df_UkrPoshta_Model_Request_Rate::P__QUERY_PARAMS => $this->getQueryParams()
			));
			df_assert($this->{__METHOD__} instanceof Df_UkrPoshta_Model_Request_Rate);
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
}