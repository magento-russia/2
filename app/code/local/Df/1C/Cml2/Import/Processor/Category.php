<?php
/** @method Df_1C_Cml2_Import_Data_Entity_Category|Df_1C_Cml2_Import_Data_Entity getEntity() */
class Df_1C_Cml2_Import_Processor_Category extends Df_1C_Cml2_Import_Processor {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		/** @var Df_Catalog_Model_Category $category */
		$category = df()->registry()->categories()->findByExternalId($this->getEntity()->getExternalId());
		if (!$category) {
			/**
			 * Перед созданием и сохранением товарного раздела
			 * надо обязательно надо установить текущим магазином административный,
			 * иначе возникают неприятные проблемы.
			 *
			 * В частности, для успешного сохранения товарного раздела
			 * надо отключить на время сохранения режим денормализации.
			 * Так вот, в стандартном программном коде Magento автоматически отключает
			 * режим денормализации при создании товарного раздела из административного магазина
			 * (в конструкторе товарного раздела).
			 *
			 * А если сохранять раздел, чей конструктор вызван при включенном режиме денормализации —
			 * то произойдёт сбой:
			 *
			 * SQLSTATE[23000]: Integrity constraint violation:
			 * 1452 Cannot add or update a child row:
			 * a foreign key constraint fails
			 * (`catalog_category_flat_store_1`, * CONSTRAINT `FK_CAT_CTGR_FLAT_STORE_1_ENTT_ID_CAT_CTGR_ENTT_ENTT_ID`
			 * FOREIGN KEY (`entity_id`) REFERENCES `catalog_category_entity` (`en)
			 */
			$category = Df_Catalog_Model_Category::createAndSave(array(
				Df_Catalog_Model_Category::P__PATH => $this->getParent()->getPath()
				,Df_Catalog_Model_Category::P__NAME => $this->getEntity()->getName()
				,Df_Catalog_Model_Category::P__IS_ACTIVE => true
				,Df_Catalog_Model_Category::P__IS_ANCHOR => true
				,Df_Catalog_Model_Category::P__DISPLAY_MODE => Mage_Catalog_Model_Category::DM_MIXED
				,Df_1C_Const::ENTITY_EXTERNAL_ID => $this->getEntity()->getExternalId()
				,'attribute_set_id' =>
					Df_Catalog_Model_Resource_Installer_Attribute::s()->getCategoryAttributeSetId()
				,'include_in_menu' => 1
			), $this->storeId());
			df()->registry()->categories()->addEntity($category);
			df_1c_log('Создан товарный раздел «%s».', $category->getName());
		}
		foreach ($this->getEntity()->getChildren() as $child) {
			/** @var Df_1C_Cml2_Import_Data_Entity_Category $child */
			self::i($category, $child)->process();
		}
	}

	/** @return Df_Catalog_Model_Category */
	private function getParent() {return $this->cfg(self::$P__PARENT);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__PARENT, Df_Catalog_Model_Category::class);
	}
	/** @var string */
	private static $P__PARENT = 'parent';
	/**
	 * @used-by Df_1C_Cml2_Action_Catalog_Import::importCategories()
	 * @static
	 * @param Df_Catalog_Model_Category $parent
	 * @param Df_1C_Cml2_Import_Data_Entity_Category $category
	 * @return Df_1C_Cml2_Import_Processor_Category
	 */
	public static function i(
		Df_Catalog_Model_Category $parent, Df_1C_Cml2_Import_Data_Entity_Category $category
	) {
		return new self(array(self::$P__PARENT => $parent, self::$P__ENTITY => $category));
	}
}