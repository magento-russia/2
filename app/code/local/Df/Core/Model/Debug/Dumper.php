<?php
class Df_Core_Model_Debug_Dumper extends Df_Core_Model {
	/**
	 * @param mixed $value
	 * @return string
	 */
	public function dump($value) {
		/** @var string $result */
		if ($value instanceof Varien_Object) {
			$result = $this->dumpVarienObject($value);
		}
		else if (is_array($value)) {
			$result = $this->dumpArray($value);
		}
		else {
			$result = print_r($value, true);
		}
		return $result;
	}

	/**
	 * @param mixed $array
	 * @return string
	 */
	private function dumpArray(array $array) {
		return "array(" . df_tab_multiline($this->dumpArrayElements($array)) . "\n)";
	}

	/**
	 * @param mixed $array
	 * @return string
	 */
	private function dumpArrayElements(array $array) {
		/** @var string $result */
		$result = '';
		foreach ($array as $key => $value)  {
			/** @var string|int $key */
			/** @var mixed $value */
			$result .= "\n" . '[' . $key . '] => ' . $this->dump($value);
		}
		return $result;
	}

	/**
	 * @param Varien_Object $object
	 * @return string
	 */
	private function dumpVarienObject(Varien_Object $object) {
		return sprintf(
			"%s(%s\n)"
			, get_class($object)
			, df_tab_multiline($this->dumpArrayElements($object->getData()))
		);
	}

	const _CLASS = __CLASS__;
	/** @return Df_Core_Model_Debug_Dumper */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}