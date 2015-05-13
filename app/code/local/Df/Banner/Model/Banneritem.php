<?php
/**
 * @method Df_Banner_Model_Resource_Banneritem getResource()
 */
class Df_Banner_Model_Banneritem extends Df_Core_Model_Abstract {
	/** @return string */
	public function getContent() {
		return $this->cfg(self::P__CONTENT, '');
	}

	/** @return string|null */
	public function getImageFileName() {
		/** @var string|null $result */
		$result = $this->cfg(self::P__IMAGE__FILE_NAME);
		if (!is_null($result)) {
			df_result_string($result);
		}
		return $result;
	}

	/** @return string|null */
	public function getImageUrl() {
		/** @var string|null $result */
		$result = $this->cfg(self::P__IMAGE__URL);
		if (!is_null($result)) {
			df_result_string($result);
		}
		return $result;
	}

	/** @return string */
	public function getTitle() {
		/** @var string $result */
		$result = $this->cfg(self::P__TITLE);
		df_result_string($result);
		return $result;
	}

	/** @return string|null */
	public function getThumbnailFileName() {
		/** @var string|null $result */
		$result = $this->cfg(self::P__THUMBNAIL__FILE_NAME);
		if (!is_null($result)) {
			df_result_string($result);
		}
		return $result;
	}

	/** @return string|null */
	public function getThumbnailUrl() {
		/** @var string|null $result */
		$result = $this->cfg(self::P__THUMBNAIL__URL);
		if (!is_null($result)) {
			df_result_string($result);
		}
		return $result;
	}

	/** @return string */
	public function getUrl() {
		/** @var string $result */
		$result = $this->cfg(self::P__URL);
		df_result_string($result);
		return $result;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Banner_Model_Resource_Banneritem::mf());
	}

	const _CLASS = __CLASS__;
	const P__CONTENT = 'content';
	const P__ID = 'banner_item_id';
	const P__IMAGE__FILE_NAME = 'image';
	const P__IMAGE__URL = 'image_url';
	const P__TITLE = 'title';
	const P__THUMBNAIL__FILE_NAME = 'thumb_image';
	const P__THUMBNAIL__URL = 'thumb_image_url';
	const P__URL = 'link_url';

	/** @return Df_Banner_Model_Resource_Banneritem_Collection */
	public static function c() {return self::s()->getCollection();}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Banner_Model_Banneritem
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_Banner_Model_Banneritem
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}
	/**
	 * @see Df_Banner_Model_Resource_Banneritem_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
	/** @return Df_Banner_Model_Banneritem */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}