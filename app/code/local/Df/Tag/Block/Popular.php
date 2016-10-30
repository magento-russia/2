<?php
class Df_Tag_Block_Popular extends Mage_Tag_Block_Popular {
	/**
	 * @override
	 * @return bool|int|null
	 */
	public function getCacheLifetime() {return Df_Core_Block_Template::CACHE_LIFETIME_STANDARD;}

	/**
	 * @overide
	 * @return string|null
	 */
	public function getTemplate() {
		/** @var string $result */
		$result = parent::getTemplate();
		/** @var Df_Tweaks_Model_Settings_Tags_Popular $settings */
		$settings = df_cfgr()->tweaks()->tags()->popular();
		if (
			df_module_enabled(Df_Core_Module::TWEAKS)
			&&
				(
						$settings->removeFromAll()
					||
						(
								$settings->removeFromFrontpage()
							&&
								df_handle(Df_Core_Model_Layout_Handle::CMS_INDEX_INDEX)
						)
					||
						(
								$settings->removeFromCatalogProductList()
							&&
								df_handle(Df_Core_Model_Layout_Handle::CATALOG_CATEGORY_VIEW)
						)
					||
						(
								$settings->removeFromCatalogProductView()
							&&
								df_handle(Df_Core_Model_Layout_Handle::CATALOG_PRODUCT_VIEW)
						)
				)
		) {
			/**
			 * Обратите внимание,
			 * что в демо-данных для главной страницы блок tag/popular
			 * создаётся синтаксисом {{block type="tag/popular" template="tag/popular.phtml"}}
			 * уже после события controller_action_layout_generate_blocks_after.
			 *
			 * Поэтому приходится скрывать блок перекрытием метода getTemplate
			 *
			 */
			$result = null;
		}
		return $result;
	}
}