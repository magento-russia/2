<?php
class Df_Core_Model_Units_Weight extends Df_Core_Model_Abstract {
	/**
	 * @param float $productWeightInDefaultUnits
	 * @return int
	 */
	public function convertToGrammes($productWeightInDefaultUnits) {
		return (int)$this->getProductWeightUnitsRatio() * $productWeightInDefaultUnits;
	}

	/**
	 * @param float $productWeightInDefaultUnits
	 * @return float
	 */
	public function convertToKilogrammes($productWeightInDefaultUnits) {
		return 0.001 * $this->convertToGrammes($productWeightInDefaultUnits);
	}

	/** @return array(string => array(string => string|int)) */
	public function getAll() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Mage::app()->getConfig()->getNode('df/units/weight')->asCanonicalArray();
			/**
			 * Varien_Simplexml_Element::asCanonicalArray может возвращать строку в случае,
			 * когда структура исходных данных не соответствует массиву.
			 */
			df_result_array($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	private function getProductWeightUnitsRatio() {
		if (!isset($this->{__METHOD__})) {
			/** @var array $productDefaultUnits */
			$productDefaultUnits =
				df_a(
					df()->units()->weight()->getAll()
					,df_cfg()->shipping()->product()->getUnitsWeight()
				)
			;
			$this->{__METHOD__} = rm_float(df_a($productDefaultUnits, self::UNIT__RATIO));
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
	const UNIT__LABEL = 'label';
	const UNIT__RATIO = 'ratio';

	/** @return Df_Core_Model_Units_Weight */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}