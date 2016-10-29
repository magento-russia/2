<?php
/**
 * 2015-08-14
 * @return Mage_Core_Model_Cache
 */
function df_cache() {static $r; return $r ? $r : $r = Mage::app()->getCacheInstance();}

/**
 * 2015-02-10
 *
 * @uses Mage_Core_Model_Cache::flush().
 * Вызов Mage::app()->getCacheInstance()->flush()
 * соответствует действию административной кнопки «удалить веь кэш (Mаgento и другой)».
 * Например, кэш храниться в файлах (он там хранится по умолчанию),
 * то вызов Mage::app()->getCacheInstance()->flush() удалит всё содержимое папки с файлами кэша
 * (по умолчанию это папка «var/cache»).
 *
 * Вызов Mage::app()->getCache()->clean()
 * @see Mage_Core_Model_Cache::clean()
 * соответствует действию административной кнопки «удалить весь кэш Mаgento».
 * При этом удаляется кэш модулей Magento CE/EE, однако кэш сторонних модулей,
 * в том числе и кэш Российской сборки Magento, может не удаляться.
 *
 * Поэтому использовать Mage::app()->getCacheInstance()->flush() надёжнее,
 * чем Mage::app()->getCache()->clean().
 *
 * Обратите внимание, что Magento кэширует структуру базы данных.
 * При этом в Magento есть метод @see Varien_Db_Adapter_Pdo_Mysql::resetDdlCache()
 * для удаления кэша либо всей структуры базы данных (при вызове без параметров),
 * либо структуры базы данных конкретной таблицы (при вызове с параметром: именем таблицы).
 * Однако после изменения структуры базы данных опасно ограничиваться только вызовом
 * @see Varien_Db_Adapter_Pdo_Mysql::resetDdlCache(),
 * потому что после изменения структуры базы данных может оказаться устаревшим
 * и кэш объектов слоя бизнес-логики.
 * Поэтому в любом случае надёжней использовать @see df_cache_clean().
 * Другие методы (частичная очистка кэша) могут быть эффективнее,
 * но используйте их с осторожностью.
 * @return void
 */
function df_cache_clean() {df_cache()->flush();}

/**
 * 2015-08-13
 * @param string $type
 * @return bool
 */
function df_cache_enabled($type) {return df_cache()->canUse($type);}

/**
 * 2016-07-18
 * 2016-10-28
 * Добавил дополнительный уровень кэширования: в оперативной памяти.
 * Также позволил в качестве $key передавать массив.
 * @param string|string[] $key
 * @param callable $method
 * @param mixed[] ...$arguments [optional]
 * @return mixed
 */
function df_cache_get_simple($key, callable $method, ...$arguments) {return
	dfcf(function($key) use ($method, $arguments) {
		/** @var string|bool $resultS */
		$resultS = df_cache_load($key);
		/** @var mixed $result */
		$result = null;
		if (false !== $resultS) {
			/** @var array(string => mixed) $result */
			$result = df_unserialize_simple($resultS);
		}
		/**
		 * 2016-10-28
		 * json_encode(null) возвращает строку 'null',
		 * а json_decode('null') возвращает null.
		 * Поэтому если $resultS равно строке 'null',
		 * то нам не надо вызывать функцию: она уже вызывалась,
		 * и (кэшированным) результатом этого вызова было значение null.
		 */
		if (null === $result && 'null' !== $resultS) {
			$result = call_user_func_array($method, $arguments);
			df_cache_save(df_serialize_simple($result), $key);
		}
		return $result;
	}, [!is_array($key) ? $key : dfa_hashm($key)])
;}

/**
 * 2016-08-25
 * Можно, конечно, реализовать функцию как return df_cc('::', $params);
 * но для ускорения я сделал иначе.
 * @param string[] ...$p
 * @return string
 */
function df_ckey(...$p) {return !$p ? '' : implode('::', is_array($p[0]) ? $p[0] : $p);}

/**
 * 2015-08-13
 * @param string $key
 * @return string|false
 */
function df_cache_load($key) {return df_cache()->load($key);}

/**
 * 2016-07-18
 * @param mixed $data
 * @param string $key
 * @param string[] $tags [optional]
 * @param int|null $lifeTime [optional]
 * @return bool
 */
function df_cache_save($data, $key, $tags = [], $lifeTime = null) {
	return df_cache()->save($data, $key, $tags, $lifeTime);
}

/**
 * 2016-08-31
 * Кэш должен быть не глобальным, а храниться внутри самого объекта по 2 причинам:
 * 1) @see spl_object_hash() может вернуть одно и то же значение для разных объектов,
 * если первый объект уже был уничтожен на момент повторного вызова spl_object_hash():
 * http://php.net/manual/en/function.spl-object-hash.php#76220
 * 2) после уничтожения объекта нефиг замусоривать память его кэшем.
 *
 * @param object $o
 * @param \Closure $m
 * @param mixed[] $a [optional]
 * @return mixed
 */
function dfc($o, \Closure $m, array $a = []) {
	/** @var array(string => string) $b */
	$b = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
	/** @var string $k */
	$k = $b['class'] . '::' . $b['function'] . (!$a ? null : dfa_hash($a));
	return property_exists($o, $k) ? $o->$k : $o->$k = call_user_func_array($m, $a);
}

/**
 * 2016-09-04
 * Не используем решения типа такого: http://stackoverflow.com/a/34711505
 * потому что они возвращают @see \Closure, и тогда кэшируемая функция становится переменной,
 * что неудобно (неунифицировано и засоряет глобальную область видимости переменными).
 * @param \Closure $f
 * Используем именно  array $a = [], а не ...$a,
 * чтобы кэшируемая функция не перечисляла свои аргументы при передачи их сюда,
 * а просто вызывала @see func_get_args()
 * @param mixed[] $a [optional]
 * @return mixed
 */
function dfcf(\Closure $f, array $a = []) {
	/** @var array(string => mixed) $c */
	static $c = [];
	/** @var array(string => string) $b */
	$b = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
	// 2016-09-04
	// Когда мы кэшируем статический метод, то ключ «class» присутствует,
	// а когда функцию — то отсутствует: https://3v4l.org/ehu4O
	//Ради ускорения не используем свои функции dfa() и df_cc().
	/** @var string $k */
	$k = (!isset($b['class']) ? null : $b['class'] . '::') . $b['function'] . (!$a ? null : dfa_hash($a));
	// 2016-09-04
	// https://3v4l.org/9cQOO
	return array_key_exists($k, $c) ? $c[$k] : $c[$k] = call_user_func_array($f, $a);
}