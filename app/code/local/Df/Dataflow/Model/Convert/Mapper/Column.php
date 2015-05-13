<?php
class Df_Dataflow_Model_Convert_Mapper_Column extends Df_Dataflow_Model_Convert_Mapper_Abstract {
	/**
	 * @override
	 * @return string
	 */
	protected function getFeatureCode() {
		return Df_Core_Feature::DATAFLOW;
	}

	/**
	 * @param array $row
	 * @return array
	 */
	protected function processRow(array $row) {
		df_param_array($row, 0);
		foreach ($this->getMap() as $key => $value) {
			/** @var string $key */
			/** @var string $value */
			df_assert_string($key);
			df_assert_string($value);
			$row[$value] = df_a($row, $key);
			if ($key != $value) {
				unset($row[$key]);
			}
		}
		df_result_array($row);
		return $row;
	}

}