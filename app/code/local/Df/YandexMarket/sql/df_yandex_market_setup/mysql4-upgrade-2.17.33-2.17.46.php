<?php
/** @var Df_Core_Model_Resource_Setup $this */
$this->startSetup();
/**
 * ���� ��� �������� ������ ������.������
 * � ������ 2.17.33 (2013-06-28) �� ������ 2.17.46 (2013-07-17).
 * ����� ����� ���������� ���:
		Df_Catalog_Model_Resource_Installer_Attribute::s()->updateAttribute(
			$entityTypeId = Mage_Catalog_Model_Product::ENTITY
			,$id = Df_YandexMarket_Const::ATTRIBUTE__CATEGORY
			,$field = 'backend_model'
			,$value = Df_YandexMarket_Model_Config_Backend_Category::class
		);
 * ������ ����������� ��� ���������� � ��������� �������� ������� �������� �
 * @see Df_YandexMarket_Setup_AttributeSet::p()
 */
$this->endSetup();