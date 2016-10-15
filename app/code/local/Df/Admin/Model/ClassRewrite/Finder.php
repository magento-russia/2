<?php
class Df_Admin_Model_ClassRewrite_Finder extends Df_Core_Model {
	/** @return Df_Admin_Model_ClassRewrite_Collection */
	public function getRewrites() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Admin_Model_ClassRewrite_Collection $result */
			$result = Df_Admin_Model_ClassRewrite_Collection::i();
			foreach ($this->getModulesConfiguration() as $filePath => $moduleConfiguration) {
				/** @var string $filePath */
				/** @var Df_Varien_Simplexml_Config $moduleConfiguration */
				/** @var \Df\Xml\X $xmlGlobal */
				$xmlGlobal = $moduleConfiguration->getNode()->{'global'};
				if ($xmlGlobal) {
					foreach (Df_Admin_Model_ClassInfo::classTypeMap() as $xmlKey => $classType) {
						/** @var \Df\Xml\X $xmlBlocks */
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
			$this->{__METHOD__} = df_leaf_b(rm_config_node('global/disable_local_modules'));
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => Df_Varien_Simplexml_Config) */
	private function getModulesConfiguration() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => Df_Varien_Simplexml_Config) $result */
			$result = array();
			/** @var array(string => Mage_Core_Model_Config_Element) $moduleDeclarations */
			$moduleDeclarations = rm_config_node('modules')->children();
			/** @var string[] $configFileBaseNames */
			$configFileBaseNames = array('config.xml', $this->getResourceConfigFileName());
			foreach ($moduleDeclarations as $moduleName => $moduleDeclaration) {
				/** @var string $moduleName */
				/** @var Mage_Core_Model_Config_Element $moduleDeclaration */
				/**
				 * 2015-02-06
				 * Метод @uses Mage_Core_Model_Config_Element::is() возвращает true,
				 * если данный узел XML $fieldConfig содержит дочерний узел с заданным именем.
				 */
				if (
						$moduleDeclaration->is('active')
					&&
						(
								!$this->areLocalModulesDisabled()
							||
								('local' !== df_leaf_s($moduleDeclaration->{'codePool'}))
						)
				) {
					foreach ($configFileBaseNames as $configFileBaseName) {
						/** @var string $configFileBaseName */
						$configFileName =
							df_cc_path(
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
			$resourceConfigNameSuffix = df_leaf_sne($resourceConnectionConfig->{'model'});
			$this->{__METHOD__} = sprintf('config.%s.xml', $resourceConfigNameSuffix);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param Df_Admin_Model_ClassRewrite_Collection $rewrites
	 * @param \Df\Xml\X $e
	 * @param string $type
	 * @param string $moduleName
	 * @param string $filePath
	 * @return Df_Admin_Model_ClassRewrite_Finder
	 */
	private function parseRewrites(
		Df_Admin_Model_ClassRewrite_Collection $rewrites
		,\Df\Xml\X $e
		,$type
		,$moduleName
		,$filePath
	) {
		foreach ($e->children() as $moduleNameMf => $child) {
			/** @var string $moduleNameMf */
			/** @var \Df\Xml\X $child */
			/** @var \Df\Xml\X $xmlRewrite */
			$xmlRewrite = $child->{'rewrite'};
			if ($xmlRewrite) {
				foreach ($xmlRewrite->children() as $originSuffixMf => $xmlDestinationClassName) {
					/** @var string $originSuffixMf */
					/** @var \Df\Xml\X $xmlDestinationClassName */
					/** @var string $destinationClassName */
					$destinationClassName = df_leaf_s($xmlDestinationClassName);
					/** @var string $originClassNameMf */
					$originClassNameMf = $moduleNameMf . '/' . $originSuffixMf;
					/** @var Df_Admin_Model_ClassRewrite|null $rewrite */
					$rewrite = $rewrites->getByOrigin($type, $originClassNameMf);
					if (!$rewrite) {
						$rewrite = Df_Admin_Model_ClassRewrite::i(Df_Admin_Model_ClassInfo::i_mf(
							$type, $originClassNameMf
						));
						$rewrites->addItem($rewrite);
					}
					if (!$rewrite->getDestinations()->getItemById($destinationClassName)) {
						$rewrite->getDestinations()->addItem(Df_Admin_Model_ClassInfo::i(
							$type, $destinationClassName, $moduleName, $filePath
						));
					}
				}
			}
		}
		return $this;
	}

	/** @return Df_Admin_Model_ClassRewrite_Finder */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}