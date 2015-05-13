<?php
abstract class Df_Core_Block_Element_Input extends Df_Core_Block_Element {
	/** @return string */
	public function getName() {
		/** @var string $result */
		$result = $this->cfg(self::P__NAME);
		df_result_string_not_empty($result);
		return $result;
	}

	/** @return string */
	public function getValue() {return $this->cfg(self::P__VALUE, '');}

	const _CLASS = __CLASS__;
	const P__NAME = 'name';
	const P__VALUE = 'value';
}