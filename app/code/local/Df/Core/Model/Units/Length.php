<?php
class Df_Core_Model_Units_Length extends Df_Core_Model {
	/**
	 * @param float|float[]|array(string => float) $lengthInDefaultUnits
	 * @return float|float[]|array(string => float)
	 */
	public function inCentimetres($lengthInDefaultUnits) {
		/**
		 * Хотя документация к PHP говорит,
		 * что @uses func_num_args() быть параметром других функций лишь с версии 5.3 PHP,
		 * однако на самом деле @uses func_num_args() быть параметром других функций
		 * в любых версиях PHP 5 и даже PHP 4.
		 * http://3v4l.org/HKFP7
		 * http://php.net/manual/function.func-num-args.php
		 */
		if (1 < func_num_args()) {
			$lengthInDefaultUnits = func_get_args();
		}
		return
			is_array($lengthInDefaultUnits)
			? array_map(array($this, __FUNCTION__), $lengthInDefaultUnits)
			: (
				self::VALUE__CENTIMETRE === $this->getDefaultUnits()
				? rm_float($lengthInDefaultUnits)
				: 0.1 * $this->inMillimetres($lengthInDefaultUnits)
			)
		;
	}

	/**
	 * @param float|float[]|array(string => float) $lengthInDefaultUnits
	 * @return float|float[]|array(string => float)
	 */
	public function inMetres($lengthInDefaultUnits) {
		/**
		 * Хотя документация к PHP говорит,
		 * что @uses func_num_args() быть параметром других функций лишь с версии 5.3 PHP,
		 * однако на самом деле @uses func_num_args() быть параметром других функций
		 * в любых версиях PHP 5 и даже PHP 4.
		 * http://3v4l.org/HKFP7
		 * http://php.net/manual/function.func-num-args.php
		 */
		if (1 < func_num_args()) {
			$lengthInDefaultUnits = func_get_args();
		}
		return
			is_array($lengthInDefaultUnits)
			? array_map(array($this, __FUNCTION__), $lengthInDefaultUnits)
			: (
				self::VALUE__METRE === $this->getDefaultUnits()
				? rm_float($lengthInDefaultUnits)
				: 0.001 * $this->inMillimetres($lengthInDefaultUnits)
			)
		;
	}

	/**
	 * @param float $lengthInDefaultUnits
	 * @return float
	 */
	public function inMillimetres($lengthInDefaultUnits) {
		/** @var float $length*/
		$length = rm_float($lengthInDefaultUnits);
		return
			self::VALUE__MILLIMETRE === $this->getDefaultUnits()
			? $length
			: $this->getRatio() * $length
		;
	}

	/** @return array(string => array(string => string|int)) */
	public function getUnitsSettings() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_config_a('df/units/length');
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getDefaultUnits() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_cfg()->shipping()->product()->getUnitsLength();
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	private function getRatio() {
		if (!isset($this->{__METHOD__})) {
			/** @var mixed[] $productDefaultUnits */
			$productDefaultUnits = dfa($this->getUnitsSettings(), $this->getDefaultUnits());
			df_assert_array($productDefaultUnits);
			$this->{__METHOD__} = rm_float(dfa($productDefaultUnits, self::UNIT__RATIO));
		}
		return $this->{__METHOD__};
	}

	const _C = __CLASS__;
	const UNIT__LABEL = 'label';
	const UNIT__RATIO = 'ratio';
	const VALUE__CENTIMETRE = 'centimetre';
	const VALUE__METRE = 'metre';
	const VALUE__MILLIMETRE = 'millimetre';
	/** @return Df_Core_Model_Units_Length */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}