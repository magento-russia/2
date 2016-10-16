<?php
/**
 * @method string|null getContent()
 * @method string|null getCreatedTime()
 * @method string|null getImageFileName()
 * @method string|null getImageUrl()
 * @method Df_Banner_Model_Resource_Banneritem getResource()
 * @method string|null getThumbnailFileName()
 * @method string|null getThumbnailUrl()
 * @method string|null getTitle()
 * @method string|null getUpdateTime()
 * @method string|null getUrl()
 * @method $this setCreatedTime(string $value)
 * @method $this setIsMassupdate(bool $value)
 * @method $this setStatus(int $value)
 * @method $this setUpdateTime(string $value)
 */
class Df_Banner_Model_Banneritem extends Df_Core_Model {
	/**
	 * @override
	 * @return Df_Banner_Model_Resource_Banneritem_Collection
	 */
	public function getResourceCollection() {return self::c();}

	/**
	 * @override
	 * @return Df_Banner_Model_Resource_Banneritem
	 */
	protected function _getResource() {return Df_Banner_Model_Resource_Banneritem::s();}

	/** @used-by Df_Banner_Model_Resource_Banneritem_Collection::_construct() */

	/** @used-by Df_Banner_Model_Resource_Banneritem::_construct() */
	const P__ID = 'banner_item_id';

	/**
	 * @used-by getResourceCollection()
	 * @used-by Df_Banner_Block_Banner::getBannerItems()
	 * @used-by Df_Banner_Block_Adminhtml_Banneritem_Grid::_prepareCollection()
	 * @return Df_Banner_Model_Resource_Banneritem_Collection
	 */
	public static function c() {return new Df_Banner_Model_Resource_Banneritem_Collection;}
	/**
	 * @used-by Df_Banner_Adminhtml_BanneritemController::deleteAction()
	 * @used-by Df_Banner_Adminhtml_BanneritemController::editAction()
	 * @used-by Df_Banner_Adminhtml_BanneritemController::saveAction()
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Banner_Model_Banneritem
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @used-by Df_Banner_Adminhtml_BanneritemController::massDeleteAction()
	 * @used-by Df_Banner_Adminhtml_BanneritemController::massStatusAction()
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_Banner_Model_Banneritem
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}
}