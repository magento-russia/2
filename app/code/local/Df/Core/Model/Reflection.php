<?php
class Df_Core_Model_Reflection extends Df_Core_Model_DestructableSingleton {
	/**
	 * @param string $className		Например:	«Df_SalesRule_Model_Event_Validator_Process»
	 * @return string
	 */
	public function getModelNameInMagentoFormat($className) {
		if (!isset($this->{__METHOD__}[$className])) {
			$classNameParts = explode(self::PARTS_SEPARATOR, $className);
			/**
			 * @var array $classNameParts
			 * Например:	[«Df», «SalesRule», «Model», «Event», «Validator», «Process»]
			 */
			/**
			 * @var string $moduleName
			 * Например:	«Df_SalesRule»
			 */
			$moduleName =
				implode(
					self::PARTS_SEPARATOR
					,array_slice($classNameParts, 0, self::MODULE_NAME_PARTS_COUNT)
				)
			;
			/** @var string $entityType */
			$entityType = strtolower(df_a($classNameParts, 2));
			/**
			 * @var string $moduleNameInMagentoFormat
			 * Например:	«df_sales_rule»
			 */
			$moduleNameInMagentoFormat =
				$this->getModuleNameInMagentoFormat(
					$moduleName
					,$entityType
				)
			;
			/**
			 * @var string|null $classNameWithoutModuleNameInMagentoFormat
			 * Например:	«event_validator_process»
			 */
			$classNameWithoutModuleNameInMagentoFormat = null;
			/**
			 * Для главного хелпера data всегда используем краткую нотацию
			 * (Df_Directory_Helper_Data => df_directory).
			 *
			 * Длинную нотацию (df_directory/data) Magento так же понимает,
			 * однако её использование приведёт к дублированию объектов-одиночек в реестре
			 * (они там идентифицируются по имени в формате Magento)
			 */
			if (
					(self::ENTITY_TYPE__HELPER === $entityType)
				&&
					(4 === count($classNameParts))
				&&
					'data' === strtolower(df_a($classNameParts, 3))
			) {
				$classNameWithoutModuleNameInMagentoFormat = null;
			}
			else {
				$classNameWithoutModuleNameInMagentoFormat =
					implode(
						self::PARTS_SEPARATOR
						,array_map(
							'df_lcfirst'
							,array_slice(
								$classNameParts
								,// +1, чтобы пропустить слово «model» или «block»
								self::MODULE_NAME_PARTS_COUNT + 1
							)
						)
					)
				;
			}
			$this->{__METHOD__}[$className] = rm_concat_clean(self::MODULE_NAME_SEPARATOR
				,$moduleNameInMagentoFormat
				,$classNameWithoutModuleNameInMagentoFormat
			);
			$this->markCachedPropertyAsModified(__METHOD__);
		}
		return $this->{__METHOD__}[$className];
	}

	/**
	 * @param string $className
	 * @return string
	 */
	public function getModuleName($className) {
		df_param_string($className, 0);
		if (!isset($this->{__METHOD__}[$className])) {
			$this->{__METHOD__}[$className] =
				implode(
					self::PARTS_SEPARATOR
					,array_slice(
						explode(self::PARTS_SEPARATOR, $className)
						,0
						,self::MODULE_NAME_PARTS_COUNT
					)
				)
			;
			$this->markCachedPropertyAsModified(__METHOD__);
		}
		return $this->{__METHOD__}[$className];
	}

	/**
	 * @param string $className		Например:	«Df_SalesRule_Model_Resource_Order_Collection»
	 * @return string
	 */
	public function getResourceNameInMagentoFormat($className) {
		if (!isset($this->{__METHOD__}[$className])) {
			$classNameParts = explode(self::PARTS_SEPARATOR, $className);
			/**
			 * @var array $classNameParts
			 * Например:	[«Df», «SalesRule», «Model», «Resource», «Order», «Collection»]
			 * Например:	[«Df», «PromoGift», «Model», «Resource», «Indexer»]
			 */
			/**
			 * @var string $moduleName
			 * Например:	«Df_SalesRule»
			 * Например:	«Df_PromoGift»
			 */
			$moduleName =
				implode(
					self::PARTS_SEPARATOR
					,array_slice($classNameParts, 0, self::MODULE_NAME_PARTS_COUNT)
				)
			;
			df_assert_eq(self::ENTITY_TYPE__MODEL, strtolower(df_a($classNameParts, 2)));
			/**
			 * @var string $moduleNameInMagentoFormat
			 * Например:	«df_sales_rule»
			 * Например:	«df_promo_gift»
			 */
			$moduleNameInMagentoFormat =
				$this->getModuleNameInMagentoFormat(
					$moduleName
					,self::ENTITY_TYPE__MODEL
				)
			;
			/** @var string $resourcePrefix */
			$resourcePrefix =
				(string)
					Mage::getConfig()->getNode()->global->models->{strtolower($moduleNameInMagentoFormat)}
						->resourceModel
			;
			if (!$resourcePrefix) {
				df_error(
					'Не могу найти ресурсную модель «global/models/%s/resourceModel»'
					,strtolower($moduleNameInMagentoFormat)
				);
			}
			df_assert_string_not_empty($resourcePrefix);
			/**
			 * @var string|null $classNameWithoutModuleNameInMagentoFormat
			 * Например: «event_validator_process»
			 */
			$classNameWithoutModuleNameInMagentoFormat =
				implode(
					self::PARTS_SEPARATOR
					,array_map(
						'df_lcfirst'
						,array_slice(
							$classNameParts
							,// +2, чтобы пропустить слова «model» и «resource»
							self::MODULE_NAME_PARTS_COUNT + 2
						)
					)
				)
			;
			$this->{__METHOD__}[$className] = rm_concat_clean(self::MODULE_NAME_SEPARATOR
				, $moduleNameInMagentoFormat
				, $classNameWithoutModuleNameInMagentoFormat
			);
			$this->markCachedPropertyAsModified(__METHOD__);
		}
		return $this->{__METHOD__}[$className];
	}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getPropertiesToCache() {
		return self::m(__CLASS__,
			'getModelNameInMagentoFormat'
			,'getModuleName'
			,'getModuleNameInMagentoFormat'
			,'getResourceNameInMagentoFormat'
		);
	}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getPropertiesToCacheSimple() {return $this->getPropertiesToCache();}

	/**
	 * Почему бы не сохранять в межстраничном кэше?
	 * Например, для модуля «Df_PromoGift» метод вернёт: «df_promo_gift»
	 * @param string $moduleName
	 * @param string $entityType[optional]
	 * @return string
	 */
	private function getModuleNameInMagentoFormat($moduleName, $entityType = self::ENTITY_TYPE__MODEL) {
		if (!isset($this->{__METHOD__}[$moduleName][$entityType])) {
			/** @var string $result */
			$result = null;
			/** @var array $entityTypeUcFirst */
			static $entityTypeUcFirst = array();
			if (!isset($entityTypeUcFirst[$entityType])) {
				$entityTypeUcFirst[$entityType] = ucfirst($entityType);
			}
			/** @var array $entityTypePlural */
			static $entityTypePlural = array();
			if (!isset($entityTypePlural[$entityType])) {
				$entityTypePlural[$entityType] =
					rm_sprintf('%ss', $entityType)
				;
			}
			/**
			 * @var string $modelPrefix
			 * Например:	«Df_PromoGift_Model»
			 */
			$modelPrefix =
				implode(
					self::PARTS_SEPARATOR
					,array($moduleName, $entityTypeUcFirst[$entityType])
				)
			;
			/** @var Varien_Simplexml_Element $config */
			$config = Mage::getConfig()->getNode();
			$nodes =
				$config->xpath(
					'global/' . $entityTypePlural[$entityType] . '/*/class[. = "' . $modelPrefix . '"]'
				)
			;
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

	const ENTITY_TYPE__BLOCK = 'block';
	const ENTITY_TYPE__HELPER = 'helper';
	const ENTITY_TYPE__MODEL = 'model';
	const MODEL_PREFIX_FIELD_NAME = 'class';
	/** Количество частей в названии модуля */
	const MODULE_NAME_PARTS_COUNT = 2;
	/** Разделитель между частями в названии класса */
	const PARTS_SEPARATOR = '_';
	/** Разделитель между названием модуля и названием класса внутри модуля */
	const MODULE_NAME_SEPARATOR = '/';

	/** @return Df_Core_Model_Reflection */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}