<?php
class Df_Avangard_Model_RequestDocument extends \Df\Xml\Generator\Document {
	/**
	 * @override
	 * @return array(string => mixed)
	 */
	protected function getContentsAsArray() {return $this->cfg(self::$P__REQUEST_PARAMS);}

	/**
	 * @override
	 * @return string
	 */
	protected function tag() {return $this->cfg(self::$P__TAG_NAME);}

	/**
	 * Документы предназначены не для людей, а для платёжного шлюза,
	 * а вот надёжность важна: http://magento-forum.ru/topic/4300/
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
			->_prop(self::$P__REQUEST_PARAMS, DF_V_ARRAY)
			->_prop(self::$P__TAG_NAME, DF_V_STRING_NE)
		;
	}
	/** @var string */
	private static $P__REQUEST_PARAMS = 'request_params';
	/** @var string */
	private static $P__TAG_NAME = 'tag_name';

	/**
	 * @used-by Df_Avangard_Model_Request_Secondary::getRequestDocument()
	 * @param mixed[] $requestParameters
	 * @param string $tagName
	 * @return Df_Avangard_Model_RequestDocument
	 */
	public static function i(array $requestParameters, $tagName) {
		return new self(array(
			self::$P__REQUEST_PARAMS => $requestParameters, self::$P__TAG_NAME => $tagName
		));
	}
	/**
	 * @used-by Df_Avangard_Model_Request_Payment::getRequestDocument()
	 * @param mixed[] $requestParameters
	 * @return Df_Avangard_Model_RequestDocument
	 */
	public static function registration(array $requestParameters) {
		return self::i($requestParameters, 'new_order');
	}
}