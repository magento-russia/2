<?php
class Df_Catalog_Helper_Image extends Mage_Catalog_Helper_Image {
	/**
	 * Цель перекрытия —
	 * запись в системный журнал сообщения об исключительной ситуации (если она произойдёт).
	 * @override
	 * @return string
	 */
	public function __toString() {
		/** @var string $result */
		try {
			/** @var Mage_Catalog_Model_Product_Image $model */
			$model = $this->_getModel();
			if ($this->getImageFile()) {
				$model->setBaseFile($this->getImageFile());
			}
			else {
				$model->setBaseFile($this->getProduct()->getData($model->getDestinationSubdir()));
			}
			if ($model->isCached()) {
				$result = $model->getUrl();
			}
			else {
				if ($this->_scheduleRotate) {
					$model->rotate($this->getAngle());
				}
				if ($this->_scheduleResize) {
					$model->resize();
				}
				if ($this->getWatermark()) {
					$model->setWatermark($this->getWatermark());
				}
				$result = $model->saveFile()->getUrl();
			}
		}
		catch (Exception $e) {
			Mage::logException($e);
			$result = Mage::getDesign()->getSkinUrl($this->getPlaceholder());
		}
		return $result;
	}

	/**
	 * Цель перекрытия —
	 * устранение сбоя
	 * «Warning: imagecopymerge() expects parameter 9 to be long, string given
	 * in lib/Varien/Image/Adapter/Gd2.php on line 472».
	 * http://magento-forum.ru/topic/4581/
	 *
	 * Если администратор не указал степень прозрачности водяного знака,
	 * то в качестве $imageOpacity сюда из метода
	 * @used-by Mage_Catalog_Helper_Image::init()
			$this->setWatermarkImageOpacity(
				Mage::getStoreConfig(
	  				"design/watermark/{$this->_getModel()->getDestinationSubdir()}_imageOpacity"
	 			)
			);
	 * попадает не целое число, а пустая строка,
	 * что в дальнейшем приводит к указанному выше сбою
	 *
	 * @override
	 * @param int|string $imageOpacity
	 * @return Df_Catalog_Helper_Image
	 */
	public function setWatermarkImageOpacity($imageOpacity) {
		if (is_string($imageOpacity)) {
			$imageOpacity =
				('' == df_trim($imageOpacity))
				? 30
				: (int)($imageOpacity)
			;
		}
		parent::setWatermarkImageOpacity($imageOpacity);
		return $this;
	}

	/**
	 * Цель перекрытия —
	 * запоминание товара для картинки,
	 * чтобы в дальнейшем, при сохранении картинки,
	 * добавить к ней при необходимости информацию EXIF
	 * на основе информации товара.
	 * @override
	 * @param Df_Catalog_Model_Product $product
	 * @return Df_Catalog_Helper_Image
	 */
	protected function setProduct(
		/**
		 * Мы не можем явно указать тип параметра $product,
		 * потому что иначе интерпретатор сделает нам замечание:
		 * «Strict Notice: Declaration of Df_Catalog_Helper_Image::setProduct()
		 * should be compatible with that of Mage_Catalog_Helper_Image::setProduct()»
		 */
		$product
	) {
		parent::setProduct($product);
		$model = $this->_getModel();
		if ($model instanceof Df_Catalog_Model_Product_Image) {
			/** @var Df_Catalog_Model_Product_Image $model */
			$model->setProductDf($product);
		}
		return $this;
	}
}