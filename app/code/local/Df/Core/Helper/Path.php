<?php
class Df_Core_Helper_Path extends Mage_Core_Helper_Abstract {
	/**
	 * @param string $path
	 * @return string
	 */
	public function adjustSlashes($path) {
		return
			('\\' === DS)
			? str_replace('/', DS, $path)
			: str_replace('\\', DS, $path)
		;
	}

	/**
	 * @param string $path
	 * @param int $mode [optional]
	 * @return void
	 */
	public function chmodRecursive($path, $mode = 0777) {
		/** @var RecursiveIteratorIterator $iterator */
		$iterator =
			new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST
			)
		;
		foreach ($iterator as $item) {
		    chmod($item, $mode);
		}
	}

	/**
	 * @param string $path
	 * @return void
	 */
	public function create($path) {
		df_param_string_not_empty($path, 0);
		/** @var Varien_Io_File $file */
		$file = new Varien_Io_File();
		$file->setAllowCreateFolders(true);
		$file->createDestinationDir($path);
	}

	/**
	 * @param string $path
	 * @return void
	 */
	public function delete($path) {
		df_param_string_not_empty($path, 0);
		Varien_Io_File::rmdirRecursive($path);
	}

	/**
	 * @param string $path
	 * @return mixed
	 */
	public function makeRelative($path) {
		/** @var string $cleaned */
		$cleaned = $this->clean($path);
		/** @var string $base */
		$base = BP . DS;
		return rm_starts_with($cleaned, $base) ? str_replace($base, '', $cleaned) : $cleaned;
	}

	/**
	 * Заменяет все сиволы пути на /
	 * @param string $path
	 * @return string
	 */
	public function normalizeSlashes($path) {return str_replace('\\', '/', $path);}

	/**
	 * @param string $path
	 * @param int $mode [optional]
	 * @return void
	 */
	public function prepareForWriting($path, $mode = 0777) {
		df_param_string_not_empty($path, 0);
		if (!isset($this->{__METHOD__}[$path])) {
			if (!is_dir($path)) {
				mkdir($path, $mode, $recursive = true);
			}
			else {
				chmod($path, $mode);
			}
			$this->{__METHOD__}[$path] = true;
		}
	}

	/**
	 * @param string $path
	 * @return string
	 */
	public function removeTrailingSlash($path) {
		return
			!in_array(mb_substr($path, -1), array('/', DS))
			? $path
			: mb_substr($path, 0, -1)
		;
	}

	/**
	 * Function to strip additional / or \ in a path name
	 * @param string $path
	 * @param string $ds
	 * @return string
	 */
	private function clean($path, $ds = DS) {
		df_param_string($path, 0);
		df_param_string($path, 1);
		/** @var string $result */
		$result = df_trim($path);
		// Remove double slashes and backslahses
		// and convert all slashes and backslashes to DS
		$result = !$result ? BP : $this->adjustSlashes(preg_replace('#[/\\\\]+#u', $ds, $result));
		if (!df_check_string($result)) {
			df_error(
				strtr(
					"[{method}]:\tНе могу обработать путь {path}"
					,array('{method}%' => __METHOD__, '{path}' => $path)
				)
			);
		}
		return $result;
	}

	/** @return Df_Core_Helper_Path */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}