<?php
class Df_Core_Bootstrap extends Df_Core_Bootstrap_Abstract {
	/** @var bool */
	public static $initialized = false;
	/** @return void */
	public function init() {
		if (!self::$initialized) {
			Mage::setIsDeveloperMode(true);
			/**
			 * В PHP 5.6 этот вызов считается устаревшим:
			 * «Use of mbstring.internal_encoding is deprecated»
			 * @link http://php.net/manual/mbstring.configuration.php#ini.mbstring.internal-encoding
			 *
			 * Вместо «mbstring.internal_encoding» рекомендуется использовать «default_charset».
			 * Впрочем, начиная с PHP 5.6 значение «default_charset»
			 * по-умолчанию и так равно «UTF-8»:
			 * @link http://php.net/manual/en/ini.core.php#ini.default-charset
			 */
			if (version_compare(phpversion(), '5.6', '>=')) {
				ini_set('default_charset', 'UTF-8');
				/**
				 * Обратите внимание, что Zend Framework использует аналогичные устаревшие вызовы
				 * @see iconv_set_encoding() с параметром «internal_encoding»:
				 * @link http://magento.stackexchange.com/questions/34015/magento-1-9-php-5-6-use-of-iconv-internal-encoding-is-deprecated
				 * Однако чуть ниже в коде мы отключаем предупреждения уровня E_DEPRECATED.
				 */
			}
			else {
				ini_set('mbstring.internal_encoding', 'UTF-8');
			}
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
			 * @link http://php.net/manual/en/errorfunc.constants.php
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
			 * @link http://php.net/manual/en/session.configuration.php#115842
			 * @link https://www.phpro.be/news/magento-garbage-collection
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
			/**
			 * Обратите внимание, что двойной инициализации не происходит,
			 * потому что Mage::helper() ведёт реестр создаваемых объектов
			 * и создаёт единственный экземпляр конкретного класса.
			 */
			Df_Core_LibLoader::s();
			self::$initialized = true;
		}
		/** @var bool $timeZoneInitialized */
		static $timeZoneInitialized = false;
		if (!$timeZoneInitialized) {
			/**
			 * Нельзя вызывать Mage::getStoreConfig в режиме установки-обновления,
			 * потому что иначе система установит в качестве текущего магазина заглушку:
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
			/** @var bool $useTimezoneStub */
			$useTimezoneStub = !Mage::isInstalled() || Mage::app()->getUpdateMode();
			/** @var string $timezoneStub */
			$timezoneStub = 'Europe/Moscow';
			try {
				/**
				 * Здесь может случиться исключительная ситуация,
				 * если мы попали в этот метод по событию resource_get_tablename,
				 * а магазин ещё не инициализирован.
				 * Просто игнорируем её.
				 */
				/** @var string|null $defaultTimezone */
				$defaultTimezone =
					$useTimezoneStub
					? $timezoneStub
					: Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE)
				;
				/**
				 * По необъяснимой причине
				 * после предыдущего вызова $defaultTimezone может быть пустым значением
				 */
				if ($defaultTimezone) {
					date_default_timezone_set($defaultTimezone);
					if (!$useTimezoneStub) {
						$timeZoneInitialized = true;
					}
				}
			}
			catch(Exception $e) {}
		}
		/** @var bool $shutdownInitialized */
		static $shutdownInitialized = false;
		if (!$shutdownInitialized) {
			register_shutdown_function(array(Df_Qa_Model_Shutdown::_CLASS, 'processStatic'));
			$shutdownInitialized = true;
		}
	}
	/** @return Df_Core_Bootstrap */
	public static function s() {
		/**
		 * Используем именно @see Mage::getSingleton(),
		 * чтобы вызов @see Df_Core_Bootstrap::s()
		 * возвращал тот же объект,
		 * который используется для обработки событий системным диспетчером:
				<observers>
					<df_core__default>
						<class>Df_Core_Bootstrap</class>
						<method>init</method>
					</df_core__default>
				</observers>
		 		(...)
				<observers>
					<df_core__resource_get_tablename>
						<class>Df_Core_Bootstrap</class>
						<method>resource_get_tablename</method>
					</df_core__resource_get_tablename>
				</observers>
		 */
		return Mage::getSingleton(__CLASS__);
	}
}