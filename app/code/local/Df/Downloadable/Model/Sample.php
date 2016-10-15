<?php
class Df_Downloadable_Model_Sample extends Mage_Downloadable_Model_Sample {
	/**
	 * @override
	 * @return Df_Downloadable_Model_Resource_Sample_Collection
	 */
	public function getResourceCollection() {return self::c();}

	/** @return string */
	public function getUrl() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Downloadable_Model_Url::p(
				$this->getSampleType(), $isSample = true, $this->getSampleFile(), $this->getSampleUrl()
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return Df_Downloadable_Model_Resource_Sample
	 * 2016-10-14
	 * В родительском классе метод переобъявлен через PHPDoc,
	 * и поэтому среда разработки думает, что он публичен.
	 */
	/** @noinspection PhpHierarchyChecksInspection */
	protected function _getResource() {return Df_Downloadable_Model_Resource_Sample::s();}

	/** @used-by Df_Downloadable_Model_Resource_Sample_Collection::_construct() */

	/** @return Df_Downloadable_Model_Resource_Sample_Collection */
	public static function c() {return new Df_Downloadable_Model_Resource_Sample_Collection;}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Downloadable_Model_Sample
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/** @return Df_Downloadable_Model_Sample */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}