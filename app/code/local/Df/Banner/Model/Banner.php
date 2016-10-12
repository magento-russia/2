<?php
/**
 * @method Df_Banner_Model_Resource_Banner getResource()
 */
class Df_Banner_Model_Banner extends Df_Core_Model {
	/** @return int */
	public function getDelay() {return $this->cfg(self::P__DELAY);}
	/** @return int */
	public function getSizeHeight() {return $this->cfg(self::P__SIZE__HEIGHT);}
	/** @return int */
	public function getSizeWidth() {return $this->cfg(self::P__SIZE__WIDTH);}
	/** @return string */
	public function getTitle() {return $this->cfg(self::P__TITLE);}
	/** @return bool */
	public function isEnabled() {return $this->cfg(self::P__IS_ENABLED);}
	/** @return bool */
	public function needShowTitle() {
		/**
		 * Для свойства show_title
		 * приходится делать такое идиотское преобразование целого значения в логическое,
		 * потому что административная часть запрограммирована по-дурному,
		 * и там значению «нет» соответствует код «2», а не «0»,
		 * при том, что допустимых значений всего 2: «да» и «нет».
		 * @see Df_Banner_Block_Adminhtml_Banner_Edit_Tab_Form::_prepareForm()
		 */
		return (1 === $this->cfg(self::P__NEED_SHOW_TITLE));
	}
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Banner_Model_Resource_Banner::mf());
		$this
			->_prop(self::P__DELAY, self::V_NAT0)
			->_prop(self::P__IS_ENABLED, self::V_BOOL)
			/**
			 * Для свойства show_title
			 * приходится использовать валидатор/фильтр V_INT, а не V_BOOL,
			 * потому что административная часть запрограммирована по-дурному,
			 * и там значению «нет» соответствует код «2», а не «0»,
			 * при том, что допустимых значений всего 2: «да» и «нет».
			 * Идиотизм.
			 * @see Df_Banner_Block_Adminhtml_Banner_Edit_Tab_Form::_prepareForm()
			 */
			->_prop(self::P__NEED_SHOW_TITLE, self::V_INT)
			->_prop(self::P__SIZE__HEIGHT, self::V_NAT)
			->_prop(self::P__SIZE__WIDTH, self::V_NAT)
		;
	}
	const _CLASS = __CLASS__;
	const P__DELAY = 'delay';
	const P__ID = 'banner_id';
	const P__IS_ENABLED = 'status';
	const P__NEED_SHOW_TITLE = 'show_title';
	const P__SIZE__HEIGHT = 'height';
	const P__SIZE__WIDTH = 'width';
	const P__TITLE = 'title';

	/** @return Df_Banner_Model_Resource_Banner_Collection */
	public static function c() {return self::s()->getCollection();}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Banner_Model_Banner
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_Banner_Model_Banner
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}
	/**
	 * @see Df_Banner_Model_Resource_Banner_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
	/** @return Df_Banner_Model_Banner */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}