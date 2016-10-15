<?php
abstract class Df_Directory_Model_Currency_Import_XmlStandard
	extends Df_Directory_Model_Currency_Import {
	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getBaseCurrencyCode();

	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getTagName_CurrencyCode();

	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getTagName_CurrencyItem();

	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getTagName_Denominator();

	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getTagName_Rate();

	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getUrl();

	/**
	 * @override
	 *
	 * @param string $currencyFrom
	 * @param string $currencyTo
	 * @return float
	 */
	protected function convertInternal($currencyFrom, $currencyTo) {
		/** @var float $rateFrom */
		$rateFrom = $this->getRate($currencyFrom);
		/** @var float $rateTo */
		$rateTo = $this->getRate($currencyTo);
		/** @var float $result */
		$result = $rateFrom / $rateTo;
		df_result_float($result);
		df_assert_gt0($result);
		return $result;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getName() {
		return 'Банк России';
	}

	/**
	 * @param array $currencyData
	 * @return float
	 */
	private function calculateRate(array $currencyData) {
		/** @var int $rateDenominator */
		$rateDenominator = df_nat0(dfa($currencyData, $this->getTagName_Denominator()));
		/** @var float $rateRaw */
		$rateRaw = df_float(dfa($currencyData, $this->getTagName_Rate()));
		df_assert_gt0($rateRaw);
		/** @var float $rate */
		$result = $rateRaw / $rateDenominator;
		df_assert_gt0($result);
		return $result;
	}

	/** @return array */
	private function getMapFromCurrencyCodeToRate() {
		if (!isset($this->{__METHOD__})) {
			/** @var array $result */
			$result = array();
			/** @var Df_Core_Sxe $currenciesAsSimpleXml */
			$currenciesAsSimpleXml = $this->getSimpleXml()->descendO($this->getTagName_CurrencyItem());
			foreach ($currenciesAsSimpleXml as $currencyAsSimpleXml) {
				/** @var Df_Core_Sxe $currencyAsSimpleXml */
				/**
					<Valute ID="R01720">
						<NumCode>980</NumCode>
						<CharCode>UAH</CharCode>
						<Nominal>10</Nominal>
						<Name>Украинских гривен</Name>
						<Value>37,1672</Value>
					</Valute>

				<item>
					<date>2013-02-11</date>
					<code>643</code>
					<char3>RUB</char3>
					<size>10</size>
					<name>російських рублів</name>
					<rate>2.6504</rate>
					<change>-0.0095</change>
				</item>
				 */
				/** @var array $currencyAsArray */
				$currencyAsArray = $currencyAsSimpleXml->asArray();
				df_assert_array($currencyAsArray);
				/** @var string $currencyCode */
				$currencyCode = dfa($currencyAsArray, $this->getTagName_CurrencyCode());
				df_assert_string($currencyCode);
				$result[$currencyCode] = $this->calculateRate($currencyAsArray);
			}
			df_result_array($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $currencyCode
	 * @return float
	 */
	private function getRate($currencyCode) {
		/** @var float $result */
		$result =
			($this->getBaseCurrencyCode() === $currencyCode)
			? 1.0
			: dfa($this->getMapFromCurrencyCodeToRate(), $currencyCode)
		;
		if (is_null($result)) {
			$this->throwNoRate($this->getBaseCurrencyCode(), $currencyCode);
		}
		df_result_float($result);
		df_assert_gt0($result);
		return $result;
	}

	/** @return Df_Core_Sxe */
	private function getSimpleXml() {
		if (!isset($this->{__METHOD__})) {
			try {
				$this->{__METHOD__} = df_xml(file_get_contents($this->getUrl()));
			}
			catch (Exception $e) {
				$this->throwServiceFailure($this->getUrl());
			}
		}
		return $this->{__METHOD__};
	}

	const _C = __CLASS__;
}