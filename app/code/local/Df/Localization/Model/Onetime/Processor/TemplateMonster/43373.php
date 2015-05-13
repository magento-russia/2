<?php
class Df_Localization_Model_Onetime_Processor_TemplateMonster_43373
	extends Df_Localization_Model_Onetime_Processor {
	/**
	 * @override
	 * @return void
	 */
	protected function additionalProcessingAfterEntitiesSave() {
		/**
		 * 1)
		 * Если система содержит и русскоязычную витрину, и англоязычную,
		 * то удаляем русскоязычную витрину и переименовываем англоязычную в русскоязычную.
		 * Это позволит нам удалить и ненужную витрину, и удалить ненужный русскоязычный перевод
		 * (вместо него будем использовать перевод Российской сборки Magento).
		 *
		 * 2)
		 * Удаляем папку app/locale/ru_RU
		 *
		 * 3)
		 * Удаляем испанскую и немецкую витрины, а также папки app/locale/de_DE и app/locale/es_ES.
		 */
		/** @var array(string => Mage_Core_Model_Store) $stores */
		$stores = Mage::app()->getStores($withDefault = true, $codeKey = true);
		/** @var Mage_Core_Model_Store|null $english */
		$english = df_a($stores, 'english');
		/** @var Mage_Core_Model_Store|null $russian */
		$russian = df_a($stores, 'russian');
		/** @var Mage_Core_Model_Store|null $default */
		$default = Mage::app()->getDefaultStoreView();
		if (
				$english && $russian && $default
			&&
				($english->getId() === $default->getId())
			&&
				$russian->isCanDelete()
			&&
				!isset($stores['default'])
		) {
			Df_Core_Model_Store::deleteStatic($russian);
			$english->setCode('default');
			$english->setName('Основная витрина');
			$english->save();
			if (isset($stores['german'])) {
				Df_Core_Model_Store::deleteStatic($stores['german']);
			}
			if (isset($stores['spanish'])) {
				Df_Core_Model_Store::deleteStatic($stores['spanish']);
			}
			$this->deleteUnusedLocaleFolders();
			rm_cache_clean();
			Mage::app()->reinitStores();
		}
	}

	/** @return void */
	private function deleteUnusedLocaleFolders() {
		/** @var string $localeBasePath */
		$localeBasePath = BP . '/app/locale/';
		/** @var string[] $localesToDelete */
		$localesToDelete = array('de_DE', 'es_ES', 'ru_RU');
		foreach ($localesToDelete as $localeToDelete) {
			/** @var string $localeToDelete */
			/** @var string */
			$localeFullPath = $localeBasePath . $localeToDelete;
			if (is_dir($localeFullPath)) {
				df_path()->delete($localeFullPath);
			}
		}
	}
}


 