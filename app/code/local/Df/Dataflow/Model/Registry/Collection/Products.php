<?php
class Df_Dataflow_Model_Registry_Collection_Products extends Df_Dataflow_Model_Registry_Collection {
	/**
	 * @override
	 * @return Df_Catalog_Model_Resource_Product_Collection
	 */
	protected function createCollection() {
		/** @var Df_Catalog_Model_Resource_Product_Collection $result */
		$result =
			/**
			 * Отключение денормализации позволяет иметь в коллекции товаров все необходимые нам свойства.
			 * Вместо отключения денормализации есть и другой способ иметь все необходиые свойства:
			 * указать в установочном скрипте,
			 * что требуемые свойства должны попадать в коллекцию в режиме денормализации.
			 * @see Df_Shipping_Model_Setup_2_16_2::process()
			 * Однако методу Df_1C_Model_Cml2_Import_Processor_Product_Type::getDescription()
			 * требуется, чтобы в коллекции товаров присутствовало свойство «описание».
			 * Однако значения поля «описание» могут быть очень длинными,
			 * и если добавить колонку для этого свойства в денормализованную таблицу товаров,
			 * то тогда мы можем превысить устанавливаемый MySQL предел для одной строки таблицы
			 *
			 * «Magento по умолчанию отводит на хранение значения одного свойства товара
			 * в своей базе данных 255 символов, для хранения которых MySQL выделяет 255 * 3 + 2 = 767 байтов.
			 * Magento объединяет все свойства товаров в единой расчётной таблице,
			 * колонками которой служат свойства, а строками — товары.
			 * Если свойств товаров слишком много,
			 * то Magento превышает системное ограничение MySQL на одну строку таблицы:
			 * 65535 байтов,что приводит к сбою построения расчётной таблицы товаров»
			 *
			 * Либо же значение поля описание будет обрезаться в соответствии с установленным администратором
			 * значением опции «Российская сборка» → «Административная часть» → «Расчётные таблицы» →
			 * «Максимальное количество символов для хранения значения свойства товара».
			 */
			Df_Catalog_Model_Resource_Product_Collection::i(array(
				Df_Catalog_Model_Resource_Product_Collection::P__DISABLE_FLAT => true
			))
		;
		$result->setStore($this->getStore());
		/**
		 * По мотивам модуля Яндекс.Маркет
		 */
		$result->addStoreFilter($this->getStore());
		$result->addAttributeToSelect(array(
			/**
			 * Нужно методу
			 * @see Df_1C_Model_Cml2_Import_Processor_Product_Type::getDescriptionAbstract()
			 *
			 * 2014-09-18
			 * Отныне нужно также модулю «Русификация» для перевода демо-данных
			 * @see Df_Localization_Model_Onetime_Dictionary_Rule::getAllProducts().
			 */
			Df_Catalog_Model_Product::P__DESCRIPTION
			,Df_Eav_Const::ENTITY_EXTERNAL_ID
			,Df_Catalog_Model_Product::P__NAME
			,Df_Catalog_Model_Product::P__PRICE
			/**
			 * Нужно методу
			 * @see Df_1C_Model_Cml2_Import_Processor_Product_Type::getDescriptionAbstract()
			 *
			 * 2014-09-18
			 * Отныне нужно также модулю «Русификация» для перевода демо-данных
			 * @see Df_Localization_Model_Onetime_Dictionary_Rule::getAllProducts().
			 */
			,Df_Catalog_Model_Product::P__SHORT_DESCRIPTION
			,Df_Catalog_Model_Product::P__SKU
			/**
			 * Нужно методу
			 * Df_1C_Model_Cml2_Import_Processor_Product_Type::getProductDataNewOrUpdateBase
			 */
			,Df_Catalog_Model_Product::P__WEIGHT
		));
		/**
		 * По мотивам модуля Яндекс.Маркет.
		 *
		 * Обратите внимание, что метод addCategoryIds
		 * работает только после загрузки коллекции.
		 * Товарные разделы нужны нам
		 * в методе Df_1C_Model_Cml2_Import_Processor_Product_Type::getProductDataNewOrUpdateBase.
		 *
		 * Метод Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection::addCategoryIds
		 * отсутствует в Magento CE 1.4.0.1
		 */
		$result->load();
		if (method_exists($result, 'addCategoryIds')) {
			call_user_func(array($result, 'addCategoryIds'));
		}
		else {
			$result->addCategoryIdsRm();
		}
		return $result;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getEntityClass() {return Df_Catalog_Model_Product::_CLASS;}

	/**
	 * @override
	 * @return Mage_Core_Model_Store
	 */
	protected function getStoreDefault() {return df()->registry()->getStoreProcessed();}

	/**
	 * @override
	 * @param Mage_Core_Model_Abstract|Df_Catalog_Model_Product $entity
	 * @return void
	 */
	protected function saveEntity(Mage_Core_Model_Abstract $entity) {$entity->saveRm();}

	/**
	 * @override
	 * @param Mage_Core_Model_Abstract|Df_Catalog_Model_Product $entity
	 * @param Mage_Core_Model_Store $store
	 * @return Object
	 */
	protected function setStoreToEntity(
		Mage_Core_Model_Abstract $entity, Mage_Core_Model_Store $store
	) {
		$entity->setStoreId($store->getId());
	}

	/**
	 * @param Mage_Core_Model_Store|null $store [optional]
	 * @return Df_Dataflow_Model_Registry_Collection_Products
	 */
	public static function s($store = null) {
		if (!$store) {
			$store = df()->registry()->getStoreProcessed();
		}
		if (!isset(self::$_s[$store->getId()])) {
			self::$_s[$store->getId()] = new self(array(self::$P__STORE => $store));
		}
		return self::$_s[$store->getId()];
	}
	/** @var array(int => Df_Dataflow_Model_Registry_Collection_Products)  */
	private static $_s = array();
}