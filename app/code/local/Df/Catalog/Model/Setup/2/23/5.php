<?php
class Df_Catalog_Model_Setup_2_23_5 extends Df_Core_Model_Setup {
	/**
	 * @override
	 * @return void
	 */
	public function process() {$this->processCategories();}

	/**
	 * @return Df_Catalog_Model_Setup_2_23_5
	 * @throws Exception
	 */
	private function processCategories() {
		/**
		 * Установка перед выполнением следующей процедуры текущим магазином административного
		 * устраняет сбой «Table 'catalog_category_flat' doesn't exist»,
		 * который происходил, если перед обновлением была включена денормализация,
		 * и сразу после установки  перезагрузить не администативную страницу магазина,
		 * а витринную (а может быть, и без разницы, какую страницу).
		 * Возможно, этот же сбой можно устранить и временным отключением денормализации.
		 * @link http://magento-forum.ru/topic/4178/
		 */
		rm_admin_begin();
		try {
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
					$this
						->processCategory($rootCategoryId, $storeId = 0)
						->processCategory($rootCategoryId, $store->getId())
					;
				}
			}
			Df_Catalog_Model_Category::reindexFlat();
		}
		catch(Exception $e) {
			rm_admin_end();
			throw $e;
		}
		rm_admin_end();
		return $this;
	}

	/**
	 * @param int $categoryId
	 * @param int $storeId
	 * @return Df_Catalog_Model_Setup_2_23_5
	 */
	private function processCategory($categoryId, $storeId) {
		/** @var Df_Catalog_Model_Category $category */
		$category = Df_Catalog_Model_Category::ld($categoryId, $storeId);
		$category->setName($this->translateCategoryName($category->getName()));
		$category->saveRm($storeId);
		return $this;
	}

	/**
	 * @param string $name
	 * @param array(string => string) $dictionary
	 * @return string
	 */
	private function translate($name, array $dictionary) {return df_a($dictionary, $name, $name);}

	/**
	 * @param string $name
	 * @return string
	 */
	private function translateCategoryName($name) {
		return $this->translate($name, array('Default Category' => 'корневой раздел'));
	}

	/** @return Df_Catalog_Model_Setup_2_23_5 */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}