<?php
class Df_Varien_Image_Adapter_Gd2 extends Varien_Image_Adapter_Gd2 {
	/**
	 * @override
	 * @return string
	 */
	public function getMimeType() {
		if (is_null($this->_fileMimeType)) {
			list($this->_imageSrcWidth, $this->_imageSrcHeight, $this->_fileType, ) =
				getimagesize($this->_fileName)
			;
			$this->_fileMimeType = image_type_to_mime_type($this->_fileType);
		}
		return $this->_fileMimeType;
	}

	/** @return string */
	public function getOutput() {
		ob_start();
		/**
		 * @uses imagegif()
		 * @uses imagejpeg()
		 * @uses imagepng()
		 * @uses imagexbm()
		 * @uses imagewbmp()
		 */
		call_user_func($this->_getCallback('output'), $this->_imageHandler);
		/**
		 * Используем @, чтобы избежать сбоя «Failed to delete buffer zlib output compression».
		 * Такой сбой у меня возник на сервере moysklad.magento-demo.ru
		 * в другой точке программы при аналогичном вызове @uses ob_get_clean().
		 */
		return @ob_get_clean();
	}

	/**
 	  * Переопределяем данный метод, потому что он имеет область видимости private
	  * в родительском классе.
	  * @param string $callbackType
	  * @param int $fileType
	  * @return string
	  * @throws Exception
	  */
	 private function _getCallback(
		 $callbackType, $fileType = null, $unsupportedText = 'Unsupported image format.'
	 ) {
		 if (null === $fileType) {
			 $fileType = $this->_fileType;
		 }
		 if (empty(self::$_callbacks[$fileType])) {
			 throw new Exception($unsupportedText);
		 }
		 if (empty(self::$_callbacks[$fileType][$callbackType])) {
			 throw new Exception('Callback not found.');
		 }
		 return self::$_callbacks[$fileType][$callbackType];
	 }


	/**
	 * Переопределяем данную переменную,
	 * потому что она имеет область видимости private в родительском классе.
	 * @var array(int => array(string => string))
	 */
	private static $_callbacks = array(
		 IMAGETYPE_GIF  => array('output' => 'imagegif',  'create' => 'imagecreatefromgif')
		, IMAGETYPE_JPEG => array('output' => 'imagejpeg', 'create' => 'imagecreatefromjpeg')
		, IMAGETYPE_PNG  => array('output' => 'imagepng',  'create' => 'imagecreatefrompng')
		, IMAGETYPE_XBM  => array('output' => 'imagexbm',  'create' => 'imagecreatefromxbm')
		, IMAGETYPE_WBMP => array('output' => 'imagewbmp', 'create' => 'imagecreatefromxbm')
	);
}