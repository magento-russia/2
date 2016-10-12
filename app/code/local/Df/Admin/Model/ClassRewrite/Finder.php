<?php
class Df_Admin_Model_ClassRewrite_Finder extends Df_Core_Model {
	/** @return Df_Admin_Model_ClassRewrite_Collection */
	public function getRewrites() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Admin_Model_ClassRewrite_Collection $result */
			$result = Df_Admin_Model_ClassRewrite_Collection::i();
			/** @var array(string => string) $classTypeMap */
			$classTypeMap = array(
				'blocks' => Df_Admin_Model_ClassInfo::TYPE__BLOCK
				,'helpers' => Df_Admin_Model_ClassInfo::TYPE__HELPER
				,'models' => Df_Admin_Model_ClassInfo::TYPE__MODEL
			);
			foreach ($this->getModulesConfiguration() as $filePath => $moduleConfiguration) {
				/** @var string $filePath */
				/** @var Df_Varien_Simplexml_Config $moduleConfiguration */
				/** @var Df_Varien_Simplexml_Element $xmlGlobal */
				$xmlGlobal = $moduleConfiguration->getNode()->{'global'};
				if ($xmlGlobal) {
					foreach ($classTypeMap as $xmlKey => $classType) {
						/** @var Df_Varien_Simplexml_Element $xmlBlocks */
						$xml = $xmlGlobal->{$xmlKey};
						if ($xml) {
							$this->parseRewrites(
								$result
								, $xml
								, $classType
								, $moduleConfiguration->getModuleName()
								, $filePath
							);
						}
					}
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function areLocalModulesDisabled() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|bool $resultAsString */
			$resultAsString = (string)Mage::app()->getConfig()->getNode('global/disable_local_modules');
			$this->{__METHOD__} =
				$resultAsString && in_array($resultAsString, array('1', 'true'), $strict = true)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => Df_Varien_Simplexml_Config) */
	private function getModulesConfiguration() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => Df_Varien_Simplexml_Config) $result */
			$result = array();
			/** @var array(string => Mage_Core_Model_Config_Element) $moduleDeclarations */
			$moduleDeclarations = Mage::app()->getConfig()->getNode('modules')->children();
			/** @var string[] $configFileBaseNames */
			$configFileBaseNames = array('config.xml', $this->getResourceConfigFileName());
			foreach ($moduleDeclarations as $moduleName => $moduleDeclaration) {
				/** @var string $moduleName */
				/** @var Mage_Core_Model_Config_Element $moduleDeclaration */
				if (
						$moduleDeclaration->is('active')
					&&
						(
								!$this->areLocalModulesDisabled()
							||
								('local' !== (string)$moduleDeclaration->{'codePool'})
						)
				) {
					foreach ($configFileBaseNames as $configFileBaseName) {
						/** @var string $configFileBaseName */
						$configFileName =
							df_concat_path(
								Mage::app()->getConfig()->getModuleDir('etc', $moduleName)
								, $configFileBaseName
							)
						;
						/** @var Df_Varien_Simplexml_Config $moduleConfig */
						$moduleConfig = new Df_Varien_Simplexml_Config();
						if ($moduleConfig->loadFile($configFileName)) {
							$result[$configFileName]= $moduleConfig;
						}
					}
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getResourceConfigFileName() {
		if (!isset($this->{__METHOD__})) {
			/** @var Varien_Simplexml_Element $resourceConnectionConfig */
			$resourceConnectionConfig =
				/**
				 * Константа @see Mage_Core_Model_Resource::DEFAULT_SETUP_RESOURCE
				 * отсутствует в Magento CE 1.4.
				 */
				Mage::app()->getConfig()->getResourceConnectionConfig('core_setup')
			;
			df_assert($resourceConnectionConfig instanceof Varien_Simplexml_Element);
			/** @var string $resourceConfigNameSuffix */
			$resourceConfigNameSuffix = (string)$resourceConnectionConfig->{'model'};
			df_assert_string_not_empty($resourceConfigNameSuffix);
			$this->{__METHOD__} = sprintf('config.%s.xml', $resourceConfigNameSuffix);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param Df_Admin_Model_ClassRewrite_Collection $rewrites
	 * @param Df_Varien_Simplexml_Element $xml
	 * @param string $type
	 * @param string $moduleName
	 * @param string $filePath
	 * @return Df_Admin_Model_ClassRewrite_Finder
	 */
	private function parseRewrites(
		Df_Admin_Model_ClassRewrite_Collection $rewrites
		,Df_Varien_Simplexml_Element $xml
		,$type
		,$moduleName
		,$filePath
	) {
		foreach ($xml->children() as $moduleNameMf => $child) {
			/** @var string $moduleNameMf */
			/** @var Df_Varien_Simplexml_Element $child */
			/** @var Df_Varien_Simplexml_Element $xmlRewrite */
			$xmlRewrite = $child->{'rewrite'};
			if ($xmlRewrite) {
				foreach ($xmlRewrite->children() as $originSuffixMf => $xmlDestinationClassName) {
					/** @var string $originSuffixMf */
					/** @var Df_Varien_Simplexml_Element $xmlDestinationClassName */
					/** @var string $destinationClassName */
					$destinationClassName = (string)$xmlDestinationClassName;
					/** @var string $originClassNameMf */
					$originClassNameMf = $moduleNameMf . '/' . $originSuffixMf;
					/** @var Df_Admin_Model_ClassRewrite|null $rewrite */
					$rewrite = $rewrites->getByOrigin($type, $originClassNameMf);
					if (!$rewrite) {
						$rewrite =
							Df_Admin_Model_ClassRewrite::i(
								Df_Admin_Model_ClassInfo::i(array(
									Df_Admin_Model_ClassInfo::P__NAME_MF => $originClassNameMf
									, Df_Admin_Model_ClassInfo::P__TYPE => $type
								))
							)
						;
						$rewrites->addItem($rewrite);
					}
					if (!$rewrite->getDestinations()->getItemById($destinationClassName)) {
						$rewrite->getDestinations()->addItem(
							Df_Admin_Model_ClassInfo::i(array(
								Df_Admin_Model_ClassInfo::P__CONFIG_FILE_PATH => $filePath
								, Df_Admin_Model_ClassInfo::P__MODULE_NAME => $moduleName
								, Df_Admin_Model_ClassInfo::P__NAME => $destinationClassName
								, Df_Admin_Model_ClassInfo::P__TYPE => $type
							))
						);
					}
				}
			}
		}
		return $this;
	}

	/** @return Df_Admin_Model_ClassRewrite_Finder */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}