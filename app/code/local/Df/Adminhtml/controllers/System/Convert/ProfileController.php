<?php
require_once BP . '/app/code/core/Mage/Adminhtml/controllers/System/Convert/ProfileController.php';
class Df_Adminhtml_System_Convert_ProfileController extends Mage_Adminhtml_System_Convert_ProfileController {
	/**
	 * @override
 	 * @return void
	 */
	public function batchRunAction() {
		/** @var bool $patchNeeded */
		static $patchNeeded;
		if (!isset($patchNeeded)) {
			$patchNeeded =
					df_enabled(Df_Core_Feature::DATAFLOW)
				&&
					df_cfg()->dataflow()->common()->getShowInteractiveMessages()
			;
		}
		if ($patchNeeded) {
			$this->batchRunActionDf();
		}
		else {
			parent::batchRunAction();
		}
	}

	/**
	 * @override
 	 * @return void
	 */
	public function batchFinishAction() {
		/** @var bool $patchNeeded */
		static $patchNeeded;
		if (!isset($patchNeeded)) {
			$patchNeeded =
					df_enabled(Df_Core_Feature::DATAFLOW)
				&&
					df_cfg()->dataflow()->common()->getShowInteractiveMessages()
			;
		}
		if ($patchNeeded) {
			$this->batchFinishActionDf();
		}
		else {
			parent::batchFinishAction();
		}
	}

	/** @return void */
	private function batchFinishActionDf() {
		df_mage()->adminhtml()->session()->unsetData(Df_Dataflow_Const::P__COUNTER);
		parent::batchFinishAction();
	}

	/** @return void */
	private function batchRunActionDf() {
		df_assert($this->getRequest()->isPost());
		/** @var int $batchId */
		$batchId = rm_nat($this->getRequest()->getPost('batch_id'));
		/** @var array $rowIds */
		$rowIds  = $this->getRequest()->getPost('rows');
		df_assert_array($rowIds);
		df_assert_gt0(count($rowIds));
		/* @var Mage_Dataflow_Model_Batch $batchModel */
		$batchModel = df_mage()->dataflow()->batch();
		$batchModel->load($batchId);
		df_assert(!is_null($batchModel->getId()));
		df_assert_eq($batchId, rm_nat($batchModel->getId()));
		/** @var string $adapterClassMf */
		$adapterClassMf = $batchModel->getDataUsingMethod('adapter');
		df_assert_string_not_empty($adapterClassMf);
		/** @var Mage_Dataflow_Model_Convert_Adapter_Interface $adapter */
		/** @var Mage_Dataflow_Model_Convert_Container_Abstract $adapter */
		$adapter = df_model($adapterClassMf);
		// Адаптер у нас — как двуликий Янус.
		df_assert($adapter instanceof Mage_Dataflow_Model_Convert_Adapter_Interface);
		df_assert($adapter instanceof  Mage_Dataflow_Model_Convert_Container_Abstract);
		if (
			/**
			 * К сожалению, нельзя здесь для проверки публичности метода использовать is_callable,
			 * потому что наличие Varien_Object::__call
			 * приводит к тому, что is_callable всегда возвращает true.
			 */
			!method_exists($adapter, 'saveRow')
		) {
			df_error(
				'При включенном интерактивном режиме работы Magento Dataflow '
				.'у класса %s требуется наличие метода saveRow.'
				,get_class($adapter)
			);
		}
		/** @var Mage_Dataflow_Model_Batch_Import $batchImportModel */
		$batchImportModel = $batchModel->getBatchImportModel();
		df_assert($batchImportModel instanceof Mage_Dataflow_Model_Batch_Import);
		$adapter->setBatchParams($batchModel->getParams());
		/** @var array $errors */
		$errors = array();
		/** @var int $savedRowsCounter */
		$savedRowsCounter = 0;

		// BEGIN PATCH
		$adapter->setProfile(Df_Dataflow_Model_Convert_Profile::i());
		$messages = array();
		// END PATCH

		/**
		 * Как правило (и по умолчанию), в этом массиве будет всего 1 элемент,
		 * потому что для каждой импортируемой строки клиентская часть Magento
		 * будет делать отдельный запрос к серверной.
		 */
		foreach ($rowIds as $importId) {
			/** @var int $importId */
			df_assert_integer($importId);
			/** @var int|null $counter */
			$counter = df_mage()->adminhtml()->session()->getData(Df_Dataflow_Const::P__COUNTER);
			if (is_null($counter)) {
				$counter = 0;
			}
			$counter++;
			df_mage()->adminhtml()->session()->setData(Df_Dataflow_Const::P__COUNTER, $counter);
			$batchImportModel->load($importId);
			if (is_null($batchImportModel->getId())) {
				$errors[]= df_mage()->dataflowHelper()->__('Skip undefined row.');
				continue;
			}
			try {
				$importData = $batchImportModel->getBatchData();
				call_user_func(array($adapter, 'saveRow'), $importData);
			}
			catch(Exception $e) {
				$errors[]= rm_ets($e);
				continue;
			}
			$savedRowsCounter ++;
		}
		// BEGIN PATCH
		if ($adapter->getProfile()) {
			/** @var Mage_Dataflow_Model_Convert_Profile $dataflowConvertProfile */
			$dataflowConvertProfile = $adapter->getProfile();
			df_assert($dataflowConvertProfile instanceof Mage_Dataflow_Model_Convert_Profile);
			/** @var array $exceptions */
			$exceptions = $dataflowConvertProfile->getExceptions();
			df_assert_array($exceptions);
			foreach ($exceptions as $exception) {
				/** @var Varien_Convert_Exception $exception */
				if (Mage_Dataflow_Model_Convert_Exception::NOTICE === $exception->getLevel()) {
					$messages[]= rm_ets($exception);
				}
				else {
					$errors[]= rm_ets($exception);
				}
			}
		}
		// END PATCH
		$result =
			array(
				'savedRows' => $savedRowsCounter

				,'errors'	=> $errors

				// BEGIN PATCH
				,"messages" => $messages
				// END PATCH

			)
		;
		$this->getResponse()
			->setBody(
				df_mage()->coreHelper()->jsonEncode(
					$result
				)
			)
		;
	}
}