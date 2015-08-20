<?php
class Df_Core_Helper_File extends Mage_Core_Helper_Abstract {
	/**
	 * 2015-08-20
	 * @used-by Df_Core_Helper_Path::getFilePath()
	 * @used-by Df_Core_Helper_Path::chmodRecursive()
	 * @used-by Df_Core_Model_Logger::getFilePath()
	 * @param string $resource
	 * @param int $mode [optional]
	 * @return void
	 * http://magento-forum.ru/topic/5197/
	 */
	public function chmod($resource, $mode = 0777) {
		/** @var bool $r */
		try {$r = chmod($resource, $mode);}
		catch (Exception $e) {$r = false;}
		if (!$r) {
			// Видимо, надо кого-то оповестить?
			// Но сам я пока не хочу такие оповещения получать: завалят ими.
			rm_log('Не могу установить права %o для %s.', $mode, $resource);
		}
	}

	/**
	 * @param string $file
	 * @return string
	 */
	public function getExt($file) {
		/** @var int $dotPosition */
		$dotPosition = mb_strrpos($file, '.') + 1;
		return mb_substr($file, $dotPosition);
	}

	/**
	 * @param string $fileName
	 * @return string
	 */
	public function getUniqueFileName($fileName) {
		$result = $fileName;
		if (file_exists($fileName)) {
			$fileInfo = pathinfo($fileName);
			$dirname = df_a($fileInfo, 'dirname');
			$extension = df_a($fileInfo, 'extension');
			$key = df_a($fileInfo, 'filename');
			$i = 1;
			while (1) {
				$result =
					$dirname . '/'
					. rm_concat_clean('.', $this->generateOrderedKey($key, $i++), $extension)
				;
				if (!file_exists($result)) {
					break;
				}
			}
		}
		df_result_string_not_empty($result);
		/**
		 * Раз путь к файлу уникален —
		 * значит, не должно быть уже загруженного файла с таким путём
		 */
		df_assert(!is_file($result));
		return $result;
	}

	/**
	 * @param string $file
	 * @return mixed
	 */
	public function stripExt($file) {return preg_replace('#\.[^.]*$#', '', $file);}

	/**
	 * @param string $key
	 * @param int $ordering
	 * @return string
	 */
	private function generateOrderedKey($key, $ordering) {
		return
			(1 === $ordering)
			? $key
			: implode('-', array($key, $ordering))
		;
	}

	/** @return Df_Core_Helper_File */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}