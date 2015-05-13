<?php
class Df_Dataflow_Model_Convert_Mapper_Defaults extends Df_Dataflow_Model_Convert_Mapper_Abstract {
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
		$result = array_merge ($this->getMap(), $row);
		df_result_array($result);
		return $result;
	}

}
