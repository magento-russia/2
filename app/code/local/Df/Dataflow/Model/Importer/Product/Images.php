<?php
class Df_Dataflow_Model_Importer_Product_Images extends Df_Core_Model {
	/** @return Df_Dataflow_Model_Importer_Product_Images */
	public function process() {
		/** @var bool $needSave */
		$needSave = false;
		/** @var bool $needReload */
		$needReload = false;
		/** @var Df_Catalog_Model_Product $product */
		$product = df_product($this->getProduct()->getData());
		$product->deleteImages();
		/** @var bool $isPrimaryImage */
		$isPrimaryImage = true;
		foreach ($this->getImages() as $imagePath) {
			/** @var string $imagePath */
			try {
				$product->addImageToMediaGallery(
					$imagePath
					,$isPrimaryImage ? Df_Catalog_Model_Product::getMediaAttributeNames() : null
					,false
					,false
				);
			}
			catch (Exception $e) {
				df_notify_exception($e);
				$this->error(
					'При импорте товарного изображения для товара %s произошёл сбой: «%s».'
					,$product->getTitle()
					,df_ets($e)
				);
			}
			$isPrimaryImage = false;
			$this->log(
				'К товару %s добавлена картинка «%s».'
				,$product->getTitle()
				,df_path_relative($imagePath)
			);
			$needSave = true;
			/**
			 * Если после добавления к товару картинок не перезагрузить товар,
			 * то при повторном сохранении товара произойдёт исключительная ситуация
			 * (система будет пытаться повторно прикрепить те же самые картинки к товару).
			 * @todo Наверняка есть более правильное решение, чем перезагрузка товара.
			 */
			$needReload = true;
		}
		if ($needSave) {
			$product->saveRm($isMassUpdate = true);
			$this->log('Обновлён товар %s.', $product->getTitle());
		}
		if ($needReload) {
			$product->reload();
		}
		if ($needSave && !Mage::app()->isSingleStoreMode()) {
			/**
			 * Если система содержит другие магазины, кроме текущего
			 * (того, с которым сейчас выполняется обмен данными),
			 * то система, конечно, должна добавить товарное изображение
			 * только для текущего магазина.
			 * Однако в этом случае товарное изображение будет отсутствовать
			 * на витринной странице списка товаров:
			 * http://magento-forum.ru/topic/3783/
			 *
			 * Причиной отсутствия картиник на странице товарного изображения
			 * является недостаток (либо дефект, либо так было задумано с целью ускорения)
			 * Magento CE: если в системе несколько магазинов,
			 * и значение свойства задано только на уровне конкретного магазина,
			 * но не задано глобальное значение по умолчанию,
			 * то данное свойство не попадет в коллекцию.
			 * @see Mage_Catalog_Model_Resource_Collection_Abstract::_getLoadAttributesSelect
			 * Там используется LEFT JOIN, что и является причиной такого поведения.
			 *
			 * Хотел исправить данное поведение ядра (видимо, нужно FULL OUTER JOIN),
			 * но это показалось слишком трудоёмким.
			 * Вместо этого выбрал более простой путь:
			 * стал добавлять товарное изображение не только к текущему магазину,
			 * но и к глобальной области настроек.
			 */
			$this->addImagesToDefaultScopeIfNeeded($product);
			/**
			 * Раз уж мы добавили картинку к глобальной области настроек,
			 * то надо исключить ее показ из всех магазинов, за исключением текущего.
			 */
			$this->excludeImagesFromOtherStores($product);
		}
		df()->registry()->products()->addEntity($product);
		return $this;
	}

	/**
	 * @param Df_Catalog_Model_Product $product
	 * @return \Df\C1\Cml2\Import\Processor\Product\Part\Images
	 */
	private function addImagesToDefaultScopeIfNeeded(Df_Catalog_Model_Product $product) {
		/** @var string[] $mediaAttributes */
		$mediaAttributes =
			array(
				'small_image'
				/**
				 * http://magento-forum.ru/topic/3941/
				 */
				,'thumbnail'
			)
		;
		/** @var Df_Catalog_Model_Product $productWithDefaultValues */
		$productWithDefaultValues = $product->forStore(Mage_Core_Model_App::ADMIN_STORE_ID);
		$productWithDefaultValues->deleteImages();
		/** @var string[] $attributesToUpdate */
		$attributesToUpdate = array();
		foreach ($mediaAttributes as $mediaAttribute) {
			/** @var string $mediaAttribute */
			if (!$productWithDefaultValues->getData($mediaAttribute)) {
				$attributesToUpdate[]= $mediaAttribute;
			}
		}
		if ($attributesToUpdate) {
			foreach ($attributesToUpdate as $mediaAttribute) {
				/** @var string $mediaAttribute */
				$productWithDefaultValues->setData($mediaAttribute, $product->getData($mediaAttribute));
			}
			$productWithDefaultValues->saveRm($isMassUpdate = true);
		}
		return $this;
	}

	/**
	 * @param string $imageUrl
	 * @return string
	 */
	private function download($imageUrl) {
		/** @var string $result */
		$result = '';
		/** @var Zend_Http_Client $httpClient */
		$httpClient = new Zend_Http_Client();
		$httpClient
			->setUri($imageUrl)
			->setMethod(Zend_Http_Client::GET)
		;
		/** @var Zend_Http_Response $response */
		$response = $httpClient->request();
		/** @var string $contentType */
		$contentType = strtolower($response->getHeader('content-type'));
		df_assert_string_not_empty($contentType);
		/** @var string[] $contentTypeExploded */
		$contentTypeExploded = df_explode_xpath($contentType);
		if ('image' === df_first($contentTypeExploded)) {
			/** @var string $imageType */
			$imageType = df_last($contentTypeExploded);
			$result =
				df_cc_path(
					$this->getDownloadPath()
					, implode('.', array(md5($imageUrl), $imageType))
				)
			;
			df_file_put_contents($result, $response->getBody());
		}

		df_result_string($result);
		return $result;
	}

	/**
	 * @param string|array(string|int => string) $message
	 * @return void
	 * @throws Exception
	 */
	public function error($message) {
		/** @var array $arguments */
		$arguments = func_get_args();
		/** @var string $message */
		$message = df_format($arguments);
		$this->log($message);
		df_error($message);
	}

	/**
	 * @param Df_Catalog_Model_Product $product
	 * @return \Df\C1\Cml2\Import\Processor\Product\Part\Images
	 */
	private function excludeImagesFromOtherStores(Df_Catalog_Model_Product $product) {
		/** @var int[] $storesToExcludeFrom */
		$storesToExcludeFrom = array_diff($product->getStoreIds(), array($product->getStoreId()));
		$storesToExcludeFrom[]= Mage_Core_Model_App::ADMIN_STORE_ID;
		if ($storesToExcludeFrom) {
			/** @var string[] $imageFileNames */
			$imageFileNames = array();
			/** @var Mage_Eav_Model_Entity_Attribute_Abstract[] $attributes */
			$attributes = $product->getTypeInstance()->getSetAttributes();
			df_assert_array($attributes);
			/** @var Mage_Eav_Model_Entity_Attribute_Abstract|null $mediaGalleryAttribute */
			$mediaGalleryAttribute = dfa($attributes, Df_Catalog_Model_Product::P__MEDIA_GALLERY);
			if (!is_null($mediaGalleryAttribute)) {
				df_assert($mediaGalleryAttribute instanceof Mage_Eav_Model_Entity_Attribute_Abstract);
				/** @var Mage_Catalog_Model_Product_Attribute_Backend_Media $backend */
				$backend = $mediaGalleryAttribute->getBackend();
				df_assert($backend instanceof Mage_Catalog_Model_Product_Attribute_Backend_Media);
				if (is_array($product->getMediaGallery())) {
					$product->getMediaGalleryImages();
					/** @var array|null $images */
					$images = dfa($product->getMediaGallery(), 'images');
					if (is_array($images)) {
						foreach ($images as $image){
							/** @var string|null $fileName */
							$fileName = dfa($image, 'file');
							if ($fileName) {
								$imageFileNames[]= $fileName;
							}
						}
					}
				}
				if ($imageFileNames) {
					foreach ($storesToExcludeFrom as $storeToExcludeFrom) {
						/** @var int $storeToExcludeFrom */
						/** @var $productWithScopedValues $productWithDefaultValues */
						$productWithScopedValues = $product->forStore($storeToExcludeFrom);
						foreach ($imageFileNames as $imageFileName) {
							/** @noinspection PhpParamsInspection */
							$backend->updateImage(
								$productWithScopedValues
								, $imageFileName
								, array('exclude' => 1)
							);
						}
						$productWithScopedValues->saveRm($isMassUpdate = true);
					}
				}
			}
		}
		return $this;
	}
	
	/** @return string */
	private function getDownloadPath() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_cc_path(Mage::getBaseDir('var'), 'rm', 'dataflow', 'images');
			/** @var Varien_Io_File $file */
			$file = new Varien_Io_File();
			$file->setAllowCreateFolders(true);
			$file->createDestinationDir($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return string[] */
	private function getImages() {
		if (!isset($this->{__METHOD__})) {
			/** @var string[] $result  */
			$result = array();
			foreach ($this->cfg(self::P__IMAGES) as $image) {
				/** @var string $image */
				if (df_check_url($image)) {
					$image = $this->download($image);
				}
				$result[]= $image;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Dataflow_Logger|null */
	private function getLogger() {return $this->cfg(self::P__LOGGER);}

	/** @return Df_Catalog_Model_Product */
	private function getProduct() {return $this->cfg(self::P__PRODUCT);}

	/**
	 * @param string|array(string|int => string) $message
	 * @return Df_Dataflow_Model_Importer_Product_Images
	 */
	public function log($message) {
		if (is_object($this->getLogger())) {
			/** @var mixed[] $arguments */
			$arguments = func_get_args();
			$this->getLogger()->log(df_format($arguments));
		}
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__IMAGES, DF_V_ARRAY)
			// Раньше вместо 'Df_Dataflow_Logger' использовалось Df_Dataflow_Logger::class,
			// однако это привело к сбою:
			// «Fatal error: Cannot inherit previously-inherited or override constant _CLASS
			// from interface Df_Dataflow_Logger in app/code/local/Df/1C/Helper/Data.php on line 2»
			->_prop(self::P__LOGGER, 'Df_Dataflow_Logger', false)
			->_prop(self::P__PRODUCT, Df_Catalog_Model_Product::class)
		;
	}

	const P__IMAGES = 'images';
	const P__LOGGER = 'logger';
	const P__PRODUCT = 'product';
	/**
	 * @static
	 * @param Df_Catalog_Model_Product $product
	 * @param string[] $images
	 * @param Df_Dataflow_Logger|null $logger [optional]
	 * @return Df_Dataflow_Model_Importer_Product_Images
	 */
	public static function i(
		Df_Catalog_Model_Product $product, array $images, Df_Dataflow_Logger $logger = null
	) {
		return new self(array(
			self::P__PRODUCT => $product, self::P__IMAGES => $images, self::P__LOGGER => $logger
		));
	}
}