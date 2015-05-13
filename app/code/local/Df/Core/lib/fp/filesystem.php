<?php
/**
 * @param string $filePath
 * @param string $contents
 * @return void
 * @throws Exception
 */
function rm_file_put_contents($filePath, $contents) {
	df_param_string_not_empty($filePath, 0);
	Df_Core_Helper_Path::s()->create(dirname($filePath));
	/** @var int|bool $r */
	$r = file_put_contents($filePath, $contents);
	df_assert(false !== $r);
}

/** @return Df_Core_Helper_Path */
function df_path() {return Df_Core_Helper_Path::s();}


 