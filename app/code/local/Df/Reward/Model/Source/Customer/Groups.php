<?php
class Df_Reward_Model_Source_Customer_Groups {
	/** @return array(int => string) */
	public function toOptionArray() {
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
		return
				array(0 => df_h()->reward()->__('All Customer Groups'))
			+
				Df_Customer_Model_Group::c()
					->addFieldToFilter('customer_group_id', array('gt'=> 0))
					->toOptionHash()
		;
	}

	/** @return Df_Reward_Model_Source_Customer_Groups */
	public static function i() {return new self;}
}