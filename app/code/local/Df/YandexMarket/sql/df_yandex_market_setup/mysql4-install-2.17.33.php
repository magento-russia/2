<?php
/** @var Df_Core_Model_Resource_Setup $this */
$this->startSetup();
/**
 * ������ ��� ��� ��� (�� ������ ���������):
		self::attribute()->addAdministrativeAttribute(
			$entityType = 'catalog_product'
			,$attributeId = Df_YandexMarket_Const::ATTRIBUTE__CATEGORY
			,$attributeLabel = '��������� ������.�������'
		);
 * ������ ���������� ������� ����� ���� � �������� � ����������� ������ 2.38.2.
 * ������� ��� ������, ������ ��� ���� ����������� ������� ��� �������������
 * � ��� ����� ���������� �� ������������� ��� �������������.
 *
 * ������� ��� ��� �� ������ ������� �� ��� �������,
 * ��� @see Df_Catalog_Model_Resource_Installer_Attribute::addAdministrativeAttribute()
 * ��������� �������� ����� �� ���� ������� ���������� ����� ������,
 * �� ����� �� ������ ������ ���������� ����� ��������
 * � ���������� ����������� � ������� ���������� ����� ������
 * (���������� ���� ������ ������� �� 2014-09-29 ������ 1� � ��������).
 *
 * �������� ��������, ��� ����������� ������� ��������������� ���������� ����� ������
 * ��� �������� �� ��������, ������ ��� ������� ���������� ����
 * ������ ��������� �� ������ ������-���� ��� ������������� ����������� ����
 * � ��������� ��� �������� ����� ����������� ����
 * (� ��� ����� � ����������� ���� ��������).
 *
 * �� ������ ������� ����� ����� ������ ��������� ���:
		self::attribute()->addAdministrativeCategoryAttribute(
			$attributeId = Df_YandexMarket_Const::ATTRIBUTE__CATEGORY
			,$attributeLabel = '��������� ������.�������'
		);
		df_eav_reset();
		Df_Catalog_Model_Category::reindexFlat();
 * ���� ��� �� ������ �������� �������� ���������� ������.�������
 * �� � �������, � � �������� ��������:
 * @see Df_Catalog_Model_Resource_Installer_Attribute::addAdministrativeCategoryAttribute()
 *
 * ������ �� � ��������� ����� ������ ����������� ��� �����������.
 * ���������� �������� ������� ���������� ������ �
 * @see Df_YandexMarket_Setup_AttributeSet::p()
 */
$this->endSetup();