<?php
/** @var Df_Core_Model_Resource_Setup $this */
$this->startSetup();
/**
 * Раньше тут был код (не совсем идеальный):
		self::attribute()->addAdministrativeAttribute(
			$entityType = 'catalog_product'
			,$attributeId = \Df\YandexMarket\ConstT::ATTRIBUTE__CATEGORY
			,$attributeLabel = 'Категория Яндекс.Маркета'
		);
 * Теперь улучшенный вариант этого кода я поместил в инсталлятор версии 2.38.2.
 * Прежний код удалил, потому что есть возможность удалить его безболезненно
 * и тем самым избавиться от необходимости его сопровождения.
 *
 * Прежний код был не совсем идеален по той причине,
 * что @see Df_Catalog_Model_Resource_Installer_Attribute::addAdministrativeAttribute()
 * добавляет свойство сразу ко всем текущим прикладным типам товара,
 * но никак не решает задачу добавления этого свойства
 * к программно создаваемым в будущем прикладным типам товара
 * (программно типы товара создают на 2014-09-29 модули 1С и МойСклад).
 *
 * Обратите внимание, что создаваемых вручную администратором прикладных типов товара
 * эта проблема не касалась, потому что вручную прикладные типы
 * всегда создаются на основе какого-либо уже существующего прикладного типа
 * и наследуют все свойства этого прикладного типа
 * (в том числе и добавленные нами свойства).
 */
/**
 * Во вторую попытку здесь стоял вообще ошибочный код:
		self::attribute()->addAdministrativeCategoryAttribute(
			$attributeId = \Df\YandexMarket\ConstT::ATTRIBUTE__CATEGORY
			,$attributeLabel = 'Категория Яндекс.Маркета'
		);
		rm_eav_reset();
		Df_Catalog_Model_Category::reindexFlat();
 * Этот код по ошибке добавлял свойство «Категория Яндекс.Маркета»
 * не к товарам, а к товарным разделам:
 * @see Df_Catalog_Model_Resource_Installer_Attribute::addAdministrativeCategoryAttribute()
 *
 */
/**
 * Теперь же я полностью убрал отсюда программный код установщика.
 * Добавление товарных свойств установщик делает в версии 2.38.2:
 * @see Df_YandexMarket_Model_Setup_2_38_2::process()
 */
$this->endSetup();