<?php
class Df_Catalog_Setup_2_23_5 extends Df_Core_Setup {
	/**
	 * @override
	 * @see Df_Core_Setup::_process()
	 * @used-by Df_Core_Setup::process()
	 * @return void
	 */
	protected function _process() {
		foreach (Df_Core_Model_Store::c() as $store) {
			/** @var Df_Core_Model_Store $store */
			/** @var int $rootCategoryId */
			$rootCategoryId = $store->getRootCategoryId();
			/**
			 * Обратите внимание, что у адмсинистративного магазина (с идентификатором «0»)
			 * как бы отсутствует корневой товарный раздел
			 * ($store->getRootCategoryId() вернёт пустое значение).
			 * По этой причине и писать выше Df_Core_Model_Store::c(true)
			 * вместо Df_Core_Model_Store::c() особого смысла нет.
			 * Вместо этого мы обрабатываем административный раздел вручную.
			 */
			if ($rootCategoryId) {
				$this->processCategory($rootCategoryId, $storeId = 0);
				$this->processCategory($rootCategoryId, $store->getId());
			}
		}
		Df_Catalog_Model_Category::reindexFlat();
	}

	/**
	 * @param int $categoryId
	 * @param int $storeId
	 * @return void
	 */
	private function processCategory($categoryId, $storeId) {
		/** @var Df_Catalog_Model_Category $category */
		$category = Df_Catalog_Model_Category::ld($categoryId, $storeId);
		$category->setName($this->translateCategoryName($category->getName()));
		$category->saveRm($storeId);
	}

	/**
	 * @param string $name
	 * @param array(string => string) $dictionary
	 * @return string
	 */
	private function translate($name, array $dictionary) {return dfa($dictionary, $name, $name);}

	/**
	 * @param string $name
	 * @return string
	 */
	private function translateCategoryName($name) {
		return $this->translate($name, array('Default Category' => 'корневой раздел'));
	}

	/**
	 * @used-by Df_Catalog_Observer::df__magento_ce_has_just_been_installed()
	 * @return void
	 */
	public static function p() {self::pc(__CLASS__);}
}