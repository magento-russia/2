<?php
class Df_Avangard_Model_RequestDocument extends Df_Core_Model_SimpleXml_Generator_Document {
	/**
	 * @override
	 * @return array(string => mixed)
	 */
	protected function getContentsAsArray() {return $this->cfg(self::P__REQUEST_PARAMS);}

	/**
	 * @override
	 * @return string
	 */
	protected function getTagName() {return $this->cfg(self::P__TAG_NAME);}

	/**
	 * Документы предназначены не для людей, а для платёжного шлюза,
	 * а вот надёжность важна: @link http://magento-forum.ru/topic/4300/
	 * Поэтому пусть уж система обрабляет все данные в CDATA.
	 * @override
	 * @return bool
	 */
	protected function needWrapInCDataAll() {return true;}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__REQUEST_PARAMS, self::V_ARRAY)
			->_prop(self::P__TAG_NAME, self::V_STRING_NE)
		;
	}
	const _CLASS = __CLASS__;
	const P__REQUEST_PARAMS = 'request_params';
	const P__TAG_NAME = 'tag_name';
	const TAG__REGISTRATION = 'new_order';
	const TAG__REFUND = 'reverse_order';
	const TAG__STATE = 'get_order_info';
	/**
	 * @static
	 * @param mixed[] $requestParameters
	 * @param string $tagName
	 * @return Df_Avangard_Model_RequestDocument
	 */
	public static function i(array $requestParameters, $tagName) {
		return new self(array(
			self::P__REQUEST_PARAMS => $requestParameters, self::P__TAG_NAME => $tagName
		));
	}
	/**
	 * @static
	 * @param mixed[] $requestParameters
	 * @return Df_Avangard_Model_RequestDocument
	 */
	public static function registration(array $requestParameters) {
		return self::i($requestParameters, self::TAG__REGISTRATION);
	}
	/**
	 * @static
	 * @param mixed[] $requestParameters
	 * @return Df_Avangard_Model_RequestDocument
	 */
	public static function refund(array $requestParameters) {
		return self::i($requestParameters, self::TAG__REFUND);
	}
	/**
	 * @static
	 * @param mixed[] $requestParameters
	 * @return Df_Avangard_Model_RequestDocument
	 */
	public static function state(array $requestParameters) {
		return self::i($requestParameters, self::TAG__STATE);
	}
}