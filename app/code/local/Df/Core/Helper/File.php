<?php
class Df_Core_Helper_File extends Mage_Core_Helper_Abstract {
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