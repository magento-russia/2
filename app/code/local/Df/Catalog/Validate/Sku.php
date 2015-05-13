<?php
class Df_Catalog_Validate_Sku extends Df_Zf_Validate_Type {
	/**
	 * @override
	 * @param mixed $value
	 * @return bool
	 */
	public function isValid($value) {
		$this->prepareValidation($value);
		/** @var bool $result */
		$result = true;
		if (!is_string($value)) {
			$result = false;
			$this->setExplanation('Артукул должен быть строкой.');
		}
		else if (!$value) {
			$result = false;
			$this->setExplanation('Вместо артикула получена пустая строка.');
		}
		else if (Df_Catalog_Model_Product::MAX_LENGTH__SKU < mb_strlen($value)) {
			$result = false;
			$this->setExplanation('Значение слишком длинное. Максимальная длина артикула: 64 символа.');
		}
		return $result;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInAccusativeCase() {return 'артикул';}
	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInGenitiveCase() {return 'артикула';}

	/** @return Df_Catalog_Validate_Sku */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}