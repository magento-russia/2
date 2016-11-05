<?php
class Df_Core_Model_Translate extends Mage_Core_Model_Translate {
	/**
	 * @override
	 * @param string $file
	 * @param string $type
	 * @param string|null $localeCode [optional]
	 * @return string|bool
	 */
	public function getTemplateFile($file, $type, $localeCode = null) {
		if (
				is_null($localeCode)
			||
				// В ядре Magento стоит обратное условие: видимо, там дефект
				(0 === preg_match('/[^a-zA-Z_]/', $localeCode))
		) {
			$localeCode = $this->getLocale();
			/**
			 * Почему-то в магазине mirigrushek.kz
			 * метод @uses getLocale() возвращает не строку, а объект @see Zend_Locale
			 */
			if (!is_string($localeCode)) {
				if ($localeCode instanceof Zend_Locale) {
					/** @var Zend_Locale|string $localeCode */
					$localeCode = $localeCode->toString();
				}
				else {
					df_error();
				}
			}
		}
		// НАЧАЛО ЗАПЛАТКИ
		if (
				('ru_RU' === $localeCode)
			&&
				(
						Df_Localization_Settings::s()->email()->isEnabled()
					||
						df_contains($file, 'df' . DS)
				)
		) {
			$localeCode = self::LOCALE__RU_DF;
		}
		// КОНЕЦ ЗАПЛАТКИ
		/** @var string $filePath */
		$filePath = 
			$this->getTemplateFilePathForLocale(
				$localeCode
				,$fileType = $type
				,$fileName = $file
			)
		;
		if (!file_exists($filePath)) {
			// If no template specified for this locale, use store default
			$filePath =
				$this->getTemplateFilePathForLocale(
					$localeCode = Mage::app()->getLocale()->getDefaultLocale()
					,$fileType = $type
					,$fileName = $file
				)				
			;
		}
		if (!file_exists($filePath)) {
			// If no template specified as store default locale, use en_US
			$filePath =
				$this->getTemplateFilePathForLocale(
					$localeCode = Mage_Core_Model_Locale::DEFAULT_LOCALE
					,$fileType = $type
					,$fileName = $file
				)					
			;
		}
		/** @var Varien_Io_File $ioAdapter */
		$ioAdapter = new Varien_Io_File();
		$ioAdapter->open(array('path' => $this->getBaseDirLocale()));
		/** @var string $result */
		$result = (string)$ioAdapter->read($filePath);
		return $result;
	}

	/**
	 * @override
	 * @param string $area
	 * @param bool $forceReload [optional]
	 * @return Df_Core_Model_Translate
	 */
	public function init($area, $forceReload = false) {
		if (!$this->needDisableTranslation()) {
			parent::init($area, $forceReload);
		}
		else {
			$this->_translateInline = false;
			$this->_data = [];
		}
		return $this;
	}

	/**
	 * 2015-03-10    
	 * Работает быстрее, чем @see Mage_Core_Model_Transtale::translate()
	 * Не поддерживает дополнительные параметры переводимой строки, 
	 * а также @see Df_Localization_Realtime_Translator.
	 *  
	 * @see Df_Localization_Realtime_Translator мы не применяем из-за кэширования:
	 * его результат зависит от контекста (в частности, текущего блока),
	 * и мы бы не смогли здесь кэшировать результат.
	 *  
	 * Если нужна поддержка @see Df_Localization_Realtime_Translator
	 * и параметров переводимой строки , то используйте @see translateFast()
	 * @used-by df_translate_simple()
	 * @param string $text
	 * @param string $moduleName
	 * @return string
	 */
	public function translateSimple($text, $moduleName) {
		/** @var string $code */
		$code = $moduleName . self::SCOPE_SEPARATOR . $text;
		if (!isset($this->{__METHOD__}[$code])) {
			$this->{__METHOD__}[$code] = $this->_getTranslatedStringLowLevel($text, $code);
		}
		return $this->{__METHOD__}[$code];
	}

	/**    
	 * 2015-03-15              
	 * Работает быстрее, чем @see Mage_Core_Model_Transtale::translate() 
	 * Отличается от @see translateSimple() поддержкой параметров переводимой строки 
	 * и @see
	 * @used-by df_translate()
	 * @param string $text
	 * @param string $moduleName
	 * @param mixed[] $variables [optional]
	 * @return string
	 */
	public function translateFast($text, $moduleName, array $variables = array()) {
		/** @var string $code */
		$code = $moduleName . self::SCOPE_SEPARATOR . $text;
		/** @var string $result */
		$result = $this->_getTranslatedString($text, $code);
		return !$variables ? $result : vsprintf($result, $variables);
	}

	/**
	 * В качестве $scope метод может получать:
	 * 1) имя модуля (для перевода модулей)
	 * 2) значение false (для перевода оформительских тем):
	 * @used-by Mage_Core_Model_Translate::_loadThemeTranslation()
	 * 3) целочисленный идентификатор магазина (для перевода из БД):
	 * @used-by Mage_Core_Model_Translate::_loadDbTranslation()
	 * @override
	 * @param array(string => string) $data
	 * @param string|bool|int $scope
	 * @param bool $forceReload [optional]
	 * @return Mage_Core_Model_Translate
	 */
	protected function _addData($data, $scope, $forceReload = false) {
		/** @var bool $allowInterference */
		static $allowInterference;
		if (is_null($allowInterference)) {
			/** @var string $allowInterferenceAsString */
			$allowInterferenceAsString = df_loc()->allowInterference();
			if (is_null($allowInterferenceAsString)) {
				/**
				 * Как ни странно, в магазине shop.d-m-t.ru
				 * метод @uses Df_Localization_Settings_Area::allowInterference() возвращает NULL.
				 * http://magento-forum.ru/topic/3703/
				 */
				$allowInterferenceAsString = Df_Admin_Config_Source_YesNoDev::DEVELOPER_MODE;
			}
			$allowInterference =
				!Mage::getIsDeveloperMode()
				? (Df_Admin_Config_Source_YesNoDev::NO !== $allowInterferenceAsString)
				: (Df_Admin_Config_Source_YesNoDev::YES === $allowInterferenceAsString)
			;
		}
		foreach ($data as $key => $value) {
			/** @var string $key */
			/** @var string $value */
			if ($key === $value) {
				continue;
			}
			$key = $this->_prepareDataString($key);
			$value = $this->_prepareDataString($value);
			/** @var string|bool|int|null $currentScope */
			$currentScope = is_null($this->_dataScope) ? null : dfa($this->_dataScope, $key);
			if ($scope && $currentScope && !$forceReload) {
				// В словаре уже имеется перевод для фразы $key.
				// Этот перевод принадлежит модулю $currentScope.
				/** @var string $keyWithCurrentSkope */
				$keyWithCurrentSkope = $currentScope . self::SCOPE_SEPARATOR . $key;
				if (!isset($this->_data[$keyWithCurrentSkope])) {
					// Перевод фразы $key хоть и принадлежит модулю $currentScope,
					// но (почему-то) является глобальным.
					if (isset($this->_data[$key])) {
						// Делаем перевод фразы $key из глобального локальным
						// (указываем его принадлежность модулю $currentScope посредством приставки)
						$this->_data[$keyWithCurrentSkope] = $this->_data[$key];
						// При необходимости (в соответствии с выбранными администратором настройками)
						// запрещаем модулям использовать переводы других модулей.
						// НАЧАЛО ЗАПЛАТКИ
						if (!$allowInterference) {
							unset($this->_data[$key]);
						}
						// КОНЕЦ ЗАПЛАТКИ
					}
				}
				/** @var string $keyWithNewSkope */
				$keyWithNewSkope = $scope . self::SCOPE_SEPARATOR . $key;
				// Добавляем новый, локальный для модуля $scope, перевод фразы $key
				$this->_data[$keyWithNewSkope] = $value;
			}
			else {
				// Сюда мы попадаем в трёх ситуациях:
				// 1) когда явно указан флаг $forceReload
				// 2) когда фразу $key ещё никто не переводил
				// 3) когда при вызове данного метода не указан владелец перевода ($scope)
				$this->_data[$key] = $value;
				$this->_dataScope[$key] = $scope;
			}
		}
		return $this;
	}

	/**
	 * Цель перекрытия —
	 * сделать возможным перевод строк (сторонних модулей), содержащих спецсимволы
	 * (например, переносы строк: «\r», «\n»).
	 * Решить эту задачу можно было двумя способами:
	 * 1) при переводе строки заменять спецсимволы в ней на пробелы
	 * 2) сделать возможным указывать спецсимволы в языковом файле
	 * Первый способ проще, однако он требует обработки в реальном времени
	 * каждой строки при её переводе, и я посчитал это слишком ресурсозатратным.
	 * Второй способ посложнее: он требует указывать спецсимволы в словаре (языковом файле),
	 * однако при этом нам не требуется обрабатывать спецсимволы в реальном времени,
	 * ведь словарь кэшируется.
	 * По этой причине задача решена вторым способом.
	 * @override
	 * @param string $file
	 * @return array(string => string)
	 */
	protected function _getFileData($file) {
		/** @var array(string => string) $dictionary */
		$dictionary = parent::_getFileData($file);
		/**
		 * Массив $dictionary может оказаться пустым,
		 * и тогда прямое применение @see array_combine() вместо @uses array_combine()
		 * приведёт к сбою: «array_combine: Both parameters should have at least 1 element
		 * in Df/Core/Model/Translate.php on line 226»
		 * http://magento-forum.ru/topic/4815/
		 *
		 * 2015-07-07
		 * Обратите внимание, что спецсимволы {\n}, {\r}, {\t} надо замещать только в значении
		 * и не надо заменять в ключе, потому что они не должны встречаться в ключе.
		 * Из ключа мы переносы строк и символы табуляции удаляем в методе @see _getTranslatedString()
		 */
		return array_combine(
			array_keys($dictionary), $this->processSpecialCharacters(array_values($dictionary))
		);
	}

	/**
	 * @override
	 * @see Mage_Core_Model_Translate::_getTranslatedString()
	 * @param string $text
	 * @param string $code
	 * @return string
	 */
	protected function _getTranslatedString($text, $code) {
		/**
		 * 2015-08-25
		 * Крайне неряшливый модуль Ves_Blog
		 * оформительской темы Ves Super Store (ThemeForest 8002349)
		 * ломает инициализацию системы, и в данной точке программы
		 * словарь может быть ещё не инициализирован.
		 */
		if (!$this->_data) {
			$this->init('frontend');
		}
		/**
		 * 2015-07-07
		 * Позволяет нам переводить стандартным способом (посредством файлов CSV) строки,
		 * содержащие переносы строк и символы табуляции.
		 * Например, оформительская тема Infortis Ultimo содержит надпись:
			<comment><![CDATA[<strong>IMPORTANT:</strong><br/>Width of the header columns is specified in <strong style="color:red;">grid units</strong>.<br/>If you want to display three columns (<strong>Left Column</strong>, <strong>Central Column</strong>, <strong>Right Column</strong>) in a single row,<br/>sum of these columns has to be <strong>equal 12 grid units</strong>, for example:<br/>4 + 4 + 4, or 4 + 0 + 8, or 3 + 0 + 9, or 0 + 12 + 0.
			<br/><br/>]]></comment>
		 * Обратите внимание на окончиние этой надписи: перед заверщающими символами <br/><br/>
		 * расположен перенос строки и несколько символов табуляции.
		 * Стандартным для Magento CE способом подобную строку перевести нельзя (ну, или я не понял, как).
		 * Моя заплатка позволяет переводить подобные строки.
		 *
		 * Обратите внимание, что нельзя вместо str_replace() использовать @see strtr c 3-мя параметрами:
		 * strtr($text, "\r\n\t", '')
		 * Такой вызов действительно обрабатывает строку побайтово,
		 * заменяя каждый из символов \r, \n, \t по-отдельности,
		 * однако он разумно работает только если длина последнего аргумента равна количеству замещаемых символов:
		 * «If given three arguments,
		 * this function returns a copy of str where all occurrences of each (single-byte) character in from
		 * have been translated to the corresponding character in to,
		 * i.e., every occurrence of $from[$n] has been replaced with $to[$n],
		 * where $n is a valid offset in both arguments.
		 * If from and to have different lengths,
		 * the extra characters in the longer of the two are ignored.
		 * The length of str will be the same as the return value's.»
		 * http://php.net/strtr
		 *
		 * То есть, если бы мы заменяли не на пустой символ, а, например, на пробел,
		 * то можно было бы короче написать с @see strtr():
		 * strtr($text, "\r\n\t", '   ')
		 * При замене на пустой символ непонятно, как сделать последний аргумент равным по длине второму.
		 *
		 * Новый алгоритм взял отсюда:
		 * http://stackoverflow.com/a/20717751
		 */
		/** @var string[] $symbolsToRemove */
		static $symbolsToRemove = array("\r", "\n", "\t");
		/**
		 * 2015-08-08
		 * Сохраняет оригинальное значение со всеми спецсимволами,
		 * чтобы вернуть его в том случае, когда перевод не найден или не нужен.
		 * Конкретная ситуация: я в файле system.xml своего модуля
		 * пишу видимый администратору комментарий к настроечной опции на русском языке,
		 * используя в том числе переносы строк.
		 * Magento CE пытается перевести этот комментарий:
		 * @used-by Mage_Adminhtml_Block_System_Config_Form::_prepareFieldComment():
		 * $comment = Mage::helper($helper)->__($commentInfo);
		 * https://github.com/OpenMage/magento-mirror/blob/magento-1.9.2.1/app/code/core/Mage/Adminhtml/Block/System/Config/Form.php#L522
		 * Т.к. комментарий — на русском языке, то перевода для него не находится,
		 * и в итоге наш метод должен вернуть комментарий в неизменном виде,
		 * с сохранением всех переносов строк, чтобы администратору было удобно его читать.
		 * @var string $textOriginal
		 */
		$textOriginal = $text;
		$text = str_replace($symbolsToRemove, '', $text);
		$code = str_replace($symbolsToRemove, '', $code);
		/** @var string $result */
		$result = null;
		/**
		 * 2015-07-04
		 * Замечение №1
		 * Заметил интересную ситуацию в магазине chepe.ru:
		 * в период эксплуатации магазина до установки Российской сборки Magento
		 * около сотни интерфейсных строк были переведены интерактивно:
		 * это когда администратор переводит строки приямо на витрине, перевод сохраняется в БД.
		 * Так вот, в шаблоне app/design/frontend/studiyaak/default/template/page/html/topmenu.phtml
		 * там нестандартная строка $this->__( 'Production' ),
		 * которая после интерактивного перевода сохранились в БД как
		 * Mage_Page::Production => Продукция.
		 * После установки Российской сборки Magento соответствующий класс блок был перекрыт,
		 * и система стала искать не Mage_Page::Production, а Df_Page::Production,
		 * не находя, разумеется, перевод из БД.
		 *
		 * Написал заплатку для подобной ситуации.
		 * Думаю, эта заплатка будет полезной всем магазинам,
		 * которые использовали интерактивный перевод до установки Российской сборки Magento.
		 *
		 * Замечение №2
		 * Заметил ещё одну проблему Вы описанной выше ситуации:
		 * получается, что перевод Российской сборки
		 * перекрывает инлайновый перевод, сделанный до установки РСМ,
		 * потому что код перевода РСМ начинается с Df_,
		 * а код инлайнового перевода до установки РСМ начинается с Mage_.
		 * Пример — перевод фразы «Sort By».
		 * В БД хранится инлайновый перевод с кодом Mage_Catalog::Sort By.
		 * РСМ же делала так:
			<translate>
				<modules>
					<Df_Catalog>
						<files>
							<default>Mage_Catalog.csv</default>
						</files>
					</Df_Catalog>
				</modules>
			</translate>
		 * То есть РСМ повторно загружала строки файла Mage_Catalog.csv,
		 * только со своим префиксом Df_Catalog.
		 * Далее, РСМ искала ключ с префиксом Df_Catalog и находила его,
		 * игнорируя инлайновый перевод до установки РСМ.
		 *
		 * Мало того, что этот подход дефектен, так он ещё и ведёт
		 * к удвоению расхода оперативной памяти и процессорного времени
		 * на хранение и обработку словарей, ведь словари ядра грузятся дальше.
		 * Куда только я раньше смотрел???
		 *
		 * Переделал по-правильному.
		 */
		/** @var bool $isItRmCode */
		$isItRmCode = ('Df_' === substr($code, 0, 3));
		/**
		 * Обратите внимание, что в ядре PHP нет функции @see mb_str_replace
		 * Используем уж @see str_replace,
		 * тем более, что мы здесь замещаем только латинские символы.
		 */
		/** @var string $originalCode */
		$originalCode = !$isItRmCode ? null : str_replace('Df_', 'Mage_', $code);
		/** @var bool $needUseRmTranslator */
		static $needUseRmTranslator;
		if (is_null($needUseRmTranslator)) {
			/**
			 * 2015-03-10
			 * Раньше переменная $needUseRmTranslator инициализировалась значением false,
			 * если function_exists('df_model') возвращало false.
			 *
			 * Обратите внимание, что переменная $needUseRmTranslator — статическая,
			 * и, значит, она инициализируется навечно.
			 * Как ни странно, это ранее почему-то не приводило к проблемам.
			 * Видимо, потому что объект-одиночка нашего класса @see Df_Core_Model_Translate
			 * создавался не вполне корректно:
			 * public static function s() {static $r; return $r ? $r : $r = new self;}
			 *
			 * Правильно, конечно, создавать наш объект-одиночку так:
			 * public static function s() {return Mage::getSingleton('core/translate');}
			 * Этот код возвращает объект, который уже был инициализирован ядром Magento ранее,
			 * ведь наш класс перекрывает системный класс @see Mage_Core_Model_Translate
			 * А ошибочный код выше создавал новый объект=одиночку,
			 * игнорируя уже созданный ядром Magento объект, и в системе существовало
			 * сразу два объекта-одиночки одного класса,
			 * что приводило, в частности, к дублированию кэша.
			 *
			 * Теперь же я это исправил, и здесь, видимо, тоже надо исправить:
			 * вместо проверки function_exists('df_model')
			 * сразу инициализировать Российскую сборку Magento.
			 *
			 * Ещё одной нормальной альтернативой является применение метода
			 * @see Df_Core_Boot::done(), чтобы использовать
			 * @uses Df_Localization_Realtime_Translator
			 * уже после инициализации Российской сборки Magento,
			 * которая случиться где-нибудь в другом месте позже.
			 */
			Df_Core_Boot::run();
			/**
			 * Не используем Df_Localization_Realtime_Translator
			 * в процессе установки Magento Community Edition,
			 * потому что в это время Российская сборка ещё не установлена и не инициализирована,
			 * и использование Df_Localization_Realtime_Translator::s() приводит к сбою
			 * Call to undefined function df_model().
			 *
			 * 2015-08-08
			 * Обратите внимание, что @see Df_Localization_Realtime_Translator
			 * не используетс также для административной части:
			 * @uses Df_Localization_Realtime_Translator::isEnabled()
			 */
			$needUseRmTranslator =
				Mage::isInstalled() && Df_Localization_Realtime_Translator::s()->isEnabled()
			;
		}
		if ($needUseRmTranslator) {
			/** @var Df_Localization_Realtime_Translator */
			static $rmTranslator;
			if (!$rmTranslator) {
				$rmTranslator = Df_Localization_Realtime_Translator::s();
			}
			$result = $rmTranslator->translate($text, $code);
			if ((is_null($result) || $text === $result) && $originalCode) {
				$result = $rmTranslator->translate($text, $originalCode);
			}
		}
		if (is_null($result) || ($text === $result)) {
			/**
			 * Раньше тут стояло:
			 * $result = parent::_getTranslatedString($text, $code);
			 * Вместо вызова родительского метода
			 * @see Mage_Core_Model_Translate::_getTranslatedString()
			 * реализуем его алгоритм несколько другим, более быстрым способом:
			 * заменив @see array_key_exists на @see isset
			 * http://stackoverflow.com/a/700257
			 */
			if (!$this->_data) {
				$result = $text;
			}
			else if (isset($this->_data[$code])) {
				$result = $this->_data[$code];
			}
			else if ($originalCode && isset($this->_data[$originalCode])) {
				$result = $this->_data[$originalCode];
			}
			else if (isset($this->_data[$text])) {
				$result = $this->_data[$text];
			}
			else {
				/**
				 * 2015-08-08
				 * Перевод не найден или не нужен.
				 * Возвращаем оригинальное значение со всеми спецсимволами.
				 * Конкретная ситуация: я в файле system.xml своего модуля
				 * пишу видимый администратору комментарий к настроечной опции на русском языке,
				 * используя в том числе переносы строк.
				 * Magento CE пытается перевести этот комментарий:
				 * @used-by Mage_Adminhtml_Block_System_Config_Form::_prepareFieldComment():
				 * $comment = Mage::helper($helper)->__($commentInfo);
				 * https://github.com/OpenMage/magento-mirror/blob/magento-1.9.2.1/app/code/core/Mage/Adminhtml/Block/System/Config/Form.php#L522
				 * Т.к. комментарий — на русском языке, то перевода для него не находится,
				 * и в итоге наш метод должен вернуть комментарий в неизменном виде,
				 * с сохранением всех переносов строк, чтобы администратору было удобно его читать.
				 */
				$result = $textOriginal;
			}
		}
		if (
			Df_Localization_Realtime_Translator::$watched
			&& (
				Df_Localization_Realtime_Translator::$needle
				&& df_contains_ci($text, Df_Localization_Realtime_Translator::$watched)
				|| $text === Df_Localization_Realtime_Translator::$watched
			)
		) {
			Mage::log('text: ' . $text);
			Mage::log('code: ' . $code);
			Mage::log('result: ' . $result);
			//df_bt();
			/** @var bool $isFirstTime */
			static $isFirstTime = true;
			if ($isFirstTime && $this->getData()) {
				Mage::log(
					$this->getData()
					, $level = null
					, $file = 'rm.translation.dictionary.log'
					, $forceLog = true
				);
				$isFirstTime = false;
			}
		}
		return $result;
	}

	/**
	 * @override
	 * @param string $moduleName
	 * @param array $files
	 * @param bool $forceReload
	 * @return Df_Core_Model_Translate
	 */
	protected function _loadModuleTranslation($moduleName, $files, $forceReload=false) {
		/** @var bool $localeIsRussian */
		static $localeIsRussian;
		if (is_null($localeIsRussian)) {
			$localeIsRussian = df_h()->localization()->locale()->isRussian();
		}
		/** @var bool $needEnableRmTranslation */
		$needEnableRmTranslation = $this->needEnableRmTranslation();
		/** @var bool $needSetRmTranslationAsPrimary */
		$needSetRmTranslationAsPrimary = $this->needSetRmTranslationAsPrimary();
		/** @var string $ruDfBasePath */
		static $ruDfBasePath;
		if (!$ruDfBasePath) {
			$ruDfBasePath = $this->getBaseDirLocale() . DS . self::LOCALE__RU_DF . DS;
		}
		foreach ($files as $file) {
			/** @var string $file */
			/** @var string[] $paths */
			$paths = [];
			/** @var bool $isItRmModule */
			/**
			 * Раньше тут стоял код:
			 * $isItRmModule = df_starts_with($file, 'Df_');
			 * Измнил этот код ради ускорения.
			 */
			$isItRmModule = ('Df_' === substr($file, 0, 3));
			if ($localeIsRussian && ($isItRmModule || $needEnableRmTranslation)) {
				$paths[]=
					/**
					 * Работает в 2 раза быстрее, чем implode
					 * http://stackoverflow.com/questions/4502654/php-many-concats-or-one-implode
					 */
					$ruDfBasePath . $file
				;
			}
			if (!$isItRmModule) {
				$paths[]= $this->_getModuleFilePath($moduleName, $file);
			}
			if ($needSetRmTranslationAsPrimary) {
				$paths = array_reverse($paths);
			}
			foreach ($paths as $path) {
				$this->_addData($this->_getFileData($path), $moduleName, $forceReload);
			}
		}
		return $this;
	}

	/**
	 * 2015-03-10
	 * Более быстрая альтернатива @see Mage_Core_Model_Translate::_getTranslatedString()^
	 * вместо @see array_key_exists использует @uses isset()
	 * http://stackoverflow.com/a/700257
	 * @param string $text
	 * @param string $code
	 * @return string
	 */
	private function _getTranslatedStringLowLevel($text, $code) {
		return
			!$this->_data
			? $text
			: (
				isset($this->_data[$code])
				? $this->_data[$code]
				: (
					isset($this->_data[$text])
					? $this->_data[$text]
					: $text
				)
			)
		;
	}

	/** @return string */
	private function getBaseDirLocale() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Mage::getBaseDir('locale');
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $localeCode
	 * @param string $fileType
	 * @param string $fileName
	 * @return string
	 */
	private function getTemplateFilePathForLocale($localeCode, $fileType, $fileName) {
		df_param_string_not_empty($localeCode, 0);
		return
			df_cc_path(
				$this->getBaseDirLocale(), $localeCode, 'template', $fileType, $fileName
			)
		;
	}

	/** @return bool */
	private function isRuRuExist() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = is_dir($this->getBaseDirLocale() . DS . 'ru_RU');
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function needDisableTranslation() {
		if (!isset($this->{__METHOD__})) {
			/** @var bool $result */
			$result = false;
			foreach (df_config_a('df/disable-translation') as $suffix) {
				/** @var string $suffix */
				if (df_ruri_contains($suffix)) {
					$result = true;
					break;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function needEnableRmTranslation() {
		/** @var bool $result */
		static $result;
		if (is_null($result)) {
			/** @var bool $result */
			$result =
					df_h()->localization()->locale()->isRussian()
				&&
					(
							// Используем для экранов установки перевод Российской сборки Magento
							!Mage::isInstalled()
						||
							// В системе выбрана русская локаль и присутствует только перевод
							// Российской сборки Magento, поэтому автоматически включаем его
							// и назначаем основным
							!$this->isRuRuExist()
						||
							df_loc()->isEnabled()
					)
			;
		}
		return $result;
	}

	/** @return bool */
	private function needSetRmTranslationAsPrimary() {
		/** @var bool $result */
		static $result;
		if (is_null($result)) {
			/** @var bool $result */
			$result =
					// Используем для экранов установки перевод Российской сборки Magento
					!Mage::isInstalled()
				||
					// В системе выбрана русская локаль и присутствует только перевод
					// Российской сборки Magento, поэтому автоматически включаем его
					// и назначаем основным
					!$this->isRuRuExist()
				||
					df_loc()->needSetAsPrimary()
			;
		}
		return $result;
	}

	/**
	 * @used-by Df_Core_Model_Translate::_getFileData()
	 * @param string|string[] $text
	 * @return string|string[]
	 */
	private function processSpecialCharacters($text) {
		return
			is_array($text)
			? array_map(array($this, __FUNCTION__), $text)
			: strtr($text, array('{\n}' => "\n", '{\r}' => "\r", '{\t}' => "\t"))
		;
	}


	const LOCALE__RU_DF = 'ru_DF';
	/** @return Df_Core_Model_Translate */
	public static function s() {return Mage::getSingleton('core/translate');}
}