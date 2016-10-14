<?php
/**
 * @method Df_Downloadable_Model_Link setProduct(Df_Catalog_Model_Product $product)
 */
class Df_Downloadable_Model_Link extends Mage_Downloadable_Model_Link {
	/**
	 * @override
	 * @return Df_Downloadable_Model_Resource_Link_Collection
	 */
	public function getResourceCollection() {return self::c();}

	/**
	 * 2015-01-28
	 * Оказалось, что в этом методе нет пока большого толку,
	 * потому что при обращении по данному адресу веб-сервер возвращает ответ «403 Forbidden»,
	 * потому что в папке media/downloadable лежит файл .htaccess с правилами:
	 	Order deny,allow
	 	Deny from all
	 * Чтобы метод имел практическое применение — надо править этот файл.
	 * @return string
	 */
	public function getUrl() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Downloadable_Model_Url::p(
				$this->getLinkType(), $isSample = false, $this->getLinkFile(), $this->getLinkUrl()
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return Df_Downloadable_Model_Resource_Link
	 * 2016-10-14
	 * В родительском классе метод переобъявлен через PHPDoc,
	 * и поэтому среда разработки думает, что он публичен.
	 */
	/** @noinspection PhpHierarchyChecksInspection */
	protected function _getResource() {return Df_Downloadable_Model_Resource_Link::s();}

	/** @used-by Df_Downloadable_Model_Resource_Link_Collection::_construct() */
	const _C = __CLASS__;
	/** @return Df_Downloadable_Model_Resource_Link_Collection */
	public static function c() {return new Df_Downloadable_Model_Resource_Link_Collection;}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Downloadable_Model_Link
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/** @return Df_Downloadable_Model_Link */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}