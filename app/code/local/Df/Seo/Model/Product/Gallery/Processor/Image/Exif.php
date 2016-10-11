<?php
class Df_Seo_Model_Product_Gallery_Processor_Image_Exif extends Df_Core_Model_Abstract {
	/** @return Df_Seo_Model_Product_Gallery_Processor_Image_Exif */
	public function process() {
		if ($this->isEligibleForExif ()) {
			if (
					file_exists($this->getImagePath())
				&&
					$this->getProduct()
			) {
				/**
				 * Этот вызов неявно (автоматически) инициализирует библиотеку Pel.
				 * @uses Df_Pel_Lib::s()
				 */
				Df_Pel_Lib::s()->setCompatibleErrorReporting();
				try {
					$data =
						new PelDataWindow (
							file_get_contents(
								$this->getImagePath()
							)
						)
					;
					/** @var bool $imageIsValid */
					$imageIsValid = false;
					try {
						/**
						 * При вызове PelJpeg::isValid библиотека Pel может давать сбой:
						 * «Offset 0 not within[0, -1]»
						 */
						$imageIsValid = PelJpeg::isValid($data);
					}
					catch(Exception $e) {
					}
					if ($imageIsValid) {
						$jpeg = new PelJpeg();
						try {
							$jpeg->load($data);
						}
						catch(Exception $e) {
							/**
							 * При вызове PelJpeg::load()
							 * тоже может произойти исключительная ситуация
							 * «Offset -996 not within[0, 1009]».
							 *
							 * В случае её возникновения
							 * просто оставляем попытки добавления EXIF к данной картинке.
							 */
							$imageIsValid = false;
						}
						if ($imageIsValid) {
							$exif = $jpeg->getExif ();
							if (is_null($exif)) {
								$exif = new PelExif ();
								$jpeg->setExif ($exif);
								$tiff = new PelTiff();
								$exif->setTiff($tiff);
							}

							$tiff = $exif->getTiff();
							$ifd0 = $tiff->getIfd();
							if (is_null($ifd0)) {
								$ifd0 = new PelIfd(PelIfd::IFD0);
								$tiff->setIfd($ifd0);
							}

							$ifdExif = new PelIfd(PelIfd::EXIF);
							$ifd0->addSubIfd($ifdExif);
							$description =
								html_entity_decode(
									strip_tags(
										$this->getProduct()->getDescription()
									)
								)
							;
							$title = $this->getProduct()->getName();
							//$author = Mage::getStoreConfig('design/head/default_title');
							$copyright = Mage::getStoreConfig('design/footer/copyright');
							$keywords = $this->getProduct()->getMetaKeyword();
							$categoryIds = $this->getProduct()->getCategoryIds();
							$subject =
								empty($categoryIds)
								? $title
								: Df_Catalog_Model_Category::ld(df_a($categoryIds, 0))->getName()
							;
							$ifd0->addEntry(new PelEntryAscii(PelTag::IMAGE_DESCRIPTION, $title));
							//$ifd0->addEntry(new PelEntryAscii(PelTag::MAKE, "MAKE"));
							$ifd0->addEntry(
								new PelEntryCopyright(
									strtr(
										$copyright
										,array(
											"&copy;" => "(c)"
											,"©" => "(c)"
											,'{currentYear}' =>
												df_dts(Zend_Date::now(), Zend_Date::YEAR)
										)
									)
								)
							);
							$ifd0
								->addEntry(
									new PelEntryWindowsString (
										PelTag::XP_KEYWORDS
										,$keywords
									)
								)
							;
							$ifd0
								->addEntry(
									new PelEntryWindowsString (
										PelTag::XP_SUBJECT
										,$subject
									)
								)
							;
							$ifd0
								->addEntry(
									new PelEntryWindowsString (
										PelTag::XP_AUTHOR
										,Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB)
									)
								)
							;
							$ifdExif
								->addEntry(
									new PelEntryUserComment(
										df_text()->convertUtf8ToWindows1251($description)
									)
								)
							;
	//						$ifdExif
	//							->addEntry(
	//								new PelEntryTime (
	//									PelTag::DATE_TIME_ORIGINAL
	//									,
	//									time() - 7 * 24 * 3600
	//								)
	//							)
	//						;
							$ifdExif
								->addEntry(
									new PelEntryVersion(PelTag::EXIF_VERSION, 2.2)
								)
							;
							$ifdInterop= new PelIfd(PelIfd:: INTEROPERABILITY);
							$ifd0->addSubIfd($ifdInterop);
							rm_file_put_contents($this->getImagePath(), $jpeg->getBytes());
						}
					}
					Df_Pel_Lib::s()->restoreErrorReporting();
				}
				catch(Exception $e) {
					Df_Pel_Lib::s()->restoreErrorReporting();
					df_handle_entry_point_exception($e, false);
				}
			}
		}
		return $this;
	}

	/** @return string */
	private function getImagePath() {
		return $this->cfg(self::P__IMAGE_PATH);
	}

	/** @return Mage_Catalog_Model_Product|null */
	private function getProduct() {
		return $this->cfg(self::P__PRODUCT);
	}

	/**
	* @return bool
	*/
	private function isEligibleForExif () {
		return
			in_array(
				strtolower(
					df_a(pathinfo($this->getImagePath()), 'extension')
				)
				,// Today, we supports only JPEG images...
				array("jpg", "jpeg")
			)
		;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__IMAGE_PATH, self::V_STRING_NE)
			->_prop(self::P__PRODUCT, Df_Catalog_Model_Product::_CLASS, false)
		;
	}
	const _CLASS = __CLASS__;
	const P__IMAGE_PATH = 'imagePath';
	const P__PRODUCT = 'product';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Seo_Model_Product_Gallery_Processor_Image_Exif
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}