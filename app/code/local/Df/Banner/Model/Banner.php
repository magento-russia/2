<?php
/**
 * @method Df_Banner_Model_Resource_Banner getResource()
 */
class Df_Banner_Model_Banner extends Df_Core_Model {
	/** @return int */
	public function getDelay() {return $this->cfg(self::$P__DELAY);}
	/**
	 * @override
	 * @return Df_Banner_Model_Resource_Banner_Collection
	 */
	public function getResourceCollection() {return self::c();}
	/** @return int */
	public function getSizeHeight() {return $this->cfg(self::$P__SIZE__HEIGHT);}
	/** @return int */
	public function getSizeWidth() {return $this->cfg(self::$P__SIZE__WIDTH);}
	/** @return string */
	public function getTitle() {return $this->cfg(self::$P__TITLE);}
	/** @return bool */
	public function isEnabled() {return $this->cfg(self::$P__IS_ENABLED);}
	/**
	 * Для свойства «show_title»
	 * приходится делать такое идиотское преобразование целого значения в логическое,
	 * потому что административная часть запрограммирована по-дурному,
	 * и там значению «нет» соответствует код «2», а не «0»,
	 * при том, что допустимых значений всего 2: «да» и «нет».
	 * @see Df_Banner_Block_Adminhtml_Banner_Edit_Tab_Form::_prepareForm()
	 * @see Df_Banner_Model_Status::yesNo()
	 * @return bool
	 */
	public function needShowTitle() {return (1 === $this->cfg(self::$P__NEED_SHOW_TITLE));}

	/**
	 * @override
	 * @return Df_Banner_Model_Resource_Banner
	 */
	protected function _getResource() {return Df_Banner_Model_Resource_Banner::s();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__DELAY, DF_V_NAT0)
			->_prop(self::$P__IS_ENABLED, DF_V_BOOL)
			/**
			 * Для свойства show_title
			 * приходится использовать валидатор/фильтр V_INT, а не V_BOOL,
			 * потому что административная часть запрограммирована по-дурному,
			 * и там значению «нет» соответствует код «2», а не «0»,
			 * при том, что допустимых значений всего 2: «да» и «нет».
			 * Идиотизм.
			 * @see Df_Banner_Block_Adminhtml_Banner_Edit_Tab_Form::_prepareForm()
			 * @see Df_Banner_Model_Status::yesNo()
			 */
			->_prop(self::$P__NEED_SHOW_TITLE, DF_V_INT)
			->_prop(self::$P__SIZE__HEIGHT, DF_V_NAT)
			->_prop(self::$P__SIZE__WIDTH, DF_V_NAT)
		;
	}
	/** @used-by Df_Banner_Model_Resource_Banner_Collection::_construct() */

	/** @used-by Df_Banner_Model_Resource_Banner::_construct() */
	const P__ID = 'banner_id';
	/** @var string */
	private static $P__DELAY = 'delay';
	/** @var string */
	private static $P__IS_ENABLED = 'status';
	/** @var string */
	private static $P__NEED_SHOW_TITLE = 'show_title';
	/** @var string */
	private static $P__SIZE__HEIGHT = 'height';
	/** @var string */
	private static $P__SIZE__WIDTH = 'width';
	/** @var string */
	private static $P__TITLE = 'title';

	/**
	 * @used-by Df_Banner_Block_Adminhtml_Banner_Grid::_prepareCollection()
	 * @used-by Df_Banner_Block_Adminhtml_Banneritem_Grid::_prepareColumns()
	 * @used-by Df_Banner_Block_Adminhtml_Banneritem_Edit_Tab_Form::_prepareForm()
	 * @return Df_Banner_Model_Resource_Banner_Collection
	 */
	public static function c() {return new Df_Banner_Model_Resource_Banner_Collection;}
	/**
	 * @used-by Df_Banner_Adminhtml_BannerController::deleteAction()
	 * @used-by Df_Banner_Adminhtml_BannerController::editAction()
	 * @used-by Df_Banner_Adminhtml_BannerController::saveAction
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Banner_Model_Banner
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @used-by Df_Banner_Block_Banner::getBanner()
	 * @used-by Df_Banner_Adminhtml_BannerController::massDeleteAction()
	 * @used-by Df_Banner_Adminhtml_BannerController::massStatusAction()
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_Banner_Model_Banner
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}
}