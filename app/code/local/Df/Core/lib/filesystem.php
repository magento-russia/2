<?php
if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

/**
 * 2015-11-28
 * http://stackoverflow.com/a/10368236
 * @param string $fileName
 * @return string
 */
function df_file_ext($fileName) {return pathinfo($fileName, PATHINFO_EXTENSION);}

/**
 * Возвращает неиспользуемое имя файла в заданной папке $directory по заданному шаблону $template.
 * Результатом всегда является непустая строка.
 * @used-by Autostyler_Import_Model_Action::getLogFilePath()
 * @used-by Df_C1_Helper_Data::logger()
 * @used-by df_report()
 * @used-by Df_Core_Model_Action::responseLogName()
 * @used-by \Df\Xml\Generator\Document::createLogger()
 * @used-by Df_YandexMarket_Helper_Data::getLogger()
 * @param string $directory
 * @param string $template
 * @param string $datePartsSeparator [optional]
 * @return string
 */
function df_file_name($directory, $template, $datePartsSeparator = '-') {
	return Df_Core_Model_Fs_GetNotUsedFileName::r($directory, $template, $datePartsSeparator);
}

/**
 * @param string $filePath
 * @param string $contents
 * @return void
 * @throws Exception
 */
function df_file_put_contents($filePath, $contents) {
	df_param_string_not_empty($filePath, 0);
	df_path()->createAndMakeWritable($filePath);
	/** @var int|bool $r */
	$r = file_put_contents($filePath, $contents);
	df_assert(false !== $r);
}

/**
 * 2015-11-29
 * Преобразует строку таким образом,
 * чтобы её было безопасно и удобно использовать в качестве имени файла или папки.
 * http://stackoverflow.com/a/2021729
 * @param string $name
 * @param string $spaceSubstitute [optional]
 * @return string
 */
function df_fs_name($name, $spaceSubstitute = '-') {
	$name = str_replace(' ', $spaceSubstitute, $name);
	// Remove anything which isn't a word, whitespace, number
	// or any of the following caracters -_~,;:[]().
	// If you don't need to handle multi-byte characters
	// you can use preg_replace rather than mb_ereg_replace
	// Thanks @Łukasz Rysiak!
	$name = mb_ereg_replace("([^\w\s\d\-_~,;:\[\]\(\).])", '', $name);
	// Remove any runs of periods (thanks falstro!)
	return mb_ereg_replace("([\.]{2,})", '', $name);
}

/** @return \Df\Core\Helper\Path */
function df_path() {return \Df\Core\Helper\Path::s();}

/**
 * Заменяет все сиволы пути на /
 * @param string $path
 * @return string
 */
function df_path_n($path) {return str_replace('\\', '/', $path);}

/**
 * 2015-12-06
 * Левый «/» мы убираем.
 * @param string $path
 * @param string $base [optional]
 * @return string
 */
function df_path_relative($path, $base = BP) {return
	df_trim_ds_left(df_trim_text_left(
		df_path_n($path), df_trim_ds_left(df_path_n($base))
	))
;}

/**
 * 2015-04-01
 * Раньше алгоритм был таким: return preg_replace('#\.[^.]*$#', '', $file)
 * Новый вроде должен работать быстрее?
 * http://stackoverflow.com/a/22537165
 * @used-by Df_Adminhtml_Catalog_Product_GalleryController::uploadActionDf()
 * @used-by
 * @param string $file
 * @return mixed
 */
function df_strip_ext($file) {return pathinfo($file, PATHINFO_FILENAME);}

/**
 * 2016-10-14
 * @param string $path
 * @return string
 */
function df_trim_ds($path) {return df_trim($path, '/\\');}

/**
 * 2015-11-30
 * @param string $path
 * @return string
 */
function df_trim_ds_left($path) {return df_trim_left($path, '/\\');}

/**
 * 2016-10-14
 * @param string $path
 * @return string
 */
function df_trim_ds_right($path) {return df_trim_right($path, '/\\');}
 