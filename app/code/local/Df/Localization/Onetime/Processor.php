<?php
class Df_Localization_Onetime_Processor extends Df_Core_Model {
	/**
	 * @override
	 * @return string
	 */
	public function getId() {return $this->cfg(self::P__ID);}

	/** @return string */
	public function getLink() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_url_admin('df_localization/theme/process', array(
				Df_Localization_Onetime_Action::RP__PROCESSOR => $this->getId()
			));
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	public function getSortWeight() {
		if (!isset($this->{__METHOD__})) {
			/** @var int $result */
			$result = 0;
			/**
			 * Русификаторы присутствующих в системе оформительских тем
			 * будут отображаться в общем списке
			 * выше русификаторов отсутствующих в системе оформительских тем.
			 */
			if ($this->isThemeInstalled()) {
				$result -= 10;
				/**
				 * Не запускавшиеся ранее русификаторы
				 * будут отображаться в общем списке
				 * выше запускавшиеся ранее русификаторов.
				 */
				if (!$this->getTimeOfLastProcessing()) {
					$result -= 9;
				}
			}
			/**
			 * Русификаторы нестандартных оформительских тем
			 * будут отображаться в общем списке
			 * выше русификаторов стандартных оформительских тем.
			 */
			if (df_contains($this->getDictionaryLocalPath(), 'Magento/')) {
				$result += 8;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Zend_Date|null */
	public function getTimeOfLastProcessing() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|null $resultAsString */
			$resultAsString = Mage::getStoreConfig($this->getConfigPath_TimeOfLastProcessing());
			$this->{__METHOD__} = df_n_set(!$resultAsString ? null : new Zend_Date($resultAsString));
		}
		return df_n_get($this->{__METHOD__});
	}

	/** @return string */
	public function getTitle() {return $this->cfg(self::$P__TITLE);}

	/** @return string */
	public function getType() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				!$this->isThemeInstalled()
				? self::$TYPE__ABSENT
				: ($this->getTimeOfLastProcessing() ? self::$TYPE__PROCESSED : self::$TYPE__APPLICABLE)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => string) */
	public function getUrl() {return $this->cfg(self::$P__URL, array());}

	/** @return bool */
	public function isApplicable() {return self::$TYPE__APPLICABLE === $this->getType();}

	/** @return bool */
	public function isProcessed() {return self::$TYPE__PROCESSED === $this->getType();}

	/** @return bool */
	public function isThemeInstalled() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * Алгоритм позаимствовал из @see Mage_Core_Model_Design_Package::designPackageExists()
			 * Не использую напрямую @see Mage_Core_Model_Design_Package::designPackageExists()
			 * в целях ускорения: чтобы использовать уже готовую переменную $packageDir
			 * для расчёта папки темы.
			 */
			/** @var string $packageDir */
			$packageDir = df_cc_path(Mage::getBaseDir('design'), 'frontend', $this->getPackage());
			$this->{__METHOD__} =
					is_dir($packageDir)
				&&
					(
							!$this->getTheme()
						||
							is_dir(df_cc_path($packageDir, $this->getTheme()))
					)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return void */
	public function process() {
		$this->applyDictionary($this->getDictionaryForTheme());
		$this->applyDictionary($this->getDictionaryCommon());
		$this->additionalProcessingBeforeEntitiesSave();
		$this->saveModifiedMagentoEntities();
		$this->additionalProcessingAfterEntitiesSave();
		$this->updateTimeOfLastProcessing();
		df_cache_clean();
		/*
		 * 2014-12-06
		 * Обязательно надо сделать,
		 * иначе русификатор будет по-прежнему отображаться в списке неприменявшихся
		 * до новой перезагрузки страницы, что может сбивать с толку администратора.
		 * Раньше тут стояло Mage::getConfig()->reinit();
		 * что не решало проблему.
		 */
		df_store()->resetConfig();
		$this->importDemoImages();
	}

	/** @return void */
	protected function additionalProcessingAfterEntitiesSave() {}

	/** @return void */
	protected function additionalProcessingBeforeEntitiesSave() {}

	/**
	 * @param Df_Localization_Onetime_Dictionary $dictionary
	 * @return void
	 */
	private function applyDictionary(Df_Localization_Onetime_Dictionary $dictionary) {
		foreach ($dictionary->getRules() as $rule) {
			/** @var Df_Localization_Onetime_Dictionary_Rule $rule */
			Df_Localization_Onetime_Processor_Rule::i($rule)->process();
		}
		foreach ($dictionary->getConfigEntries() as $configEntry) {
			/** @var Df_Localization_Onetime_Dictionary_Config_Entry $configEntry */
			Df_Localization_Onetime_Processor_Config::i($configEntry)->process();
		}
		/**
		 * 2015-08-23
		 * Прямой перевод значений в базе данных
		 * Пример:
			 <dictionary>
				<db>
					<table name='ves_megamenu/megamenu'>
						<column name='title'>
							<term>
								<from>Home</from>
								<to>Главная</to>
							</term>
							<term>
								<from>Root Catalog</from>
								<to>Каталог</to>
							</term>
						</column>
					</table>
				</db>
			</dictionary>
		 */
		foreach ($dictionary->tables() as $table) {
			/** @var Df_Localization_Onetime_Dictionary_Db_Table $table */
			foreach ($table->columns() as $column) {
				/** @var Df_Localization_Onetime_Dictionary_Db_Column $column */
				Df_Localization_Onetime_Processor_Db_Column::p($column);
			}
		}
		$this->executeFilesystemOperations($dictionary);
		/**
		 * 2015-08-24
		 * Поддержка синтаксиса
			<attributes>
				<used_in_product_listing>featured</used_in_product_listing>
			</attributes>
		 */
		/** @var string[] $attributes */
		$attributes = df_csv_parse((string)$dictionary->descendS('attributes/used_in_product_listing'));
		if ($attributes) {
			/** @var int[] $atttibuteIds */
			$atttibuteIds = df_fetch_col_int_unique(
				'eav/attribute', 'attribute_id', 'attribute_code', $attributes
			);
			if ($atttibuteIds) {
				df_conn()->update(
					df_table('catalog/eav_attribute')
					, array('used_in_product_listing' => 1)
					, array('attribute_id IN (?)' => $atttibuteIds)
				);
			}
		}
	}

	/**
	 * @param Df_Localization_Onetime_Dictionary $dictionary
	 * @return void
	 */
	private function executeFilesystemOperations(Df_Localization_Onetime_Dictionary $dictionary) {
		/** @var string[] $messages */
		$messages = array();
		foreach ($dictionary->getFilesystemOperations() as $filesystemOperation) {
			/** @var Df_Localization_Onetime_Dictionary_Filesystem_Operation $filesystemOperation */
			try {
				Df_Localization_Onetime_Processor_Filesystem::i($filesystemOperation)->process();
			}
			catch (Exception $e) {
				$messages[]= df_ets($e);
			}
		}
		if ($messages) {
			df_session()->addError(df_t()->nl2br(
				"В процессе русификации оформительской темы"
				. " некоторые операции с файловой системой завершились сбойно."
				. "\nКак правило, причиной сбоя является отсутствие у интерпрататора PHP"
				. " прав на запись в соответствующие папки."
				. "\nВам нужно предоставить эти интерпретатору PHP,"
				. " а затем запустить русификатор заново."
				. "\nСбои перечислены ниже:\n"
				. df_cc_n($messages)
			));
		}
	}

	/** @return string */
	private function getConfigPath_TimeOfLastProcessing() {
		return 'rm/design_theme_processor/time/' . $this->getId();
	}

	/** @return string|null */
	private function getDemoImagesBaseUrl() {return dfa($this->getUrl(), 'demo_images_base');}

	/** @return Df_Localization_Onetime_Dictionary */
	private function getDictionaryForTheme() {
		return Df_Localization_Onetime_Dictionary::s($this->getDictionaryLocalPath());
	}

	/** @return Df_Localization_Onetime_Dictionary */
	private function getDictionaryCommon() {
		return Df_Localization_Onetime_Dictionary::s('common.xml');
	}

	/** @return string */
	private function getDictionaryLocalPath() {return $this->cfg(self::$P__DICTIONARY);}

	/** @return string */
	private function getPackage() {return $this->cfg(self::$P__PACKAGE);}

	/** @return string */
	private function getTheme() {return $this->cfg(self::$P__THEME);}

	/** @return void */
	private function importDemoImages() {
		if ($this->getDemoImagesBaseUrl()) {
			Df_Localization_Onetime_DemoImagesImporter::i($this->getDemoImagesBaseUrl())
				->process();
		}
	}

	/** @return void */
	private function saveModifiedMagentoEntities() {
		try {
			Df_Localization_Onetime_TypeManager::s()->saveModifiedMagentoEntities();
		}
		catch (\Df\Core\Exception\Batch $e) {
			$e->log();
			df_session()->addError(strtr(
				'При обновлении базы данных в процессе русификации оформительской темы «{theme}»'
				. ' прозошли перечисленные ниже сбои.'
				. '<br/>Подробные диагностические отчёты о каждом сбое записаны в подпапку var/log'
				. ' внутри корневой папки Magento.'
				. '<br/><a href="http://magento-forum.ru/forum/354/">Сообщите о сбоях разработчику</a>,'
				. ' приложив подробные диагностические отчёты.'
				. '<br/>После устранения сбоев — перезапустите русификацию оформительской темы повторно.'
				. '{messages}'
				, array(
					'{messages}' => df_tag_list(df_t()->nl2br($e->messages()), false, 'rm-batch')
					,'{theme}' => $this->getTitle()
				)
			));
		}
	}

	/** @return void */
	private function updateTimeOfLastProcessing() {
		Mage::getConfig()->saveConfig($this->getConfigPath_TimeOfLastProcessing(), df_dts());
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__DICTIONARY, DF_V_STRING_NE)
			->_prop(self::P__ID, DF_V_STRING_NE)
			->_prop(self::$P__PACKAGE, DF_V_STRING_NE)
			->_prop(self::$P__THEME, DF_V_STRING)
			->_prop(self::$P__TITLE, DF_V_STRING_NE)
			->_prop(self::$P__URL, DF_V_ARRAY, false)
		;
	}
	/**
	 * @used-by Df_Localization_Block_Admin_Theme_Processor::_construct()
	 * @used-by Df_Localization_Onetime_Processor_Collection::itemClass()
	 * @used-by Df_Localization_Onetime_Processor_Collection::loadInternal()
	 */
	const _C = __CLASS__;
	const P__ID = 'id';

	/** @var string */
	private static $P__DICTIONARY = 'dictionary';
	/** @var string */
	private static $P__PACKAGE = 'package';
	/** @var string */
	private static $P__THEME = 'theme';
	/** @var string */
	private static $P__TITLE = 'title';
	/** @var string */
	private static $P__URL = 'url';

	/** @var string */
	private static $TYPE__ABSENT = 'absent';
	/** @var string */
	private static $TYPE__APPLICABLE = 'applicable';
	/** @var string */
	private static $TYPE__PROCESSED = 'processed';
}