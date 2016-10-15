<?php
class Df_Core_Model_Units_Weight extends Df_Core_Model {
	/**
	 * @param float $weightInDefaultUnits
	 * @return float
	 */
	public function inGrammes($weightInDefaultUnits) {
		/** @var float $weight*/
		$weight = df_float($weightInDefaultUnits);
		return self::VALUE__GRAM === $this->getDefaultUnits() ? $weight : $this->getRatio() * $weight;
	}

	/**
	 * @param float $weightInDefaultUnits
	 * @return float
	 */
	public function inKilogrammes($weightInDefaultUnits) {
		return
			self::VALUE__KILOGRAM === $this->getDefaultUnits()
			? df_float($weightInDefaultUnits)
			: 0.001 * $this->inGrammes($weightInDefaultUnits)
		;
	}

	/** @return array(string => array(string => string|int)) */
	public function getUnitsSettings() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_config_a('df/units/weight');
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getDefaultUnits() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_cfg()->shipping()->product()->getUnitsWeight();
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	private function getRatio() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => string|int) $unitSettings */
			$unitSettings = dfa($this->getUnitsSettings(), $this->getDefaultUnits());
			$this->{__METHOD__} = df_float(dfa($unitSettings, self::UNIT__RATIO));
		}
		return $this->{__METHOD__};
	}


	const UNIT__LABEL = 'label';
	const UNIT__RATIO = 'ratio';
	const VALUE__GRAM = 'gram';
	const VALUE__KILOGRAM = 'kilogram';

	/** @return Df_Core_Model_Units_Weight */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}