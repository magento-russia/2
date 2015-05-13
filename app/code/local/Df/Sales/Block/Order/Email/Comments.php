<?php
class Df_Sales_Block_Order_Email_Comments extends Df_Core_Block_Template_NoCache {
	/**
	 * @override
	 * @return string
	 */
	public function getArea() {return Df_Core_Const_Design_Area::FRONTEND;}

	/** @return string */
	public function getComments() {return $this->cfg(self::P__COMMENTS, '');}

	/**
	 * @override
	 * @return string|null
	 */
	protected function getDefaultTemplate() {return 'df/sales/order/email/comments.phtml';}
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
		$this->_prop(self::P__COMMENTS, self::V_STRING_NE, false);
	}
	const P__COMMENTS = 'comments';
	/**
	 * @param string $comments
	 * @return Df_Sales_Block_Order_Email_Comments
	 */
	public static function i($comments) {
		return df_block(new self(array(self::P__COMMENTS => $comments)));
	}
}