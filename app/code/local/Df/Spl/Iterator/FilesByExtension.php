<?php
class Df_Spl_Iterator_FilesByExtension extends Df_Spl_Iterator_Directory {
	/**
	 * @override
	 * @param SplFileInfo $fileInfo
	 * @return bool
	 */
	protected function isValid(SplFileInfo $fileInfo) {
		return $fileInfo->isFile() && df_ends_with($fileInfo->getFilename(), $this->getExtension());
	}

	/** @return string */
	private function getExtension() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = $this->cfg(self::P__EXTENSION);
			if (!df_starts_with($result, '.')) {
				$result = '.' . $result;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__EXTENSION, DF_V_STRING_NE);
	}
	const P__EXTENSION = 'extension';
	/**
	 * @param string $path
	 * @param string $extension
	 * @return Df_Spl_Iterator_FilesByExtension
	 */
	public static function i($path, $extension) {return new self(array(
		self::P__PATH => $path, self::P__EXTENSION => $extension
	));}
}