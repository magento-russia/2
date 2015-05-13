<?php
class Df_Adminhtml_Model_Config_Data extends Mage_Adminhtml_Model_Config_Data {
	/** @return Df_Adminhtml_Model_Config_Data */
	public function save() {
		if (df_magento_version('1.4.1.0', '<')) {
			$this->save_patchFor_1_4_0_1();
		}
		else if (df_magento_version('1.7.0.2', '=')) {
			$this->save_patchFor_1_7_0_2();
		}
		else {
			parent::save();
		}
		return $this;
	}

	/**
	 * Метод save взят из Magento CE 1.7.0.0-rc1
	 * @see Mage_Adminhtml_Model_Config_Data::save()
	 * Цель перекрытия —
	 * поддержка в Magento 1.4.0.1 ключа config_path
	 * который появился только в Magento 1.4.1.0
	 * @return Df_Adminhtml_Model_Config_Data
	 */
	private function save_patchFor_1_4_0_1() {
		$this->_validate();
		$this->_getScope();
		Mage::dispatchEvent('model_config_data_save_before', array('object' => $this));
		$section = $this->getSection();
		$website = $this->getWebsite();
		$store   = $this->getStore();
		$groups  = $this->getGroups();
		$scope   = $this->getScope();
		$scopeId = $this->getScopeId();
		/** @var Df_Adminhtml_Model_Config_Data_1401 $dfConfig */
		$dfConfig = Df_Adminhtml_Model_Config_Data_1401::i();
		if (empty($groups)) {
			return $this;
		}

		/* @var $sections Mage_Core_Model_Config_Element */
		$sections = df_mage()->adminhtml()->getConfig()->getSections();

		$oldConfig = $this->_getConfig(true);
		$deleteTransaction = df_model('core/resource_transaction');
		/* @var $deleteTransaction Mage_Core_Model_Resource_Transaction */
		$saveTransaction = df_model('core/resource_transaction');
		/* @var $saveTransaction Mage_Core_Model_Resource_Transaction */

		// Extends for old config data
		$oldConfigAdditionalGroups = array();
		foreach ($groups as $group => $groupData) {
			/**
			* Map field names if they were cloned
			*/
			$groupConfig = $sections->descend($section.'/groups/'.$group);
			if ($clonedFields = !empty($groupConfig->clone_fields)) {
				if ($groupConfig->clone_model) {
					$cloneModel = df_model((string)$groupConfig->clone_model);
				} else {
					Mage::throwException('Config form fieldset clone model required to be able to clone fields');
				}
				$mappedFields = array();
				$fieldsConfig = $sections->descend($section.'/groups/'.$group.'/fields');
				if ($fieldsConfig->hasChildren()) {
					foreach ($fieldsConfig->children() as $field => $node) {
						foreach ($cloneModel->getPrefixes() as $prefix) {
							$mappedFields[$prefix['field'].(string)$field] = (string)$field;
						}
					}
				}
			}
		  // set value for group field entry by fieldname
		  // use extra memory
		  $fieldsetData = array();
		  foreach ($groupData['fields'] as $field => $fieldData) {
			  $fieldsetData[$field] = (is_array($fieldData) && isset($fieldData['value']))
				  ? $fieldData['value'] : null;
		  }

			foreach ($groupData['fields'] as $field => $fieldData) {
				/**
				* Get field backend model
				*/
				$backendClass = $sections->descend($section.'/groups/'.$group.'/fields/'.$field.'/backend_model');
				if (!$backendClass && $clonedFields && isset($mappedFields[$field])) {
					$backendClass = $sections->descend($section.'/groups/'.$group.'/fields/'.$mappedFields[$field].'/backend_model');
				}
				if (!$backendClass) {
					$backendClass = Df_Core_Model_Config_Data::_CLASS;
				}
				$dataObject = df_model($backendClass);
				if (!$dataObject instanceof Mage_Core_Model_Config_Data) {
					Mage::throwException('Invalid config field backend model: '.$backendClass);
				}
				/* @var $dataObject Mage_Core_Model_Config_Data */

				$fieldConfig = $sections->descend($section.'/groups/'.$group.'/fields/'.$field);
				if (!$fieldConfig && $clonedFields && isset($mappedFields[$field])) {
					$fieldConfig = $sections->descend($section.'/groups/'.$group.'/fields/'.$mappedFields[$field]);
				}
				$dataObject
					->setField($field)
					->setGroups($groups)
					->setGroupId($group)
					->setStoreCode($store)
					->setWebsiteCode($website)
					->setScope($scope)
					->setScopeId($scopeId)
					->setFieldConfig($fieldConfig)
				  	->setFieldsetData($fieldsetData)
				;
				if (!isset($fieldData['value'])) {
					$fieldData['value'] = null;
				}

				/*if (is_array($fieldData['value'])) {
				$fieldData['value'] = implode(',', $fieldData['value']);
				}*/

				$path	= $section.'/'.$group.'/'.$field;
				/**
				* Look for custom defined field path
				*/
				if (is_object($fieldConfig)) {
					$configPath = (string)$fieldConfig->config_path;
					if (!empty($configPath) && strrpos($configPath, '/') > 0) {
						// Extend old data with specified section group
						$groupPath = substr($configPath, 0, strrpos($configPath, '/'));
						if (!isset($oldConfigAdditionalGroups[$groupPath])) {
							$dfConfig->setData($this->getData());
							$oldConfig =
								$dfConfig
									->extendConfig(
										$groupPath
										,true
										,$oldConfig
									)
							;
							$oldConfigAdditionalGroups[$groupPath] = true;
						}
						$path = $configPath;
					}
				}

				$inherit = !empty($fieldData['inherit']);
				$dataObject->setPath($path)
					->setValue($fieldData['value']);
				if (isset($oldConfig[$path])) {
					$dataObject->setConfigId($oldConfig[$path]['config_id']);
					/**
					* Delete config data if inherit
					*/
					if (!$inherit) {
						$saveTransaction->addObject($dataObject);
					}
					else {
						$deleteTransaction->addObject($dataObject);
					}
				}
				else if (!$inherit) {
					$dataObject->unsConfigId();
					$saveTransaction->addObject($dataObject);
				}
			}
		}

		$deleteTransaction->delete();
		$saveTransaction->save();
		return $this;
	}

	/**
	 * Устранение сбоя
	 * При сохранении настроек произошёл сбой: Notice: Trying to get property of non-object
	 * in app\code\core\Mage\Adminhtml\Model\Config\Data.php on line 135
	 * An error occurred while saving this configuration: Notice: Trying to get property of non-object
	 * in app\code\core\Mage\Adminhtml\Model\Config\Data.php on line 135
	 *
	 * @url http://www.magentocommerce.com/bug-tracking/issue?issue=14217
	 * @return Df_Adminhtml_Model_Config_Data
	 */
	private function save_patchFor_1_7_0_2() {
		$this->_validate();
		$this->_getScope();
		Mage::dispatchEvent('model_config_data_save_before', array('object' => $this));
		$section = $this->getSection();
		$website = $this->getWebsite();
		$store   = $this->getStore();
		$groups  = $this->getGroups();
		$scope   = $this->getScope();
		$scopeId = $this->getScopeId();
		if (empty($groups)) {
			return $this;
		}

		$sections = df_mage()->adminhtml()->getConfig()->getSections();
		/* @var $sections Mage_Core_Model_Config_Element */

		$oldConfig = $this->_getConfig(true);
		$deleteTransaction = df_model('core/resource_transaction');
		/* @var $deleteTransaction Mage_Core_Model_Resource_Transaction */
		$saveTransaction = df_model('core/resource_transaction');
		/* @var $saveTransaction Mage_Core_Model_Resource_Transaction */

		// Extends for old config data
		$oldConfigAdditionalGroups = array();
		foreach ($groups as $group => $groupData) {
			/**
			* Map field names if they were cloned
			*/
			$groupConfig = $sections->descend($section.'/groups/'.$group);
			if ($clonedFields = !empty($groupConfig->clone_fields)) {
				if ($groupConfig->clone_model) {
					$cloneModel = df_model((string)$groupConfig->clone_model);
				}
				else {
					Mage::throwException('Config form fieldset clone model required to be able to clone fields');
				}
				$mappedFields = array();
				$fieldsConfig = $sections->descend($section.'/groups/'.$group.'/fields');
				if ($fieldsConfig->hasChildren()) {
					foreach ($fieldsConfig->children() as $field => $node) {
						foreach ($cloneModel->getPrefixes() as $prefix) {
							$mappedFields[$prefix['field'].(string)$field] = (string)$field;
						}
					}
				}
			}
			// set value for group field entry by fieldname
			// use extra memory
			$fieldsetData = array();
			foreach ($groupData['fields'] as $field => $fieldData) {
				$fieldsetData[$field] = (is_array($fieldData) && isset($fieldData['value']))
					? $fieldData['value'] : null;
			}

			foreach ($groupData['fields'] as $field => $fieldData) {
				$fieldConfig = $sections->descend($section . '/groups/' . $group . '/fields/' . $field);
				if (!$fieldConfig && $clonedFields && isset($mappedFields[$field])) {
					$fieldConfig = $sections->descend($section . '/groups/' . $group . '/fields/'
						. $mappedFields[$field]);
				}
				if (!$fieldConfig) {
					$node = $sections->xpath($section .'//' . $group . '[@type="group"]/fields/' . $field);
					if ($node) {
						$fieldConfig = $node[0];
					}
				}

				/**
				 * Get field backend model
				 */


				/******************************
				 * BEGIN PATCH
				 */
				/** @var string $backendClass */
				$backendClass =
					isset($fieldConfig->backend_model)
					? $fieldConfig->backend_model
					: Df_Core_Model_Config_Data::_CLASS
				;
				/******************************
				 * END PATCH
				 */
				/** @var $dataObject Mage_Core_Model_Config_Data */
				$dataObject = df_model($backendClass);
				if (!$dataObject instanceof Mage_Core_Model_Config_Data) {
					Mage::throwException('Invalid config field backend model: '.$backendClass);
				}
				$dataObject
					->setField($field)
					->setGroups($groups)
					->setGroupId($group)
					->setStoreCode($store)
					->setWebsiteCode($website)
					->setScope($scope)
					->setScopeId($scopeId)
					->setFieldConfig($fieldConfig)
					->setFieldsetData($fieldsetData)
				;
				if (!isset($fieldData['value'])) {
					$fieldData['value'] = null;
				}

				$path = $section . '/' . $group . '/' . $field;
				/**
				 * Look for custom defined field path
				 */
				if (is_object($fieldConfig)) {
					$configPath = (string)$fieldConfig->config_path;
					if (!empty($configPath) && strrpos($configPath, '/') > 0) {
						// Extend old data with specified section group
						$groupPath = substr($configPath, 0, strrpos($configPath, '/'));
						if (!isset($oldConfigAdditionalGroups[$groupPath])) {
							$oldConfig = $this->extendConfig($groupPath, true, $oldConfig);
							$oldConfigAdditionalGroups[$groupPath] = true;
						}
						$path = $configPath;
					}
				}

				$inherit = !empty($fieldData['inherit']);
				$dataObject->setPath($path)
					->setValue($fieldData['value']);
				if (isset($oldConfig[$path])) {
					$dataObject->setConfigId($oldConfig[$path]['config_id']);
					/**
					* Delete config data if inherit
					*/
					if (!$inherit) {
						$saveTransaction->addObject($dataObject);
					}
					else {
						$deleteTransaction->addObject($dataObject);
					}
				}
				else if (!$inherit) {
					$dataObject->unsConfigId();
					$saveTransaction->addObject($dataObject);
				}
			}
		}

		$deleteTransaction->delete();
		$saveTransaction->save();
		return $this;
	}

	const _CLASS = __CLASS__;
}