<?php
class Df_1C_Cml2_Import_Data_Collection_ProductPart_Images
	extends Df_1C_Cml2_Import_Data_Collection {
	/**
	 * @uses unlink()
	 * @return void
	 */
	public function deleteFiles() {@array_map('unlink', $this->getFullPaths());}

	/** @return string[] */
	public function getFullPaths() {
		if (!isset($this->{__METHOD__})) {
			/** @uses Df_1C_Cml2_Import_Data_Entity_ProductPart_Image::getFilePathFull() */
			$this->{__METHOD__} = df_each($this, 'getFilePathFull');
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return Df_1C_Cml2_Import_Data_Entity_ProductPart_Image::class;}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemPath()
	 * @return string|string[]
	 */
	protected function itemPath() {return 'Картинка';}

	/**
	 * @used-by Df_1C_Cml2_Import_Data_Entity_ProductЖЖgetImages()
	 * @static
	 * @param \Df\Xml\X $e
	 * @return Df_1C_Cml2_Import_Data_Collection_ProductPart_Images
	 */
	public static function i(\Df\Xml\X $e) {return new self(array(self::$P__E => $e));}
}