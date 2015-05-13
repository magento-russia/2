<?php
class Df_Logging_Model_Handler_Models {
	/**
	 * @param object Mage_Core_Model_Abstract $model
	 * @param $processor
	 * @return object Df_Logging_Event_Changes
	 */
	public function modelDeleteAfter($model, $processor) {
		$processor->collectId($model);
		$changes = df_model('df_logging/event_changes')
			->setOriginalData($model->getOrigData())
			->setResultData(null);
		return $changes;
	}

	/**
	 * @param object Mage_Core_Model_Abstract $model
	 * @param $processor
	 * @return object Df_Logging_Event_Changes
	 */
	public function modelMassDeleteAfter($model, $processor) {
		return $this->modelSaveAfter($model, $processor);
	}

	/**
	 * @param object Mage_Core_Model_Abstract $model
	 * @param $processor
	 * @return object Df_Logging_Event_Changes
	 */
	public function modelMassUpdateAfter($model, $processor) {
		return $this->modelSaveAfter($model, $processor);
	}

	/**
	 * @param Mage_Core_Model_Abstract $model
	 * @param $processor
	 * @return Df_Logging_Model_Event_Changes
	 */
	public function modelSaveAfter($model, $processor) {
		$processor->collectId($model);
		/** @var Df_Logging_Model_Event_Changes $result */
		$result = Df_Logging_Model_Event_Changes::i();
		$result->setOriginalData($model->getOrigData());
		$result->setResultData($model->getData());
		return $result;
	}

	/**
	 * @param object Mage_Core_Model_Abstract $model
	 * @param $processor
	 * @return Df_Logging_Model_Event_Changes
	 */
	public function modelViewAfter($model, $processor) {
		$processor->collectId($model);
		return true;
	}
}