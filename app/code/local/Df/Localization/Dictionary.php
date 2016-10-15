<?php
abstract class Df_Localization_Dictionary extends \Df\Xml\Parser\Entity {
	/**
	 * @used-by pathFull()
	 * @see Df_Localization_Onetime_Dictionary::type()
	 * @see Df_Localization_Realtime_Dictionary::type()
	 * @return string
	 */
	abstract protected function type();

	/**
	 * @override
	 * @see \Df\Xml\Parser\Entity::e()
	 * @return \Df\Xml\X
	 */
	public function e() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $path */
			$path = $this->modulePath("etc/dictionaries/{$this->type()}/{$this->pathLocal()}");
			if (!file_exists($path)) {
				df_error('Не найден требуемый файл «%s».', $path);
			}
			$this->{__METHOD__} = df_first(df_xml_load_file($path)->xpath('/dictionary'));
			df_assert($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by e()
	 * @used-by Df_Localization_Realtime_Dictionary::hasEntry()
	 * @return string
	 */
	protected function pathLocal() {return $this[self::$P__PATH_LOCAL];}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__PATH_LOCAL, DF_V_STRING_NE);
	}
	/** @var string */
	protected static $P__PATH_LOCAL = 'path_local';

	/**
	 * @used-by Df_Localization_Onetime_Dictionary::s()
	 * @used-by Df_Localization_Realtime_Dictionary::s()
	 * @param string $className
	 * @param string $pathLocal
	 * @return Df_Localization_Onetime_Dictionary
	 */
	protected static function sc($className, $pathLocal) {
		return df_sc($className, __CLASS__, array(self::$P__PATH_LOCAL => $pathLocal), $pathLocal);
	}
}