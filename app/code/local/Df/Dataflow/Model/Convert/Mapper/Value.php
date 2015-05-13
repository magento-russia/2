<?php
class Df_Dataflow_Model_Convert_Mapper_Value extends Df_Dataflow_Model_Convert_Mapper_Abstract {
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
		/** @var string $valueBeforeProcessing */
		$valueBeforeProcessing = df_a($row, $this->getAttributeName());
		if ($valueBeforeProcessing) {
			/** @var string|null $valueAfterProcessing */
			$valueAfterProcessing = null;
			foreach ($this->getMap() as $occurence => $replacement) {
				/** @var string $occurence */
				/** @var string $replacement */
				df_assert_string($occurence);
				df_assert_string($replacement);
				if (rm_contains($valueBeforeProcessing, $occurence)) {
					$valueAfterProcessing = str_replace($valueBeforeProcessing, $occurence, $replacement);
					break;
				}
			}
			$row[$this->getAttributeName()] = $valueAfterProcessing;
		}
		return $row;
	}

	/** @return string */
	private function getAttributeName() {
		/** @var string $result */
		$result =
			$this->getVar(self::VAR_ATTRIBUTE)
		;
		df_result_string($result);
		return $result;
	}

	const VAR_ATTRIBUTE = 'df-attribute';
}