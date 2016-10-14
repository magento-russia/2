<?php
class Df_Core_Model_Reflection extends Df_Core_Model {
	/**
	 * «Df_1C_Cml2_Action_Catalog_Export_Process» => «cml2.action.catalog.export.process»
	 * «Df_1C_Cml2_Export_Document_Catalog» => «export.document.catalog»
	 * @param Mage_Core_Model_Abstract $model
	 * @param string $separator
	 * @param int $offsetLeft [optional]
	 * @return string
	 */
	public function getModelId(Mage_Core_Model_Abstract $model, $separator, $offsetLeft = 0) {
		/** @var string $className */
		$className = get_class($model);
		if (!isset($this->{__METHOD__}[$className][$separator][$offsetLeft])) {
			$this->{__METHOD__}[$className][$separator][$offsetLeft] =
				implode('.', array_slice(df_t()->lcfirst(rm_explode_class($className)), 3 + $offsetLeft))
			;
		}
		return $this->{__METHOD__}[$className][$separator][$offsetLeft];
	}

	/**
	 * «Df_SalesRule_Model_Event_Validator_Process» => «df_sales_rule/event_validator_process»
	 * @param string $className
	 * @return string
	 */
	public function getModelNameInMagentoFormat($className) {
		if (!isset($this->{__METHOD__}[$className])) {
			/**
			 * @var array $classNameParts
			 * Например: [«Df», «SalesRule», «Model», «Event», «Validator», «Process»]
			 */
			$classNameParts = rm_explode_class($className);
			/**
			 * @var string $moduleName
			 * Например: «Df_SalesRule»
			 */
			$moduleName = df_concat_class(array_slice($classNameParts, 0, 2));
			/** @var string $entityType */
			$entityType = strtolower(dfa($classNameParts, 2));
			/**
			 * @var string $moduleNameInMagentoFormat
			 * Например: «df_sales_rule»
			 */
			$moduleNameInMagentoFormat = $this->getModuleNameInMagentoFormat($moduleName, $entityType);
			/**
			 * @var string|null $classNameWithoutModuleNameInMagentoFormat
			 * Например:	«event_validator_process»
			 */
			$classNameWithoutModuleNameInMagentoFormat =
				/**
				 * Для главного хелпера data всегда используем краткую нотацию
				 * (Df_Directory_Helper_Data => df_directory).
				 *
				 * Длинную нотацию (df_directory/data) Magento так же понимает,
				 * однако её использование приведёт к дублированию объектов-одиночек в реестре
				 * (они там идентифицируются по имени в формате Magento)
				 */
				'helper' === $entityType
				&& 4 === count($classNameParts)
				&& 'data' === strtolower(dfa($classNameParts, 3))
				? null
				// +1, чтобы пропустить слово «model» или «block»
				: df_concat_class(df_t()->lcfirst(array_slice($classNameParts, 2 + 1)))
			;
			$this->{__METHOD__}[$className] = df_ccc(self::$MODULE_NAME_SEPARATOR
				,$moduleNameInMagentoFormat
				,$classNameWithoutModuleNameInMagentoFormat
			);
			$this->markCachedPropertyAsModified(__METHOD__);
		}
		return $this->{__METHOD__}[$className];
	}

	/**
	 * @used-by rm_module_name()
	 * «Df_SalesRule_Model_Event_Validator_Process» => «Df_SalesRule»
	 * @param string $className
	 * @return string
	 */
	public function getModuleName($className) {
		if (!isset($this->{__METHOD__}[$className])) {
			$this->{__METHOD__}[$className] = df_concat_class(
				array_slice(rm_explode_class($className), 0, 2)
			);
			$this->markCachedPropertyAsModified(__METHOD__);
		}
		return $this->{__METHOD__}[$className];
	}

	/**
	 * Намеренно добавили к названию метода окончание «ByClass»,
	 * чтобы название метода не конфликтовало с родительским методом
	 * @see Df_Core_Model::moduleTitle()
	 * «Df_1C_Cml2_Export_Document_Catalog» => «1C:Управление торговлей»
	 * @param string $className
	 * @return string
	 */
	public function getModuleTitleByClass($className) {
		if (!isset($this->{__METHOD__}[$className])) {
			/** @var string $moduleName */
			$moduleName = $this->getModuleName($className);
			$this->{__METHOD__}[$className] = rm_leaf_s(
				rm_config_node('modules', $moduleName,  'title'), $moduleName
			);
			$this->markCachedPropertyAsModified(__METHOD__);
		}
		return $this->{__METHOD__}[$className];
	}

	/**
	 * @override
	 * @see Df_Core_Model::cachedGlobal()
	 * @return string[]
	 */
	protected function cachedGlobal() {
		return self::m(__CLASS__,
			'getModelNameInMagentoFormat'
			,'getModuleName'
			/**
			 * Намеренно добавили к названию метода окончание «ByClass»,
			 * чтобы название метода не конфликтовало с родительским методом
			 * @see Df_Core_Model::moduleTitle()
			 */
			,'getModuleTitleByClass'
			,'getModuleNameInMagentoFormat'
		);
	}

	/**
	 * Почему бы не сохранять в межстраничном кэше?
	 * Например, для модуля «Df_PromoGift» метод вернёт: «df_promo_gift»
	 * @param string $moduleName
	 * @param string $entityType [optional]
	 * @return string
	 */
	private function getModuleNameInMagentoFormat($moduleName, $entityType = 'model') {
		if (!isset($this->{__METHOD__}[$moduleName][$entityType])) {
			/** @var string $result */
			$result = null;
			/** @var array $entityTypeUcFirst */
			static $entityTypeUcFirst = array();
			if (!isset($entityTypeUcFirst[$entityType])) {
				/**
				 * Намеренно используем @uses ucfirst() вместо @see df_ucfirst()
				 * потому что в данном случае нам не нужна поддержка UTF-8.
				 */
				$entityTypeUcFirst[$entityType] = ucfirst($entityType);
			}
			/** @var array $entityTypePlural */
			static $entityTypePlural = array();
			if (!isset($entityTypePlural[$entityType])) {
				$entityTypePlural[$entityType] = $entityType . 's';
			}
			/**
			 * @var string $modelPrefix
			 * Например: «Df_PromoGift_Model»
			 */
			$modelPrefix = df_concat_class($moduleName, $entityTypeUcFirst[$entityType]);
			/** @var Varien_Simplexml_Element $config */
			$config = Mage::getConfig()->getNode();
			$nodes = $config->xpath(
				'global/' . $entityTypePlural[$entityType] . '/*/class[. = "' . $modelPrefix . '"]'
			);
			if (is_array($nodes)) {
				foreach ($nodes as $node) {
					/** @var Varien_Simplexml_Element $node */
					if ((string)$node === $modelPrefix) {
						/** @var Varien_Simplexml_Element $parent */
						$parent = $node->getParent();
						$result = $parent->getName();
						break;
					}
				}
			}
			$this->{__METHOD__}[$moduleName][$entityType] = $result;
			$this->markCachedPropertyAsModified(__METHOD__);
		}
		return $this->{__METHOD__}[$moduleName][$entityType];
	}

	/** Разделитель между названием модуля и названием класса внутри модуля */
	private static $MODULE_NAME_SEPARATOR = '/';

	/** @return Df_Core_Model_Reflection */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}