<?php
class Df_Reward_Model_Source_Website extends Df_Core_Model {
	/**
	 * @param bool $withAll Whether to prepend "All websites" option on not
	 * @return string[]
	 */
	public function toOptionArray($withAll = true) {
		/** @var string[] $result */
		$result = df_mage()->adminhtml()->system()->storeSingleton()->getWebsiteOptionHash();
		if ($withAll) {
			/**
			 * 2015-02-07
			 * Операция «+» для массивов используется в оригинале.
			 * Обратите внимание, что операция «+» игнорирует те элементы второго массива,
			 * ключи которого присутствуют в первом массиве:
			 * «The keys from the first array will be preserved.
			 * If an array key exists in both arrays,
			 * then the element from the first array will be used
			 * and the matching key's element from the second array will be ignored.»
			 * http://php.net/manual/function.array-merge.php
			 * В данном случае мы пользуемся тем,
			 * что элемент с ключом «0» заведомо отсутствует во втором массиве.
			 */
			$result = array(0 => df_h()->reward()->__('All Websites')) + $result;
		}
		return $result;
	}



	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}