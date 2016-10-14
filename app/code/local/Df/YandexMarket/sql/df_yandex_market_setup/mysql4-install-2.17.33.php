<?php
/** @var Df_Core_Model_Resource_Setup $this */
$this->startSetup();
/**
 * –аньше тут был код (не совсем идеальный):
		self::attribute()->addAdministrativeAttribute(
			$entityType = 'catalog_product'
			,$attributeId = Df_YandexMarket_Const::ATTRIBUTE__CATEGORY
			,$attributeLabel = ' атегори€ яндекс.ћаркета'
		);
 * “еперь улучшенный вариант этого кода € поместил в инсталл€тор версии 2.38.2.
 * ѕрежний код удалил, потому что есть возможность удалить его безболезненно
 * и тем самым избавитьс€ от необходимости его сопровождени€.
 *
 * ѕрежний код был не совсем идеален по той причине,
 * что @see Df_Catalog_Model_Resource_Installer_Attribute::addAdministrativeAttribute()
 * добавл€ет свойство сразу ко всем текущим прикладным типам товара,
 * но никак не решает задачу добавлени€ этого свойства
 * к программно создаваемым в будущем прикладным типам товара
 * (программно типы товара создают на 2014-09-29 модули 1— и ћой—клад).
 *
 * ќбратите внимание, что создаваемых вручную администратором прикладных типов товара
 * эта проблема не касалась, потому что вручную прикладные типы
 * всегда создаютс€ на основе какого-либо уже существующего прикладного типа
 * и наследуют все свойства этого прикладного типа
 * (в том числе и добавленные нами свойства).
 *
 * ¬о вторую попытку здесь сто€л вообще ошибочный код:
		self::attribute()->addAdministrativeCategoryAttribute(
			$attributeId = Df_YandexMarket_Const::ATTRIBUTE__CATEGORY
			,$attributeLabel = ' атегори€ яндекс.ћаркета'
		);
		rm_eav_reset();
		Df_Catalog_Model_Category::reindexFlat();
 * Ётот код по ошибке добавл€л свойство Ђ атегори€ яндекс.ћаркетаї
 * не к товарам, а к товарным разделам:
 * @see Df_Catalog_Model_Resource_Installer_Attribute::addAdministrativeCategoryAttribute()
 *
 * “еперь же € полностью убрал отсюда программный код установщика.
 * ƒобавление товарных свойств установщик делает в
 * @see Df_YandexMarket_Setup_AttributeSet::p()
 */
$this->endSetup();