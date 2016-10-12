<?php
class Df_Core_Model_Units_Length extends Df_Core_Model {
	/**
	 * @param float $productLengthInDefaultUnits
	 * @return float
	 */
	public function convertToCentimetres($productLengthInDefaultUnits) {
		df_param_float($productLengthInDefaultUnits, 0);
		return 0.1 * $this->convertToMillimetres($productLengthInDefaultUnits);
	}

	/**
	 * @param float|float[] $productSizeInDefaultUnits
	 * @return float|float[]
	 */
	public function convertToMetres($productSizeInDefaultUnits) {
		/** @var float|float[] $result */
		if (!is_array($productSizeInDefaultUnits)) {
			$result = 0.001 * $this->convertToMillimetres($productSizeInDefaultUnits);
		}
		else {
			$result = array();
			foreach ($productSizeInDefaultUnits as $sizeName => $sizeValue) {
				/** @var string $sizeName */
				/** @var float $sizeValue */
				$result[$sizeName]= $this->convertToMetres($sizeValue);
			}
		}
		return $result;
	}

	/**
	 * @param float $productLengthInDefaultUnits
	 * @return float
	 */
	public function convertToMillimetres($productLengthInDefaultUnits) {
		df_param_float($productLengthInDefaultUnits, 0);
		return $this->getProductLengthUnitsRatio() * $productLengthInDefaultUnits;
	}

	/** @return array(string => array(string => string|int)) */
	public function getAll() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Mage::app()->getConfig()->getNode('df/units/length')->asCanonicalArray();
			/**
			 * Varien_Simplexml_Element::asCanonicalArray может возвращать строку в случае,
			 * когда структура исходных данных не соответствует массиву.
			 */
			df_result_array($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	private function getProductLengthUnitsRatio() {
		if (!isset($this->{__METHOD__})) {
			/** @var mixed[] $productDefaultUnits */
			$productDefaultUnits =
				df_a(
					df()->units()->length()->getAll()
					,df_cfg()->shipping()->product()->getUnitsLength()
				)
			;
			df_assert_array($productDefaultUnits);
			$this->{__METHOD__} = rm_float(df_a($productDefaultUnits, self::UNIT__RATIO));
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
	const UNIT__LABEL = 'label';
	const UNIT__RATIO = 'ratio';
	/** @return Df_Core_Model_Units_Length */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}