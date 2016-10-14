<?php
class Df_Catalog_Model_Product_Attribute_Backend_Media
	extends Mage_Catalog_Model_Product_Attribute_Backend_Media {
	/**
	 * Цели перекрытия:
	 * 1) Устранение сбоя
	 *    «Warning: Illegal string offset 'new_file'
	 *    in app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Media.php».
	 * 2) Устранение утраты информация о главной картинке у товара-дублёра при дублировании товара.
	 *
	 * @override
	 * @param Df_Catalog_Model_Product $object
	 * @return Df_Catalog_Model_Product_Attribute_Backend_Media
	 */
	public function beforeSave($object) {
		$this->setProduct($object);
		/** @var string $attrCode */
		$attrCode = $this->getAttribute()->getAttributeCode();
		/** @var array(string => string|array(string => mixed))|mixed $value */
		$value = $object->getData($attrCode);
		if (is_array($value) && isset($value['images'])) {
			/**
			 * Ключ «images» содержит информацию ообо всех картинках товара.
			 * Значением ключа является ассоциативный массив вида:
				[
			 		{
			 			"value_id":"1700"
			 			,"file":"d/e/detskij-bassejn-samolety-intex-168h40-58425.jpg"
			 			,"label":""
			 			,"position":"1"
			 			,"disabled":"0"
			 			,"label_default":null
			 			,"position_default":"1"
			 			,"disabled_default":"0"
			 			,"url":"http://localhost.com:720/media/catalog/product/d/e/detskij-bassejn-samolety-intex-168h40-58425.jpg"
			 		}
			 		,{
			 			"url":"http://localhost.com:720/media/tmp/catalog/product/u/p/upxza3qcta.jpg"
			 			,"file":"/u/p/upxza3qcta.jpg.tmp"
			 			,"label":""
			 			,"position":2
			 			,"disabled":0
			 			,"removed":0
			 		}
			 	]
			 * причём этот массив может быть как закодирован в строку в формате JSON,
			 * так и быть простым ассициативным массивом PHP.
			 */
			/** @var string|array(string => mixed) $images */
			$images = $value['images'];
			if ($images && is_string($images)) {
				$images = df_json_decode($images);
			}
			$images = df_nta($images);
			/**
			 * Ключ «values» содержит информацию о главной картинке товара.
			 * Значением ключа является ассоциативный массив вида:
			  {
			  		"image":"d/e/detskij-bassejn-samolety-intex-168h40-58425.jpg"
			 		, "small_image":"/u/p/upxza3qcta.jpg.tmp"
			  		, "thumbnail":"no_selection"
			  }
			 * причём этот массив может быть как закодирован в строку в формате JSON,
			 * так и быть простым ассициативным массивом PHP.
			 */
			/** @var string|array(string => mixed) $values */
			$values = dfa($value, 'values');
			/** @var bool $valuesWasEncoded */
			$valuesWasEncoded = false;
			if ($values && is_string($values)) {
				$values = df_json_decode($values);
				$valuesWasEncoded = true;
			}
			$values = df_nta($values);
			/** @var string[] $clearImages */
			$clearImages = array();
			/** @var string[] $newImages */
			$newImages = array();
			/** @var string[] $existImages */
			$existImages = array();
			/** @var string[] $duplicate */
			$duplicate = array();
			if (!$object->getIsDuplicate()) {
				foreach ($images as &$image) {
					/** @var string $file */
					$file = $image['file'];
					/** @var array(string => string|int) $image */
					if (!empty($image['removed'])) {
						$clearImages[] = $file;
					}
					else if (!isset($image['value_id'])) {
						/** @var string $newFile */
						$newFile = $this->_moveImageFromTmp($file);
						$image['new_file'] = $newFile;
						$newImages[$file] = $image;
						$this->_renamedImages[$file] = $newFile;
						$image['file'] = $newFile;
					} else {
						$existImages[$file] = $image;
					}
				}
			}
			else {
				// For duplicating we need copy original images.
				foreach ($images as &$image) {
					/** @var int|null $valueId */
					$valueId = dfa($image, 'value_id');
					if ($valueId) {
						/** @var string $file */
						$file = $image['file'];
						/** @var string $copy */
						$copy = $this->_copyImage($file);
						$duplicate[$valueId] = $copy;
						/**
						 * 	$duplicate = array(
								[1695] => d/e/detskij-bassejn-samolety-intex-168h40-58425_5.jpg
							)
						 */
						/**
						 * Устранение сбоя
						 * «Warning: Illegal string offset 'new_file'
						 * in app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Media.php».
						 */
						$newImages[$file] = array(
							'new_file' => $copy, 'label' => dfa($image, 'label')
						);
					}
				}
				$value['duplicate'] = $duplicate;
				/**
				 * $value = array(
						[images] => array(
							[0] => array(
								[value_id] => 1695
								[file] => d/e/detskij-bassejn-samolety-intex-168h40-58425.jpg
								[label] =>
								[position] => 1
								[disabled] => 0
								[label_default] =>
								[position_default] => 1
								[disabled_default] => 0
							)
						)
						[values] => array()
						[duplicate] => array(
							[1695] => d/e/detskij-bassejn-samolety-intex-168h40-58425_5.jpg
						)
					 )
				 */
			}
			foreach ($object->getMediaAttributes() as $mediaAttribute) {
				/** @var Mage_Catalog_Model_Entity_Attribute $mediaAttribute */
				/** @var string $mediaAttrCode */
				$mediaAttrCode = $mediaAttribute->getAttributeCode();
				/** @var string $labelKey */
				$labelKey = $mediaAttrCode . '_label';
				/** @var string $attrData */
				$attrData = $object->getData($mediaAttrCode);
				if (in_array($attrData, $clearImages)) {
					$object->setData($mediaAttrCode, 'no_selection');
				}
				/** @var array(string => string)|null $attrDataValue */
				$attrDataValue = dfa($newImages, $attrData);
				if ($attrDataValue) {
					/** @var string $newFile */
					$newFile = $attrDataValue['new_file'];
					$object->setData($mediaAttrCode, $newFile);
					$object->setData($labelKey, $attrDataValue['label']);
					/**
					 * При дублировании товара
					 * у товара-дублёра утрачивается информация о главной картинке.
					 * В качестве быстрого простого решения
					 * назначаем хоть какую-то (первую по списку) картинку главной.
					 */
					if ($object->getIsDuplicate() && !dfa($values, $mediaAttrCode)) {
						$values[$mediaAttrCode] = $newFile;
					}
				}
				$attrDataValue = dfa($existImages, $attrData);
				if ($attrDataValue) {
					$object->setData($labelKey, $attrDataValue['label']);
				}
			}
			$value['images'] = $images;
			$value['values'] =
				!$valuesWasEncoded
				? $values
				/**
				 * @uses Zend_Json::encode() портит нам файловые пути.
				 * Было до применения пары decode/encode:
				 * {"image":"/t/s/tsb95041.jpg.tmp","small_image":"/t/s/tsb95041.jpg.tmp","thumbnail":"/t/s/tsb95041.jpg.tmp"}
				 * Стало: {"image":"\/t\/s\/tsb95041.jpg.tmp","small_image":"\/t\/s\/tsb95041.jpg.tmp","thumbnail":"\/t\/s\/tsb95041.jpg.tmp"}
				 * http://stackoverflow.com/questions/6700719/
				 */
				: str_replace('\/', '/', Zend_Json::encode($values))
			;
			Mage::dispatchEvent('catalog_product_media_save_before', array(
				'product' => $object
				, 'images' => $value
			));
			$object->setData($attrCode, $value);
		}
		return $this;
	}

	/**
	 * Цель перекрытия —
	 * дача файлам картинок понятных описательных имён (транслитом).
	 * @override
	 * @param string $file
	 * @return string
	 */
	protected function _moveImageFromTmp($file) {
		/** @var bool $patchNeeded */
		static $patchNeeded;
		if (is_null($patchNeeded)) {
			$patchNeeded =  df_cfg()->seo()->images()->getUseDescriptiveFileNames();
		}
		return $patchNeeded ? $this->moveImageFromTmpDf($file) : parent::_moveImageFromTmp($file);
	}

	/** @return Df_Catalog_Model_Product */
	private function getProduct() {return $this->_product;}

	/**
	 * @param string $file
	 * @return string
	 */
	private function moveImageFromTmpDf($file) {
		df_param_string($file, 0);
		/** @var Varien_Io_File $ioObject */
		$ioObject = new Varien_Io_File();
		/** @var string $destDirectory */
		$destDirectory = dirname($this->_getConfig()->getMediaPath($file));
		df_assert_string_not_empty($destDirectory);
		try {
			$ioObject->open(array('path'=>$destDirectory));
		}
		catch (Exception $e) {
			try {
				$ioObject->mkdir($destDirectory, 0777, true);
				$ioObject->open(array('path'=>$destDirectory));
			}
			catch (Exception $e) {
				df_error(
					"[%method%]:\nСогласно текущему алгоритму,"
					."\nсистема должна переместить загруженную картинку в папку"
					."\n«%destionationDirectory%»"
					."\nОднако, папка «%destionationDirectory%» отсутствует на сервере,"
					."\nи система не в состоянии её вручную создать по причине:\n«%exceptionMessage%»."
					,array(
						'%method%' => __METHOD__
						,'%destionationDirectory%' => $destDirectory
						,'%exceptionMessage%»' => rm_ets($e)
					)
				);
			}
		}
		if (mb_strrpos($file, '.tmp') === mb_strlen($file)-4) {
			$file = mb_substr($file, 0, mb_strlen($file)-4);
			df_assert_string_not_empty($file);
		}
		/** @var string $destionationFilePath */
		$destionationFilePath = df_path_n($this->_getConfig()->getMediaPath($file));
		/** @var Df_Catalog_Model_Product $product */
		$product = $this->getProduct();
		df_assert($product instanceof Df_Catalog_Model_Product);
		/** @var string $destionationFilePathOptimizedForSeo */
		/**
		 * Аналогичный алгоритм:
		 * @see Df_Adminhtml_Catalog_Product_GalleryController::uploadActionDf()
		 */
		$destionationFilePathOptimizedForSeo =
			df_cc_path(
				dirname($destionationFilePath)
				,df_ccc('.'
					,df_output()->transliterate($product->getName())
					, df()->file()->getExt($destionationFilePath)
				)
			)
		;
		/** @var string $destionationFilePathOptimizedForSeoAndUnique */
		$destionationFilePathOptimizedForSeoAndUnique =
			df()->file()->getUniqueFileName($destionationFilePathOptimizedForSeo)
		;
		/** @var string $sourceFilePath */
		$sourceFilePath = $this->_getConfig()->getTmpMediaPath($file);
		if (!is_file($sourceFilePath)) {
			df_error(
				"[%method%]:\nСогласно текущему алгоритму,"
				."\nсистема должна была временно сохранить загруженную картинку по пути"
				."\n«%sourceFilePath%»"
				."\nОднако, файл «%sourceFilePath%» отсутствует на сервере."
				,array(
					'%method%' => __METHOD__
					,'%sourceFilePath%' => $sourceFilePath
				)
			);
		}
		/** @var bool $r */
		$r =
			$ioObject->mv(
				$this->_getConfig()->getTmpMediaPath($file)
				,$destionationFilePathOptimizedForSeoAndUnique
			)
		;
		df_assert_boolean($r);
		if (!$r || !is_file($destionationFilePathOptimizedForSeoAndUnique)) {
			df_error(
				"[%method%]:\nСистеме не удалось переместить файл"
				. "\nс пути «%sourceFilePath%»"
				. "\nна путь «%destionationFilePathOptimizedForSeoAndUnique%»."
				,array(
					'%method%' => __METHOD__
					,'%sourceFilePath%' => $sourceFilePath
					,'%destionationFilePathOptimizedForSeoAndUnique%' =>
						$destionationFilePathOptimizedForSeoAndUnique
				)
			);
		}
		$result =
			str_replace(
				$ioObject->dirsep()
				,/**
				 * Похоже, в качества разделителя частей пути в данном случае
				 * надо всегда использовать именно символ /
				 */
				'/'
				,str_replace(
					df_path_n($this->_getConfig()->getBaseMediaPath())
					,''
					,df_path_n($destionationFilePathOptimizedForSeoAndUnique)
				)
			)
		;
		return $result;
	}

	/**
	 * @param Df_Catalog_Model_Product $product
	 * @return void
	 */
	private function setProduct(Df_Catalog_Model_Product $product) {$this->_product = $product;}

	/** @var  Df_Catalog_Model_Product */
	private $_product;
}