<?php
/**
 * Обратите внимание, что мы перекрываем класс @see Mage_Adminhtml_Block_System_Config_Form
 * не посредством rewrite,
 * а иначе:
	<config>
		<sections>
			<df_shipping translate='label' module='df_shipping'>
				<frontend_model>Df_Adminhtml_Block_Config_Form</frontend_model>
			</df_shipping>
			<df_payment translate='label' module='df_payment'>
				<frontend_model>Df_Adminhtml_Block_Config_Form</frontend_model>
			</df_payment>
		</sections>
	</config>
 */
class Df_Adminhtml_Block_Config_Form extends Mage_Adminhtml_Block_System_Config_Form {
	/**
	 * @override
	 * @see Mage_Adminhtml_Block_System_Config_Form::initFields()
	 * @param Varien_Data_Form_Element_Fieldset $fieldset
	 * @param Varien_Simplexml_Element $group
	 * @param Varien_Simplexml_Element $section
	 * @param string $fieldPrefix
	 * @param string $labelPrefix
	 * @return Df_Adminhtml_Block_Config_Form
	 */
	public function initFields($fieldset, $group, $section, $fieldPrefix='', $labelPrefix='') {
		/** @var bool $patchNeeded */
		static $patchNeeded;
		if (is_null($patchNeeded)) {
			$patchNeeded = df_magento_version('1.4.1.0', '<');
		}
		!$patchNeeded
			? parent::initFields($fieldset, $group, $section, $fieldPrefix, $labelPrefix)
			: $this->initFields_1_4_0_1($fieldset, $group, $section, $fieldPrefix, $labelPrefix)
		;
		return $this;
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function _getAdditionalElementTypes() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array_merge(
				parent::_getAdditionalElementTypes()
				, rm_config_a('df/admin/config-form/element-types')
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param Varien_Data_Form_Element_Fieldset $fieldset
	 * @param Varien_Simplexml_Element $group
	 * @param Varien_Simplexml_Element $section
	 * @param string $fieldPrefix
	 * @param string $labelPrefix
	 * @return Mage_Adminhtml_Block_System_Config_Form
	 */
	private function initFields_1_4_0_1($fieldset, $group, $section, $fieldPrefix='', $labelPrefix='') {
		/** @var Df_Adminhtml_Model_Config_Data_1401 $dfConfig */
		$dfConfig = Df_Adminhtml_Model_Config_Data_1401::s();
		$dfConfig->load();
		foreach ($group->{'fields'} as $elements) {
			$elements = (array)$elements;
			// sort either by sort_order or by child node values bypassing the sort_order
			if ($group->{'sort_fields'} && $group->{'sort_fields'}->by) {
				$fieldset->setSortElementsByAttribute((string)$group->{'sort_fields'}->by,($group->{'sort_fields'}->direction_desc ? SORT_DESC : SORT_ASC)
				);
			} else {
				usort($elements, array($this, '_sortForm'));
			}
			foreach ($elements as $e) {
				if (!$this->_canShowField($e)) {
					continue;
				}
				/**********************************
				 * BEGIN PATCH
				 *********************************/

				//$path = $section->getName() . '/' . $group->getName() . '/' . $fieldPrefix . $e->getName();
				/**
				 * Look for custom defined field path
				 */
				$path = (string)$e->config_path;
				if (empty($path)) {
					$path = $section->getName() . '/' . $group->getName() . '/' . $fieldPrefix . $e->getName();
				} else if (strrpos($path, '/') > 0) {
					// Extend config data with new section group
					$groupPath = substr($path, 0, strrpos($path, '/'));
					if (!isset($configDataAdditionalGroups[$groupPath])) {
						$this->_configData = $dfConfig->extendConfig(
							$groupPath,false,$this->_configData
						);
						$configDataAdditionalGroups[$groupPath] = true;
					}
				}

				/**********************************
				 * END PATCH
				 *********************************/



				$id = $section->getName() . '_' . $group->getName() . '_' . $fieldPrefix . $e->getName();
				if (isset($this->_configData[$path])) {
					$data = $this->_configData[$path];
					$inherit = false;
				} else {
					$data = $this->_configRoot->descend($path);
					$inherit = true;
				}
				if ($e->frontend_model) {
					$fieldRenderer = Mage::getBlockSingleton((string)$e->frontend_model);
				} else {
					$fieldRenderer = $this->_defaultFieldRenderer;
				}

				$fieldRenderer->setForm($this);
				$fieldRenderer->setConfigData($this->_configData);
				$helperName = $this->_configFields->getAttributeModule($section, $group, $e);
				$fieldType  = (string)$e->frontend_type ? (string)$e->frontend_type : 'text';
				$name	   = 'groups['.$group->getName().'][fields]['.$fieldPrefix.$e->getName().'][value]';
				$label	  =  Mage::helper($helperName)->__($labelPrefix).' '.Mage::helper($helperName)->__((string)$e->label);
				$comment	= (string)$e->comment ? Mage::helper($helperName)->__((string)$e->comment) : '';
				$hint	   = (string)$e->hint ? Mage::helper($helperName)->__((string)$e->hint) : '';
				if ($e->backend_model) {
					$model = df_model((string)$e->backend_model);
					if (!$model instanceof Mage_Core_Model_Config_Data) {
						Mage::throwException('Invalid config field backend model: '.(string)$e->backend_model);
					}
					$model->setPath($path)->setValue($data)->afterLoad();
					$data = $model->getValue();
				}

				if ($e->depends) {
					foreach ($e->depends->children() as $dependent) {
						$dependentId = $section->getName() . '_' . $group->getName() . '_' . $fieldPrefix . $dependent->getName();
						$dependentValue = (string) $dependent;
						$this->_getDependence()
							->addFieldMap($id, $id)
							->addFieldMap($dependentId, $dependentId)
							->addFieldDependence($id, $dependentId, $dependentValue);
					}
				}
				/**
				 * Обратите внимание,
				 * что нельзя применять цепной вызов $fieldset->addField()->addField(),
				 * потому что @uses Varien_Data_Form_Element_Fieldset::addField()
				 * возвращает не $fieldset, а созданное поле.
				 */
				$field = $fieldset->addField($id, $fieldType, array(
					'name' => $name
					,'label' => $label
					,'comment' => $comment
					,'hint' => $hint
					,'value' => $data
					,'inherit' => $inherit
					,'class' => $e->frontend_class
					,'field_config' => $e
					,'scope' => $this->getScope()
					,'scope_id' => $this->getScopeId()
					,'scope_label' => $this->getScopeLabel($e)
					,'can_use_default_value' => $this->canUseDefaultValue((int)$e->show_in_default)
					,'can_use_website_value' => $this->canUseWebsiteValue((int)$e->show_in_website)
				));
				if (isset($e->validate)) {
					$field->addClass($e->validate);
				}

				if (isset($e->frontend_type) && 'multiselect' === (string)$e->frontend_type && isset($e->can_be_empty)) {
					$field->setCanBeEmpty(true);
				}

				$field->setRenderer($fieldRenderer);
				if ($e->source_model) {
					// determine callback for the source model
					$factoryName = (string)$e->source_model;
					$method = false;
					/** @var string[] $matches */
					$matches = array();
					if (1 === preg_match('/^([^:]+?)::([^:]+?)$/', $factoryName, $matches)) {
						df_assert_array($matches);
						array_shift($matches);
						list($factoryName, $method) = array_values($matches);
					}
					$sourceModel = Mage::getSingleton($factoryName);
					if ($sourceModel instanceof Varien_Object) {
						$sourceModel->setPath($path);
					}
					if ($method) {
						if ('multiselect' === $fieldType) {
							$optionArray = $sourceModel->$method();
						} else {
							$optionArray = array();
							foreach ($sourceModel->$method() as $value => $label) {
								$optionArray[]= rm_option($value, $label);
							}
						}
					}
					else {
						$optionArray = $sourceModel->toOptionArray('multiselect' === $fieldType);
					}
					$field->setValues($optionArray);
				}
			}
		}
		return $this;
	}
}