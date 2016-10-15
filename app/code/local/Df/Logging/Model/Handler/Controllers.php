<?php
/**
 * Handles generic and specific logic for logging on pre/postdispatch
 *
 * All action handlers may take the $config and $eventModel params, which are configuration node for current action and
 * the event model respectively
 *
 * Action will be logged only if the handler returns non-empty value
 *
 */
class Df_Logging_Model_Handler_Controllers
{
	/**
	 * Generic Action handler
	 *
	 *
	 * @param Varien_Simplexml_Element $config
	 * @param Df_Logging_Model_Event $eventModel
	 * @return Df_Logging_Model_Event
	 */
	public function postDispatchGeneric($config, $eventModel, $processorModel)
	{
		$collectedIds = $processorModel->getCollectedIds();
		if ($collectedIds) {
			$eventModel->setInfo(Df_Logging_Helper_Data::s()->implodeValues($collectedIds));
			return true;
		}
		return false;
	}

	/*
	 * Special postDispach handlers below
	*/

	/**
	 * Simply log action without any id-s
	 *
	 * @param Varien_Simplexml_Element $config
	 * @param Df_Logging_Model_Event $eventModel
	 * @return bool
	 */
	public function postDispatchSimpleSave($config, $eventModel)
	{
		return true;
	}

	/**
	 * Custom handler for config view
	 *
	 * @param Varien_Simplexml_Element $config
	 * @param Df_Logging_Model_Event $eventModel
	 * @return Df_Logging_Model_Event
	 */
	public function postDispatchConfigView($config, $eventModel)
	{
		$id = Mage::app()->getRequest()->getParam('section');
		if (!$id) {
			$id = 'general';
		}
		$eventModel->setInfo($id);
		return true;
	}

	/**
	 * Custom handler for config save
	 *
	 * @param Varien_Simplexml_Element $config
	 * @param Df_Logging_Model_Event $eventModel
	 * @param Df_Logging_Model_Processor $processor
	 * @return Df_Logging_Model_Event
	 */
	public function postDispatchConfigSave($config, $eventModel, $processor)
	{
		$request = Mage::app()->getRequest();
		$postData = $request->getPost();
		$groupFieldsData = array();
		/** @var Df_Logging_Model_Event_Changes $change */
		$change = Df_Logging_Model_Event_Changes::i();
		//Collect skip encrypted fields
		$encryptedNodeEntriesPaths = rm_config_adminhtml()->getEncryptedNodeEntriesPaths(true);
		$skipEncrypted = array();
		foreach ($encryptedNodeEntriesPaths as $fieldName) {
			$skipEncrypted[]= $fieldName['field'];
		}
		//For each group of current section creating separated event change
		if (isset($postData['groups'])) {
			foreach ($postData['groups'] as $groupName => $groupData) {
				foreach ($groupData['fields'] as $fieldName => $fieldValueData) {
					//Clearing config data accordingly to collected skip fields
					if (!in_array($fieldName, $skipEncrypted) && isset($fieldValueData['value'])) {
						$groupFieldsData[$fieldName] = $fieldValueData['value'];
					}
				}

				$processor->addEventChanges(
					clone $change->setSourceName($groupName)
								 ->setOriginalData(array())
								 ->setResultData($groupFieldsData)
				);
				$groupFieldsData = array();
			}
		}
		$id = $request->getParam('section');
		if (!$id) {
			$id = 'general';
		}
		return $eventModel->setInfo($id);
	}

	/**
	 * Custom handler for category move
	 *
	 * @param Varien_Simplexml_Element $config
	 * @param Df_Logging_Model_Event $eventModel
	 * @return Df_Logging_Model_Event
	 */
	public function postDispatchCategoryMove($config, $eventModel)
	{
		return $eventModel->setInfo(Mage::app()->getRequest()->getParam('id'));
	}

	/**
	 * Custom handler for global search
	 *
	 * @param Varien_Simplexml_Element $config
	 * @param Df_Logging_Model_Event $eventModel
	 * @return Df_Logging_Model_Event
	 */
	public function postDispatchGlobalSearch($config, $eventModel)
	{
		return $eventModel->setInfo(Mage::app()->getRequest()->getParam('query'));
	}

	/**
	 * Handler for forgotpassword
	 *
	 * @param Varien_Simplexml_Element $config
	 * @param Df_Logging_Model_Event $eventModel
	 * @return Df_Logging_Model_Event|bool
	 */
	public function postDispatchForgotPassword($config, $eventModel)
	{
		if (Mage::app()->getRequest()->isPost()) {
			$model = Mage::registry('df_logging_saved_model_adminhtml_index_forgotpassword');
			if ($model) {
				$info = $model->getId();
			} else {
				$info = Mage::app()->getRequest()->getParam('email');
			}
			$success = true;
			$messages = df_session()->getMessages()->getLastAddedMessage();
			if ($messages) {
				$success = 'error' != $messages->getType();
			}
			return $eventModel->setIsSuccess($success)->setInfo($info);
		}
		return false;
	}

	/**
	 * Custom handler for poll save fail's action
	 *
	 * @param Varien_Simplexml_Element $config
	 * @param Df_Logging_Model_Event $eventModel
	 * @return Df_Logging_Model_Event|bool
	 */
	public function postDispatchPollValidation($config, $eventModel) {
		$out = df_json_decode(Mage::app()->getResponse()->getBody(), false);
		if (!empty($out->error)) {
			$id = Mage::app()->getRequest()->getParam('id');
			return $eventModel->setIsSuccess(false)->setInfo($id == 0 ? '' : $id);
		} else {
			$poll = Mage::registry('current_poll_model');
			if ($poll && $poll->getId()) {
				return $eventModel->setIsSuccess(true)->setInfo($poll->getId());
			}
		}
		return false;
	}

	/**
	 * Custom handler for customer validation fail's action
	 *
	 * @param Varien_Simplexml_Element $config
	 * @param Df_Logging_Model_Event $eventModel
	 * @return Df_Logging_Model_Event|bool
	 */
	public function postDispatchCustomerValidate($config, $eventModel) {
		$out = df_json_decode(Mage::app()->getResponse()->getBody(), false);
		if (!empty($out->error)) {
			$id = Mage::app()->getRequest()->getParam('id');
			return $eventModel->setIsSuccess(false)->setInfo($id == 0 ? '' : $id);
		}
		return false;
	}

	/**
	 * Handler for reports
	 *
	 * @param Varien_Simplexml_Element $config
	 * @param Df_Logging_Model_Event $eventModel
	 * @return Df_Logging_Model_Event|bool
	 */
	public function postDispatchReport($config, $eventModel, $processor)
	{
		$fullActionNameParts = explode('_report_', $config->getName(), 2);
		if (empty($fullActionNameParts[1])) {
			return false;
		}

		$request = Mage::app()->getRequest();
		$filter = $request->getParam('filter');
		//Filtering request data
		$data = dfa_select($request->getParams(), array(
			'report_from', 'report_to', 'report_period', 'store', 'website', 'group'
		));
		//Need when in request data there are was no period info
		if ($filter) {
			$filterData = Mage::app()->getHelper('adminhtml')->prepareFilterString($filter);
			$data = array_merge($data, (array)$filterData);
		}

		//Add log entry details
		if ($data) {
			/** @var Df_Logging_Model_Event_Changes $change */
			$change = Df_Logging_Model_Event_Changes::i();
			$processor->addEventChanges($change->setSourceName('params')
				->setOriginalData(array())
				->setResultData($data));
		}
		return $eventModel->setInfo($fullActionNameParts[1]);
	}

	/**
	 * Custom handler for catalog price rules apply
	 *
	 * @param Varien_Simplexml_Element $config
	 * @param Df_Logging_Model_Event $eventModel
	 * @return Df_Logging_Model_Event
	 */
	public function postDispatchPromoCatalogApply($config, $eventModel)
	{
		$request = Mage::app()->getRequest();
		return $eventModel->setInfo($request->getParam('rule_id') ? $request->getParam('rule_id') : 'all rules');
	}

	/**
	 * Custom handler for catalog price rules save & apply
	 *
	 * @param Varien_Simplexml_Element $config
	 * @param Df_Logging_Model_Event $eventModel
	 * @param Df_Logging_Model_Processor $processorModel
	 * @return Df_Logging_Model_Event
	 */
	public function postDispatchPromoCatalogSaveAndApply($config, $eventModel, $processorModel)
	{
		$request = Mage::app()->getRequest();
		$this->postDispatchGeneric($config, $eventModel, $processorModel);
		if ($request->getParam('auto_apply')) {
			$eventModel->setInfo(Df_Logging_Helper_Data::s()->__('%s & applied', $eventModel->getInfo()));
		}
		return $eventModel;
	}

	/**
	 *
	 * @deprecated after 1.6.1.0
	 */
	public function postDispatchMyAccountSave($config, $eventModel){}

	/**
	 * Special handler for newsletter unsubscribe
	 *
	 * @param Varien_Simplexml_Element $config
	 * @param Df_Logging_Model_Event $eventModel
	 * @return Df_Logging_Model_Event
	 */
	public function postDispatchNewsletterUnsubscribe($config, $eventModel)
	{
		$id = Mage::app()->getRequest()->getParam('subscriber');
		if (is_array($id)) {
			$id = df_csv($id);
		}
		return $eventModel->setInfo($id);
	}

	/**
	 * Custom tax import handler
	 *
	 * @param Varien_Simplexml_Element $config
	 * @param Df_Logging_Model_Event $eventModel
	 * @return Df_Logging_Model_Event|bool
	 */
	public function postDispatchTaxRatesImport($config, $eventModel)
	{
		if (!Mage::app()->getRequest()->isPost()) {
			return false;
		}
		$success = true;
		$messages = df_session()->getMessages()->getLastAddedMessage();
		if ($messages) {
			$success = 'error' != $messages->getType();
		}
		return $eventModel->setIsSuccess($success)->setInfo(Df_Logging_Helper_Data::s()->__('Tax Rates Import'));
	}

	/**
	 *
	 * @deprecated after 1.6.0.0-rc1
	 */
	public function postDispatchSystemStoreSave($config, $eventModel){}

	/**
	 * Custom handler for catalog product mass attribute update
	 *
	 * @param Varien_Simplexml_Element $config
	 * @param Df_Logging_Model_Event $eventModel
	 * @param $processor
	 * @return Df_Logging_Model_Event
	 */
	public function postDispatchProductUpdateAttributes($config, $eventModel, $processor) {
		$request = Mage::app()->getRequest();
		/** @var Df_Logging_Model_Event_Changes $change */
		$change = Df_Logging_Model_Event_Changes::i();
		$products = Mage::helper('adminhtml/catalog_product_edit_action_attribute')->getProductIds();
		if ($products) {
			$processor->addEventChanges(clone $change->setSourceName('product')
				->setOriginalData(array())
				->setResultData(array('ids' => df_csv($products))));
		}

		$processor->addEventChanges(clone $change->setSourceName('inventory')
				->setOriginalData(array())
				->setResultData($request->getParam('inventory', array())));
		$attributes = $request->getParam('attributes', array());
		$status = $request->getParam('status', null);
		if (!$attributes && $status) {
			$attributes['status'] = $status;
		}
		$processor->addEventChanges(clone $change->setSourceName('attributes')
				->setOriginalData(array())
				->setResultData($attributes));
		$websiteIds = $request->getParam('remove_website', array());
		if ($websiteIds) {
			$processor->addEventChanges(clone $change->setSourceName('remove_website_ids')
				->setOriginalData(array())
				->setResultData(array('ids' => df_csv($websiteIds))));
		}

		$websiteIds = $request->getParam('add_website', array());
		if ($websiteIds) {
			$processor->addEventChanges(clone $change->setSourceName('add_website_ids')
				->setOriginalData(array())
				->setResultData(array('ids' => df_csv($websiteIds))));
		}
		return $eventModel->setInfo(Df_Logging_Helper_Data::s()->__('Attributes Updated'));
	}

	 /**
	 * Custom switcher for tax_class_save, to distinguish product and customer tax classes
	 *
	 * @param Varien_Simplexml_Element $config
	 * @param Df_Logging_Model_Event $eventModel
	 * @return Df_Logging_Model_Event
	 */
	public function postDispatchTaxClassSave($config, $eventModel)
	{
		if (!Mage::app()->getRequest()->isPost()) {
			return false;
		}
		$classType = Mage::app()->getRequest()->getParam('class_type');
		$classId = Mage::app()->getRequest()->getParam('class_id');
		if ($classType == 'PRODUCT') {
			$eventModel->setEventCode('tax_product_tax_classes');
		}
		$success = true;
		$messages = df_session()->getMessages()->getLastAddedMessage();
		if ($messages) {
			$success = 'error' != $messages->getType();
		}
		return $eventModel->setIsSuccess($success)->setInfo($classType . ($classId ? ': #' . Mage::app()->getRequest()->getParam('class_id') : ''));
	}

	/**
	 * Custom handler for creating System Backup
	 *
	 * @param Varien_Simplexml_Element $config
	 * @param Df_Logging_Model_Event $eventModel
	 * @return Df_Logging_Model_Event
	 */
	public function postDispatchSystemBackupsCreate($config, $eventModel)
	{
		$backup = Mage::registry('backup_model');
		if ($backup) {
			$eventModel->setIsSuccess($backup->exists())
				->setInfo($backup->getFileName());
		} else {
			$eventModel->setIsSuccess(false);
		}
		return $eventModel;
	}

	/**
	 * Custom handler for deleting System Backup
	 *
	 * @param Varien_Simplexml_Element $config
	 * @param Df_Logging_Model_Event $eventModel
	 * @return Df_Logging_Model_Event
	 */
	public function postDispatchSystemBackupsDelete($config, $eventModel)
	{
		$backup = Mage::registry('backup_model');
		if ($backup) {
			$eventModel->setIsSuccess(!$backup->exists())
				->setInfo($backup->getFileName());
		} else {
			$eventModel->setIsSuccess(false);
		}
		return $eventModel;
	}

	/**
	 * Custom handler for mass unlocking locked admin users
	 *
	 * @param Varien_Simplexml_Element $config
	 * @param Df_Logging_Model_Event $eventModel
	 * @return Df_Logging_Model_Event
	 */
	public function postDispatchAdminAccountsMassUnlock($config, $eventModel)
	{
		if (!Mage::app()->getRequest()->isPost()) {
			return false;
		}
		$userIds = df_nta(Mage::app()->getRequest()->getPost('unlock'));
		if (!$userIds) {
			return false;
		}
		return $eventModel->setInfo(df_csv($userIds));
	}

	/**
	 * Custom handler for mass reindex process on index management
	 *
	 * @param Varien_Simplexml_Element $config
	 * @param Df_Logging_Model_Event $eventModel
	 * @return Df_Logging_Model_Event
	 */
	public function postDispatchReindexProcess($config, $eventModel)
	{
		$processIds = Mage::app()->getRequest()->getParam('process', null);
		if (!$processIds) {
			return false;
		}
		return $eventModel->setInfo(is_array($processIds) ? df_csv($processIds) : (int)$processIds);
	}

	/**
	 * Custom handler for run Import/Export Profile
	 *
	 * @param Varien_Simplexml_Element $config
	 * @param Df_Logging_Model_Event $eventModel
	 * @return Df_Logging_Model_Event
	 */
	public function postDispatchSystemImportExportRun($config, $eventModel)
	{
		$profile = Mage::registry('current_convert_profile');
		if (!$profile) {
			return false;
		}
		$success = true;
		$messages = df_session()->getMessages()->getLastAddedMessage();
		if ($messages) {
			$success = 'error' != $messages->getType();
		}
		return $eventModel->setIsSuccess($success)->setInfo($profile->getName() .  ': #' . $profile->getId());
	}

	/**
	 * Custom handler for System Currency save
	 *
	 * @param Varien_Simplexml_Element $config
	 * @param Df_Logging_Model_Event $eventModel
	 * @param $processor
	 * @return Df_Logging_Model_Event
	 */
	public function postDispatchSystemCurrencySave($config, $eventModel, $processor) {
		$request = Mage::app()->getRequest();
		/** @var Df_Logging_Model_Event_Changes $change */
		$change = Df_Logging_Model_Event_Changes::i();
		$data = $request->getParam('rate');
		$values = array();
		if (!is_array($data)){
			return false;
		}
		foreach ($data as $currencyCode => $rate) {
			foreach ( $rate as $currencyTo => $value ) {
				$value = abs($value);
				if ($value == 0 ) {
					continue;
				}
				$values[]= $currencyCode . '=>' . $currencyTo . ': ' . $value;
			}
		}

		$processor->addEventChanges($change->setSourceName('rates')
			->setOriginalData(array())
			->setResultData(array('rates' => df_csv($values))));
		$success = true;
		$messages = df_session()->getMessages()->getLastAddedMessage();
		if ($messages) {
			$success = 'error' != $messages->getType();
		}
		return $eventModel->setIsSuccess($success)->setInfo(Df_Logging_Helper_Data::s()->__('Currency Rates Saved'));
	}

	/**
	 * Custom handler for Cache Settings Save
	 *
	 * @param Varien_Simplexml_Element $config
	 * @param Df_Logging_Model_Event $eventModel
	 * @param $processor
	 * @return Df_Logging_Model_Event
	 */
	public function postDispatchSaveCacheSettings($config, $eventModel, $processor)
	{
		$request = Mage::app()->getRequest();
		if (!$request->isPost()) {
			return false;
		}
		$clean = $enable = array();
		$action	 = $info = '';
		/** @var Df_Logging_Model_Event_Changes $change */
		$change = Df_Logging_Model_Event_Changes::i();
		$allCache = $request->getPost('all_cache');
		$postEnable = $request->getPost('enable');
		$beta	   = $request->getPost('beta');
		$catalogAction = $request->getPost('catalog_action');
		$cacheTypes = array_keys(df_mage()->coreHelper()->getCacheTypes());
		$betaCacheTypes  = array_keys(df_mage()->coreHelper()->getCacheBetaTypes());
		switch($allCache){
			case 'disable':
				$action = Df_Logging_Helper_Data::s()->__('Disable');
				break;
			case 'enable':
				$action = Df_Logging_Helper_Data::s()->__('Enable');
				break;
			case 'refresh':
				$action = Df_Logging_Helper_Data::s()->__('Refresh');
				break;
			default:
				$action = Df_Logging_Helper_Data::s()->__('No change');
				break;
		}
		$info = Df_Logging_Helper_Data::s()->__('All cache %s. ', $action);
		if ($catalogAction) {
			$info .= $catalogAction;
		}

		foreach ($cacheTypes as $type) {
			$flag = $allCache != 'disable' && (!empty($postEnable[$type]) || $allCache == 'enable');
			$enable[$type] = $flag ? 1 : 0;
			if ($allCache == '' && !$flag) {
				$clean[]= $type;
			}
		}

		foreach ($betaCacheTypes as $type) {
			if (empty($beta[$type])) {
				$clean[]= $type;
			}
			else {
				$enable[$type] = 1;
			}
		}

		$processor->addEventChanges(clone $change->setSourceName('enable')
				->setOriginalData(array())
				->setResultData($enable));
		if (!empty($clean)) {
			$processor->addEventChanges(clone $change->setSourceName('clean')
				->setOriginalData(array())
				->setResultData($clean));
		}
		if ($catalogAction) {
			$processor->addEventChanges(clone $change->setSourceName('catalog')
					->setOriginalData(array())
					->setResultData(array('action' => $catalogAction)));
		}
		$success = true;
		$messages = df_session()->getMessages()->getLastAddedMessage();
		if ($messages) {
			$success = 'error' != $messages->getType();
		}
		return $eventModel->setIsSuccess($success)->setInfo($info);
	}

	/**
	 * Custom tax export handler
	 *
	 * @param Varien_Simplexml_Element $config
	 * @param Df_Logging_Model_Event $eventModel
	 * @return Df_Logging_Model_Event|bool
	 */
	public function postDispatchTaxRatesExport($config, $eventModel)
	{
		if (!Mage::app()->getRequest()->isPost()) {
			return false;
		}
		$success = true;
		$messages = df_session()->getMessages()->getLastAddedMessage();
		if ($messages) {
			$success = 'error' != $messages->getType();
		}
		return $eventModel->setIsSuccess($success)->setInfo(Df_Logging_Helper_Data::s()->__('Tax Rates Export'));
	}
}