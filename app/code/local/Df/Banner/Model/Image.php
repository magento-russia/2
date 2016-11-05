<?php
class Df_Banner_Model_Image extends Df_Core_Model {
	protected $_width;
	protected $_height;
	protected $_keepAspectRatio  = true;
	protected $_keepFrame		= true;
	protected $_keepTransparency = true;
	protected $_constrainOnly	= false;
	protected $_backgroundColor  = array(255, 255, 255);
	protected $_baseFile;
	protected $_newFile;
	protected $_baseDir;
	protected $_processor;
	protected $_destinationSubdir;
	protected $_angle;
	protected $_watermarkPosition;
	protected $_watermarkWidth;
	protected $_watermarkHeigth;

	/**
	 * @param mixed $width
	 * @return Df_Banner_Model_Image
	 */
	public function setWidth($width) {
		$this->_width = $width;
		return $this;
	}

	/** @return mixed */
	public function getWidth() {return $this->_width;}

	/**
	 * @param mixed $height
	 * @return Df_Banner_Model_Image
	 */
	public function setHeight($height) {
		$this->_height = $height;
		return $this;
	}

	/** @return mixed */
	public function getHeight() {return $this->_height;}

	/**
	 * @param mixed $keep
	 * @return Df_Banner_Model_Image
	 */
	public function setKeepAspectRatio($keep) {
		$this->_keepAspectRatio = !!$keep;
		return $this;
	}

	/**
	 * @param mixed $keep
	 * @return Df_Banner_Model_Image
	 */
	public function setKeepFrame($keep) {
		$this->_keepFrame = !!$keep;
		return $this;
	}

	/**
	 * @param mixed $keep
	 * @return Df_Banner_Model_Image
	 */
	public function setKeepTransparency($keep) {
		$this->_keepTransparency = !!$keep;
		return $this;
	}

	/**
	 * @param mixed $flag
	 * @return Df_Banner_Model_Image
	 */
	public function setConstrainOnly($flag) {
		$this->_constrainOnly = !!$flag;
		return $this;
	}

	/**
	 * @param array $rgbArray
	 * @return Df_Banner_Model_Image
	 */
	public function setBackgroundColor(array $rgbArray) {
		$this->_backgroundColor = $rgbArray;
		return $this;
	}

	/**
	 * @param mixed $size
	 * @return Df_Banner_Model_Image
	 */
	public function setSize($size) {
		// determine width and height from string
		list($width, $height) = explode('x', strtolower($size), 2);
		foreach (array('width', 'height') as $wh) {
			$$wh  = (int)$$wh;
			if (empty($$wh))
				$$wh = null;
		}

		// set sizes
		$this->setWidth($width)->setHeight($height);
		return $this;
	}

	/**
	 * @param null $file
	 * @return bool
	 */
	protected function _checkMemory($file = null) {
		return $this->_getMemoryLimit() > ($this->_getMemoryUsage() + $this->_getNeedMemoryForFile($file));
	}

	/** @return int|string */
	protected function _getMemoryLimit() {
		$memoryLimit = ini_get('memory_limit');
		if (!isSet($memoryLimit[0])){
			$memoryLimit = "128M";
		}
		if ('M' === substr($memoryLimit, -1)) {
			return(int)$memoryLimit * 1024 * 1024;
		}
		return $memoryLimit;
	}

	protected function _getMemoryUsage() {
		if (function_exists('memory_get_usage')) {
			return memory_get_usage();
		}
		return 0;
	}

	protected function _getNeedMemoryForFile($file = null)
	{
		$file = is_null($file) ? $this->getBaseFile() : $file;
		if (!$file) {
			return 0;
		}

		if (!file_exists($file) || !is_file($file)) {
			return 0;
		}

		$imageInfo = getimagesize($file);
		if (!isset($imageInfo['channels'])) {
			// if there is no info about this parameter lets set it for maximum
			$imageInfo['channels'] = 4;
		}
		if (!isset($imageInfo['bits'])) {
			// if there is no info about this parameter lets set it for maximum
			$imageInfo['bits'] = 8;
		}
		return round(($imageInfo[0] * $imageInfo[1] * $imageInfo['bits'] * $imageInfo['channels'] / 8 + Pow(2, 16)) * 1.65);
	}

	/**
	 * Convert array of 3 items (decimal r, g, b) to string of their hex values
	 *
	 * @param array $rgbArray
	 * @return string
	 */
	private function _rgbToString($rgbArray) {
		$result = [];
		foreach ($rgbArray as $value) {
			$result[]= is_null($value) ? 'null' : sprintf('%02s', dechex($value));
		}
		return implode($result);
	}

	/**
	 * @param string $file
	 * @return Df_Banner_Model_Image
	 * @throws Exception
	 */
	public function setBaseFile($file)
	{
		$subDir = '';
		if ($file) {
			if (!df_starts_with($file, '/', 0)) {
				$file = '/' . $file;
			}

			$pos = strripos($file, '/');
			if ($pos!==false && $pos!==0) {
				$subDir = mb_substr($file, 0, $pos);
				$file = mb_substr($file, $pos);
			}
		}
		//$baseDir = df_mage()->catalog()->productMediaConfig()->getBaseMediaPath();
		$baseDir = Mage::getBaseDir('media') . $subDir;
		$this->_baseDir = Mage::getBaseDir('media') . DS;
		if ('/no_selection' === $file) {
			$file = null;
		}
		if ($file) {
			if ((!file_exists($baseDir . $file)) || !$this->_checkMemory($baseDir . $file)) {
				$file = null;
			}
		}

		/*
		if (!$file) {
			// check if placeholder defined in config
			$isConfigPlaceholder = Mage::getStoreConfig("catalog/placeholder/{$this->getDestinationSubdir()}_placeholder");
			$configPlaceholder   = '/placeholder/' . $isConfigPlaceholder;
			if ($isConfigPlaceholder && file_exists($baseDir . $configPlaceholder)) {
				$file = $configPlaceholder;
			}
			else {
				// replace file with skin or default skin placeholder
				$skinBaseDir	 = Mage::getDesign()->getSkinBaseDir();
				$skinPlaceholder = "/images/catalog/product/placeholder/{$this->getDestinationSubdir()}.jpg";
				$file = $skinPlaceholder;
				if (file_exists($skinBaseDir . $file)) {
					$baseDir = $skinBaseDir;
				}
				else {
					$baseDir = Mage::getDesign()->getSkinBaseDir(array('_theme' => 'default'));
				}
			}
		}
		*/

		$baseFile = $baseDir . $file;
		if ((!$file) || (!file_exists($baseFile))) {
			df_error(df_mage()->catalogHelper()->__('Image file not found'));
		}
		$this->_baseFile = $baseFile;
		// build new filename (most important params)
		$path = array(
			'df_banner','cache'
		);
		if ((!empty($this->_width)) || (!empty($this->_height)))
			$path[]= "{$this->_width}x{$this->_height}";
		// add misc params as a hash
		$path[]= md5(
			implode('_', array(
				($this->_keepAspectRatio  ? '' : 'non') . 'proportional',($this->_keepFrame		? '' : 'no')  . 'frame',($this->_keepTransparency ? '' : 'no')  . 'transparency',($this->_constrainOnly ? 'do' : 'not')  . 'constrainonly',$this->_rgbToString($this->_backgroundColor),'angle' . $this->_angle
			))
		);
		// append prepared filename
		$this->_newFile = df_c(df_cc_path($path), $file); // the $file contains heading slash
		return $this;
	}

	public function getBaseFile()
	{
		return $this->_baseFile;
	}

	public function getBaseDir()
	{
		return $this->_baseDir;
	}

	public function getNewFile()
	{
		return $this->_newFile;
	}

	/** @return Df_Banner_Model_Image */
	public function setImageProcessor($processor)
	{
		$this->_processor = $processor;
		return $this;
	}

	/** @return Varien_Image */
	public function getImageProcessor()
	{
		if (!$this->_processor ) {
//			var_dump($this->_checkMemory());
//			if (!$this->_checkMemory()) {
//				$this->_baseFile = null;
//			}
			$this->_processor = new Varien_Image($this->getBaseFile());
		}
		$this->_processor->keepAspectRatio($this->_keepAspectRatio);
		$this->_processor->keepFrame($this->_keepFrame);
		$this->_processor->keepTransparency($this->_keepTransparency);
		$this->_processor->constrainOnly($this->_constrainOnly);
		$this->_processor->backgroundColor($this->_backgroundColor);
		return $this->_processor;
	}

	/**
	 * @see Varien_Image_Adapter_Abstract
	 * @return Df_Banner_Model_Image
	 */
	public function resize()
	{
		if (is_null($this->getWidth()) && is_null($this->getHeight())) {
			return $this;
		}
		$this->getImageProcessor()->resize($this->_width, $this->_height);
		return $this;
	}

	/** @return Df_Banner_Model_Image */
	public function rotate($angle) {
		$angle = df_round($angle);
		$this->getImageProcessor()->rotate($angle);
		return $this;
	}

	/**
	 * Set angle for rotating
	 *
	 * This func actually affects only the cache filename.
	 *
	 * @param int $angle
	 * @return Df_Banner_Model_Image
	 */
	public function setAngle($angle)
	{
		$this->_angle = $angle;
		return $this;
	}

	/** @return Df_Banner_Model_Image */
	public function setWatermark($file, $position=null, $size=null, $width=null, $heigth=null)
	{
		$filename = false;
		if (!$file ) {
			return $this;
		}

		$baseDir = df_mage()->catalog()->productMediaConfig()->getBaseMediaPath();
		if (file_exists($baseDir . '/watermark/stores/' . df_store_id() . $file)) {
			$filename = $baseDir . '/watermark/stores/' . df_store_id() . $file;
		} else if (file_exists($baseDir . '/watermark/websites/' . df_website_id() . $file)) {
			$filename = $baseDir . '/watermark/websites/' . df_website_id() . $file;
		} else if (file_exists($baseDir . '/watermark/default/' . $file)) {
			$filename = $baseDir . '/watermark/default/' . $file;
		} else if (file_exists($baseDir . '/watermark/' . $file)) {
			$filename = $baseDir . '/watermark/' . $file;
		} else {
			$baseDir = Mage::getDesign()->getSkinBaseDir();
			if (file_exists($baseDir . $file)) {
				$filename = $baseDir . $file;
			}
		}

		if ($filename ) {
			$this->getImageProcessor()
				->setWatermarkPosition( ($position) ? $position : $this->getWatermarkPosition())
				->setWatermarkWidth( ($width) ? $width : $this->getWatermarkWidth())
				->setWatermarkHeigth( ($heigth) ? $heigth : $this->getWatermarkHeigth())
				->watermark($filename);
		}
		return $this;
	}

	/** @return Df_Banner_Model_Image */
	public function saveFile()
	{
		$this->getImageProcessor()->save($this->getBaseDir().$this->getNewFile());
		return $this;
	}

	/** @return string */
	public function getUrl()
	{
		$baseDir = Mage::getBaseDir('media');
		$path = str_replace($baseDir . DS, '', $this->_newFile);
		return Mage::getBaseUrl('media') . str_replace(DS, '/', $path);
	}

	public function push()
	{
		$this->getImageProcessor()->display();
	}

	/** @return Df_Banner_Model_Image */
	public function setDestinationSubdir($dir)
	{
		$this->_destinationSubdir = $dir;
		return $this;
	}

	/** @return string */
	public function getDestinationSubdir()
	{
		return $this->_destinationSubdir;
	}

	public function isCached()
	{
		return file_exists($this->getBaseDir().$this->_newFile);
	}

	/** @return Df_Banner_Model_Image */
	public function setWatermarkPosition($position)
	{
		$this->_watermarkPosition = $position;
		return $this;
	}

	public function getWatermarkPosition()
	{
		return $this->_watermarkPosition;
	}

	/** @return Df_Banner_Model_Image */
	public function setWatermarkSize($size) {
		if (is_array($size)) {
			$this->setWatermarkWidth($size['width']);
			$this->setWatermarkHeigth($size['heigth']);
		}
		return $this;
	}

	/** @return Df_Banner_Model_Image */
	public function setWatermarkWidth($width)
	{
		$this->_watermarkWidth = $width;
		return $this;
	}

	public function getWatermarkWidth()
	{
		return $this->_watermarkWidth;
	}

	/** @return Df_Banner_Model_Image */
	public function setWatermarkHeigth($heigth)
	{
		$this->_watermarkHeigth = $heigth;
		return $this;
	}

	public function getWatermarkHeigth()
	{
		return $this->_watermarkHeigth;
	}

	public function clearCache()
	{
		$directory = Mage::getBaseDir('media') . DS.'gallery'.DS.'cache'.DS;
		$io = new Varien_Io_File();
		$io->rmdir($directory, true);
	}
	/**
	 * @used-by Df_Banner_Helper_Image::init()
	 * @return Df_Banner_Model_Image
	 */
	public static function i() {return new self;}
}