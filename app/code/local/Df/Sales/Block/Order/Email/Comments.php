<?php
class Df_Sales_Block_Order_Email_Comments extends Df_Core_Block_Template_NoCache {
	/**
	 * @override
	 * @return string
	 */
	public function getArea() {return Df_Core_Const_Design_Area::FRONTEND;}

	/** @return string */
	public function getComments() {return $this[self::$P__COMMENTS];}

	/**
	 * @override
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/sales/order/email/comments.phtml';}

	/**
	 * @override
	 * @return bool
	 */
	protected function needToShow() {return !!$this->getComments();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__COMMENTS, RM_V_STRING_NE, false);
	}
	/** @var string */
	private static $P__COMMENTS = 'comments';

	/**
	 * @used-by Df_Sales_Model_Order::getEmailCustomerNote()
	 * @param string $comments
	 * @return string
	 */
	public static function r($comments) {
		return rm_render(new self(array(self::$P__COMMENTS => $comments)));
	}
}