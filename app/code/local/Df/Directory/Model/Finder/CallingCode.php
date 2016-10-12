<?php
class Df_Directory_Model_Finder_CallingCode extends Df_Core_Model {
	/**
	 * @param Mage_Directory_Model_Country $country
	 * @return string
	 */
	public function getAlternativeByCountry(Mage_Directory_Model_Country $country) {
		if (!isset($this->{__METHOD__}[$country->getIso3Code()])) {
			$this->{__METHOD__}[$country->getIso3Code()] =
				df()->config()->getNodeValueAsString(
					df()->config()->getNodeByKey(
						$this->getAlternativeKeyByCountry($country)
					)
				)
			;
		}
		return $this->{__METHOD__}[$country->getIso3Code()];
	}

	/**
	 * @param Mage_Directory_Model_Country $country
	 * @return string
	 */
	public function getByCountry(Mage_Directory_Model_Country $country) {
		if (!isset($this->{__METHOD__}[$country->getIso3Code()])) {
			$this->{__METHOD__}[$country->getIso3Code()] =
				df()->config()->getNodeValueAsString(
					df()->config()->getNodeByKey(
						$this->getKeyByCountry($country)
					)
				)
			;
		}
		return $this->{__METHOD__}[$country->getIso3Code()];
	}

	/**
	 * @param Mage_Directory_Model_Country $country
	 * @return string
	 */
	private function getAlternativeKeyByCountry(Mage_Directory_Model_Country $country) {
		return rm_config_key(
			self::KEY__BASE, $country->getIso3Code(), self::KEY__CALLING_CODE__ALTERNATIVE
		);
	}

	/**
	 * @param Mage_Directory_Model_Country $country
	 * @return string
	 */
	private function getKeyByCountry(Mage_Directory_Model_Country $country) {
		return rm_config_key(self::KEY__BASE, $country->getIso3Code(), self::KEY__CALLING_CODE);
	}
	const _CLASS = __CLASS__;
	const KEY__BASE = 'df/countries';
	const KEY__CALLING_CODE = 'calling-code';
	const KEY__CALLING_CODE__ALTERNATIVE = 'calling-code-alternative';

	/** @return Df_Directory_Model_Finder_CallingCode */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}