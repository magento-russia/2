<?php
/** @var Df_Core_Model_Resource_Setup $this */
$this->startSetup();
/**
 * Этот код обновлял модуль Яндекс.Маркет
 * с версии 2.17.33 (2013-06-28) до версии 2.17.46 (2013-07-17).
 * Здесь стоял пограммный код:
		Df_Catalog_Model_Resource_Installer_Attribute::s()->updateAttribute(
			$entityTypeId = Mage_Catalog_Model_Product::ENTITY
			,$id = Df_YandexMarket_Const::ATTRIBUTE__CATEGORY
			,$field = 'backend_model'
			,$value = Df_YandexMarket_Model_System_Config_Backend_Category::_CLASS
		);
 * Теперь программный код добавления и настройки товарных свойств перенесён в версию 2.38.2:
 * @see Df_YandexMarket_Model_Setup_2_38_2::process()
 */
$this->endSetup();