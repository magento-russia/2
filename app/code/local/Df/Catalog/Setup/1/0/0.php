<?php
class Df_Catalog_Setup_1_0_0 extends Df_Core_Setup {
	/**
	 * @override
	 * @see Df_Core_Setup::_process()
	 * @used-by Df_Core_Setup::process()
	 * @return void
	 */
	protected function _process() {
		/** @var Df_Catalog_Model_Resource_Installer_Attribute $installer */
		$installer = Df_Catalog_Model_Resource_Installer_Attribute::s();
		if (!$installer->getAttributeId('catalog_category', 'thumbnail')) {
			/** Magento < 1.5 */
			$entityTypeId = $installer->getEntityTypeId('catalog_category');
			$attributeSetId = $installer->getDefaultAttributeSetId($entityTypeId);
			$installer->addAttribute('catalog_category', 'df_thumbnail', array(
				'type' => 'varchar'
				,'backend' => 'catalog/category_attribute_backend_image'
				,'frontend' => ''
				,'label' => 'Thumbnail Image (Magento 1.4)'
				,'input' => 'image'
				,'class' => ''
				,'source' => ''
				,'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE
				,'visible' => true
				,'required' => false
				,'user_defined' => false
				,'default' => ''
				,'searchable' => false
				,'filterable' => false
				,'comparable' => false
				,'visible_on_front' => false
				,'unique' => false
			));
			$installer->addAttributeToGroup(
				$entityTypeId
				,$attributeSetId
				/**
				 * Не используем синтаксис
				 * $this->getDefaultAttributeGroupId($entityTypeId, $attributeSetId)
				 *
				 * потому что он при предварительно включенной русификации
				 * может приводить к созданию дополнительной вкладки ("Основное")
				 * вместо размещения свойства на главнйо вкладке ("Главное").
				 */
				,'General Information'
				,'df_thumbnail'
				,4
			);
		}
		rm_eav_reset_categories();
	}
}