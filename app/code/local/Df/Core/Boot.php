<?php
class Df_Core_Boot {
	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @return void
	 */
	public function _default() {self::run();}

	/**
	 * 2015-08-03
	 * При установке РСМ одновременно с CE
	 * controller_front_init_before — это первое событие,
	 * которое становится доступно подписчикам,
	 * а метод @see Df_Speed_Observer::controller_front_init_before()
	 * уже использует @uses df_cfg(),
	 * поэтому нам надо инициализирвать РСМ.
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @return void
	 */
	public function controller_front_init_before() {self::run();}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function resource_get_tablename(Varien_Event_Observer $observer) {
		if (!self::done() && self::needInitNow($observer['table_name'])) {
			self::run();
		}
	}

	/**
	 * @used-by resource_get_tablename()
	 * @used-by Df_Core_Model_StoreM::getConfig()
	 * @used-by Df_Core_Model_StoreM::resetConfig()
	 * @used-by Df_Core_Model_StoreM::setConfig()
	 * @return bool
	 */
	public static function done() {return self::$_done1 && self::$_done2;}

	/**
	 * 2015-03-06
	 * Вероятно, часть этих вызовов уже избыточны,
	 * потому что инициализация за последние годы развития Российской сборки Magento
	 * стала более «умной»: @see resource_get_tablename()
	 * @used-by resource_get_tablename()
	 * @used-by Df_Catalog_Model_Category::_construct()
	 * @used-by Df_Catalog_Model_Product::_construct()
	 * @used-by Df_Catalog_Model_Resource_Installer_Attribute::startSetup()
	 * @used-by Df_Compiler_Model_Process::getCompileClassList()
	 * @used-by Df_Compiler_Model_Process::_getClassesSourceCode()
	 * @used-by Df_Core_Helper_DataM::useDbCompatibleMode()
	 * @used-by Df_Core_Model_Resource_Setup::startSetup()
	 * @used-by Df_Core_Model_Translate::_getTranslatedString()
	 * @used-by Df_Directory_Observer::core_collection_abstract_load_before()
	 * @used-by Df_Eav_Model_Config::_load()
	 * @used-by Df_Eav_Model_Config::_save()
	 * @used-by Df_Logging_Model_Processor::_construct()
	 * @return void
	 */
	public static function run() {
		if (!self::$_done1) {
			self::init1();
			Df_Core_Lib::load('1C');
			Df_Core_Lib::load('Directory');
			Df_Core_Lib::load('Tax');
			Df_Core_Lib::load('Xml');
			self::$_done1 = true;
		}
		/**
		 * 2015-03-25
		 * 3 часа потратил на этот элегантный способ
		 * определения факта инициализации текущего магазина системы.
		 *
		 * @uses Mage_Core_Model_App::getStores() возвращает пустой массив,
		 * если текущий магазин системы ещё не был инициализирован.
		 *
		 * Условия Mage::isInstalled() && !Mage::app()->getUpdateMode()
		 * нам нужны потому, что нельзя работать с текущим магазином,
		 * если Magento CE ещё не установлена, либо если происходит обновление модулей.
		 *
		 * В этих ситуациях @see Mage::getStoreConfig()
		 * приводит к установке в качестве текущего магазина заглушки:
		 * @see Mage_Core_Model_App::getStore():
			if (!Mage::isInstalled() || $this->getUpdateMode()) {
				return $this->_getDefaultStore();
			}
		 * @see Mage_Core_Model_App::_getDefaultStore():
			$this->_store = Mage::getModel('core/store')
			   ->setId(self::DISTRO_STORE_ID)
			   ->setCode(self::DISTRO_STORE_CODE);
		 *
		 * В дальнейшем это приводит к фатальным сбоям в коде, подобном следующиему:
		 * @see Mage_Adminhtml_Catalog_ProductController::_initProductSave():
		 * $product->setWebsiteIds(array(Mage::app()->getStore(true)->getWebsite()->getId()));
		 * В этом коде вызов Mage::app()->getStore(true)->getWebsite()->getId() даст сбой,
		 * потому что установочный магазин-заглушка не привязан ни к какому сайту
		 * (поле website_id не инициализировано).
		 */
		if (!self::$_done2
			&& Mage::app()->getStores()
			&& Mage::isInstalled()
			&& !Mage::app()->getUpdateMode()
		) {
			self::init2();
			self::$_done2 = true;
		}
	}

	/**
	 * Этот метод содержит код инициализации, который должен выполняться как можно раньше:
	 * вне зависимости, был ли уже инициализирован текущий магазин системы.
	 * Соответственно, в этом методе мы не можем работать с объектами-магазинами.
	 * В том числе, в частости, не можем прочитывать настройки текущего магазина.
	 * @used-by run()
	 * @return void
	 */
	private static function init1() {
		// 2015-03-04
		// Наверное, это не совсем правильно для промышленных магазинов.
		// Надо, видимо, сделать административную опцию.
		Mage::setIsDeveloperMode(true);
		/**
		 * В PHP 5.6 этот вызов считается устаревшим:
		 * «Use of mbstring.internal_encoding is deprecated».
		 */
		@ini_set('mbstring.internal_encoding', 'UTF-8');
		/**
		 * Magento CE, включая самую свежую на настоящее время версию 1.9.0.1,
		 * официально не совместима с PHP 5.4 и 5.5.
		 * Однако добиться этой совместимости просто:
		 * достаточно отключить предупреждения PHP уровня E_DEPRECATED.
		 * Такое предупреждение, в частности, возникает при вызове метода
		 * @see Mage_Core_Helper_Abstract::removeTags():
		 * «preg_replace(): The /e modifier is deprecated, use preg_replace_callback instead.»
		 */
		/**
		 * Обратите внимание, что константа @see E_DEPRECATED появилась только в PHP 5.3
		 * http://php.net/manual/errorfunc.constants.php
		 */
		if (defined('E_DEPRECATED')) {
			/**
			 * Обратите внимание, что ошибочно писать здесь
			 * error_reporting(error_reporting() ^ E_DEPRECATED);
			 * потому что ^ — это побитовое XOR,
			 * и если предыдущее значение error_reporting не содержало E_DEPRECATED,
			 * то код error_reporting(error_reporting() ^ E_DEPRECATED);
			 * добавит в error_reporting E_DEPRECATED.
			 */
			error_reporting(error_reporting() &~ E_DEPRECATED);
		}
		/**
		 * На серверах Debian (в том числе официально рекомендуемых)
		 * устаревшие файлы сессий никогда не удаляются
		 * и накапливаются в виде мусора в папке var/session:
		 * http://php.net/manual/session.configuration.php#115842
		 * https://www.phpro.be/news/magento-garbage-collection
		 *
		 * Так происходит потому, что официальный дистрибутив PHP для Debian
		 * содержит для параметра session.gc_probability нестандартное значение 0
		 * вместо стандартного значения 1.
		 *
		 * Вероятность запуска очистки сессий равна session.gc_probability / session.gc_divisor.
		 * При session.gc_probability = 0 очистка сессий никогда не зппустится.
		 * Debian так делает намеренно, потому что хранит сессии в папке с очень строгим доступом,
		 * где интерпретатор PHP всё равно не имеет прав удалять файлы,
		 * и Debian удаляет их самостоятельно посредством нестандартного скрипта,
		 * запускаемого планировщиком задач.
		 * Однако этот нестандартный скрипт Debian
		 * работает только со стандартной папкой хранения сессий
		 * и ничего не знает про папку Magento var/session.
		 * По этой причине файлы сессий Magento на Debian никогда не удаляются.
		 *
		 * Чтобы устранить эту проблему, возвращаем для параметра session.gc_probability
		 * значение 1.
		 *
		 * Обратите внимание,
		 * что интерпретатор PHP, похоже, выполняет очистку сессий при вызове @see session_start().
		 * Поэтому важно, что в данном случае наш код гарантированно исполняется
		 * заведомо раньше вызова @see session_start()
		 * в методе @see Mage_Core_Model_Session_Abstract_Varien::start(),
		 * я проверял.
		 */
		ini_set('session.gc_probability', 1);
		ini_set('session.gc_divisor', 100);
		Df_Core_Lib::load('Core');
		/** @uses \Df\Qa\Message\Failure\Error::check() */
		register_shutdown_function(array('\Df\Qa\Message\Failure\Error', 'check'));
		if (!ini_get('date.timezone')) {
			/**
			 * Временно устанавливаем в качестве часового пояса московский.
			 * Часовой пояс надо установить,
			 * потому что иначе некоторые стандатные для PHP функции работы со временем
			 * могут приводить к сбою типа E_WARNING.
			 *
			 * Реальный часовой пояс мы затем установим в методе @see init2()
			 * Сразу установить реальный мы не можем,
			 * потому что это требует предварительной инициализации текущего магазина системы,
			 * а при выполнении @see init1() текущий магазин системы может быть ещё не инициализирован.
			 */
			date_default_timezone_set('Europe/Moscow');
		}
	}

	/**
	 * Этот метод содержит код инициализации,
	 * который должен выполняться после инициализации текущего магазина системы.
	 * @used-by run()
	 * @return void
	 */
	private static function init2() {
		/** @var string|null $defaultTimezone */
		$defaultTimezone = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);
		// По необъяснимой причине
		// после предыдущего вызова $defaultTimezone может быть пустым значением
		if ($defaultTimezone) {
			date_default_timezone_set($defaultTimezone);
		}
		if (Df_Speed_Model_Settings_General::s()->enablePhpScriptsLoadChecking()) {
			Df_Core_Autoload::register();
		}
	}

	/** @return bool */
	private static function isCompilationFromCommandLine() {
		static $r; return !is_null($r) ? $r : $r = @class_exists('Mage_Shell_Compiler', false);
	}

	/**
	 * Мы бы рады инициализировать нашу библиотеку при загрузке таблицы «core_resource»,
	 * однако в тот момент система оповещений о событиях ещё не работает,
	 * и мы сюда всё равно не попадём.
	 *
	 * Обратите внимание, что проблема инициализации Российской сборки Magento
	 * при работе стороронних установочных скриптов
	 * удовлетворительно решается методом @see Df_Core_Helper_DataM::useDbCompatibleMode()
	 *
	 * @param string $tableName
	 * @return bool
	 */
	private static function needInitNow($tableName) {
		return
				'core_website' === $tableName
			||
				'index_process' === $tableName && self::isCompilationFromCommandLine()
		;
	}

	/** @var bool */
	private static $_done1 = false;
	/** @var bool */
	private static $_done2 = false;
}