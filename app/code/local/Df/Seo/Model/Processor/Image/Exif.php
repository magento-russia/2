<?php
class Df_Seo_Model_Processor_Image_Exif extends Df_Core_Model {
	/** @return void */
	private function addTag_author() {
		$this->getPelIfd0()->addEntry(new PelEntryWindowsString(
			PelTag::XP_AUTHOR, Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB)
		));
	}

	/** @return void */
	private function addTag_copyright() {
		/** @var string|null $copyright */
		$copyright = Mage::getStoreConfig('design/footer/copyright');
		if ($copyright) {
			$this->getPelIfd0()->addEntry(new PelEntryCopyright(strtr($copyright, array(
				"&copy;" => "(c)"
				,"©" => "(c)"
				,'{currentYear}' => df_dts(Zend_Date::now(), Zend_Date::YEAR)
			))));
		}
	}

	/** @return void */
	private function addTag_imageDescription() {
		$this->getPelIfd0()->addEntry(new PelEntryAscii(
			PelTag::IMAGE_DESCRIPTION, $this->getProduct()->getName()
		));
	}

	/** @return void */
	private function addTag_keywords() {
		/** @var string|null $keywords */
		$keywords = $this->getProduct()->getMetaKeyword();
		if ($keywords) {
			$this->getPelIfd0()->addEntry(new PelEntryWindowsString(PelTag::XP_KEYWORDS, $keywords));
		}
	}

	/** @return void */
	private function addTag_subject() {
		$this->getPelIfd0()->addEntry(new PelEntryWindowsString(PelTag::XP_SUBJECT,
			$this->getProduct()->getCategoryMain()
			? $this->getProduct()->getCategoryMain()->getName()
			: $this->getProduct()->getName()
		));
	}

	/** @return void */
	private function addTag_userComment() {
		$this->getPelIfdExif()->addEntry(
			new PelEntryUserComment(df_1251_to(html_entity_decode(strip_tags(
				$this->getProduct()->getDescription()
			))))
		);
	}

	/** @return string */
	private function getImageExtension() {return df()->file()->getExt($this->getImagePath());}

	/** @return string */
	private function getImagePath() {return $this->cfg(self::$P__IMAGE_PATH);}

	/** @return PelDataWindow|null */
	private function getPelDataWindow() {
		if (!isset($this->{__METHOD__})) {
			/** @var PelDataWindow|null $result */
			$result = null;
			if ($this->isJpeg() && file_exists($this->getImagePath())) {
				/** @var PelDataWindow $pelDataWindow */
				$result = new PelDataWindow(file_get_contents($this->getImagePath()));
				/** вызов @see PelJpeg::isValid() может давать сбой: «Offset 0 not within[0, -1]» */
				try {
					if (!PelJpeg::isValid($result)) {
						$result = null;
					}
				}
				catch (Exception $e) {
					$result = null;
				}
			}
			$this->{__METHOD__} = df_n_set($result);
		}
		return df_n_get($this->{__METHOD__});
	}

	/** @return PelExif */
	private function getPelExif() {
		if (!isset($this->{__METHOD__})) {
			/** @var PelExif $result */
			$result = $this->getPelJpeg()->getExif();
			if (!$result) {
				$result = new PelExif();
				$this->getPelJpeg()->setExif($result);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return PelIfd */
	private function getPelIfd0() {
		if (!isset($this->{__METHOD__})) {
			/** @var PelIfd $result */
			$result = $this->getPelTiff()->getIfd();
			if (!$result) {
				$result = new PelIfd(PelIfd::IFD0);
				$this->getPelTiff()->setIfd($result);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return PelIfd */
	private function getPelIfdExif() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = new PelIfd(PelIfd::EXIF);
			$this->getPelIfd0()->addSubIfd($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return PelJpeg|null */
	private function getPelJpeg() {
		if (!isset($this->{__METHOD__})) {
			/** @var PelJpeg|null $result */
			$result = null;
			if ($this->getPelDataWindow()) {
				$result = new PelJpeg();
				/** при вызове @see PelJpeg::load() может произойти сбой: «Offset -996 not within[0, 1009]» */
				try {
					$result->load($this->getPelDataWindow());
				}
				catch (Exception $e) {
					$result = null;
				}
			}
			$this->{__METHOD__} = df_n_set($result);
		}
		return df_n_get($this->{__METHOD__});
	}

	/** @return PelTiff */
	private function getPelTiff() {
		if (!isset($this->{__METHOD__})) {
			/** @var PelTiff $result */
			$result = $this->getPelExif()->getTiff();
			if (!$result) {
				$result = new PelTiff();
				$this->getPelExif()->setTiff($result);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Catalog_Model_Product */
	private function getProduct() {return $this->cfg(self::$P__PRODUCT);}

	/** @rturn bool */
	private function isJpeg() {
		return in_array(mb_strtolower($this->getImageExtension()), array('jpg', 'jpeg'));
	}

	/** @return void */
	private function process() {
		/**
		 * Этот вызов неявно (автоматически) инициализирует библиотеку Pel.
		 * @uses Df_Pel_Lib::s()
		 */
		Df_Pel_Lib::s()->setCompatibleErrorReporting();
		try {
			if ($this->getPelJpeg()) {
				$this->addTag_imageDescription();
				$this->addTag_copyright();
				$this->addTag_keywords();
				$this->addTag_subject();
				$this->addTag_author();
				$this->addTag_userComment();
				$this->getPelIfdExif()->addEntry(new PelEntryVersion(PelTag::EXIF_VERSION, 2.2));
				/** @var PelIfd $ifdInterop */
				$ifdInterop = new PelIfd(PelIfd::INTEROPERABILITY);
				$this->getPelIfd0()->addSubIfd($ifdInterop);
				df_file_put_contents($this->getImagePath(), $this->getPelJpeg()->getBytes());
			}
			Df_Pel_Lib::s()->restoreErrorReporting();
		}
		catch (Exception $e) {
			Df_Pel_Lib::s()->restoreErrorReporting();
			df_handle_entry_point_exception($e, false);
		}
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__IMAGE_PATH, DF_V_STRING_NE)
			->_prop(self::$P__PRODUCT, Df_Catalog_Model_Product::_C)
		;
	}
	/** @var string */
	private static $P__IMAGE_PATH = 'imagePath';
	/** @var string */
	private static $P__PRODUCT = 'product';
	/**
	 * @static
	 * @param string $imagePath
	 * @param Df_Catalog_Model_Product $product
	 * @return void
	 */
	public static function p($imagePath, Df_Catalog_Model_Product $product) {
		/** @var Df_Seo_Model_Processor_Image_Exif $processor */
		$processor = new self(array(self::$P__IMAGE_PATH => $imagePath, self::$P__PRODUCT => $product));
		$processor->process();
	}
}