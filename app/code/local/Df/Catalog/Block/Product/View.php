<?php
class Df_Catalog_Block_Product_View extends Mage_Catalog_Block_Product_View {
	/**
	 * Цель перекрытия —
	 * предоставить администратору возможность скрывать витринной товарной карточки
	 * ссылку «рассказать другу».
	 * @override
	 * @return bool
	 */
	public function canEmailToFriend() {
		$result = parent::canEmailToFriend();
		if (
				df_module_enabled(Df_Core_Module::TWEAKS)
			&&
				df_handle(Df_Core_Model_Layout_Handle::CATALOG_PRODUCT_VIEW)
			&&
				df_cfg()->tweaks()->catalog()->product()->view()->needHideEmailToFriend()
		) {
			$result = false;
		}
		return $result;
	}

	/**
	 * @override
	 * @see Mage_Core_Block_Template::getCacheKeyInfo()
	 * @used-by Df_Core_Block_Abstract::getCacheKey()
	 * Обратите внимание, что блок @see Mage_Catalog_Block_Product_View
	 * выводится несколько раз на одной и той же странице
	 * с разным именем и разными шаблонами для отображения разных данных.
	 * Родительский метод @uses Mage_Core_Block_Template::getCacheKeyInfo()
	 * включает в ключ кэширование файловый путь к шаблону,
	 * поэтому путаница кэша тем самым исключена.
	 * @return string[]
	 */
	public function getCacheKeyInfo() {
		return array_merge(parent::getCacheKeyInfo(), array($this->getProduct()->getId()));
	}

	/**
	 * Цель перекрытия —
	 * предоставить администратору возможность скрывать витринной товарной карточки
	 * краткое описание товара.
	 * @override
	 * @return Mage_Catalog_Model_Product
	 */
	public function getProduct() {
		$result = parent::getProduct();
		if (!$this->_dfProductPrepared) {
			if (
					df_module_enabled(Df_Core_Module::TWEAKS)
				&&
					df_handle(Df_Core_Model_Layout_Handle::CATALOG_PRODUCT_VIEW)
				&&
					df_cfg()->tweaks()->catalog()->product()->view()->needHideShortDescription()
			) {
				$result->unsetData('short_description');
			}
			$this->_dfProductPrepared = true;
		}
		return $result;
	}
	/** @var bool */
	private $_dfProductPrepared = false;

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->addData(array(
			/**
			 * Чтобы блок кэшировался стандартным, заложенным в @see Mage_Core_Block_Abstract способом,
			 * продолжительность хранения кэша надо указывать обязательно,
			 * потому что значением продолжительности по умолчанию является «null»,
			 * что в контексте @see Mage_Core_Block_Abstract
			 * (и в полную противоположность Zend Framework
			 * и всем остальным частям Magento, где используется кэширование)
			 * означает, что блок не удет кэшироваться вовсе!
			 * @used-by Mage_Core_Block_Abstract::_loadCache()
			 */
			'cache_lifetime' => Df_Core_Block_Template::CACHE_LIFETIME_STANDARD
			/**
			 * При такой инициализации тегов
			 * (без перекрытия метода @see Mage_Core_Block_Abstract::getCacheTags())
			 * тег @see Mage_Core_Block_Abstract::CACHE_GROUP будет добавлен автоматически.
			 * @used-by Mage_Core_Block_Abstract::getCacheTags()
			 */
			,'cache_tags' => array(Mage_Catalog_Model_Product::CACHE_TAG)
		));
	}
}