<?php
class Df_Localization_Onetime_Dictionary_Db_Column extends \Df\Xml\Parser\Entity {
	/**
	 * @used-by Df_Localization_Onetime_Processor_Db_Column::process()
	 * @param string $value
	 * @return string
	 */
	public function decode($value) {return $this->applyFilters($value, __FUNCTION__);}

	/**
	 * @used-by Df_Localization_Onetime_Processor_Db_Column::process()
	 * @param string $value
	 * @return string
	 */
	public function encode($value) {return $this->applyFilters($value, __FUNCTION__);}

	/** @return bool */
	public function isComplex() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = !!$this->paths()->count();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Entity::getName()
	 * @return string
	 */
	public function getName() {return $this->getAttribute('name');}

	/** @return Df_Localization_Onetime_Dictionary_Db_Paths */
	public function paths() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Localization_Onetime_Dictionary_Db_Paths::i($this->e());
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Localization_Onetime_Dictionary_Db_Table */
	public function table() {return $this[self::P__TABLE];}

	/** @return Df_Localization_Onetime_Dictionary_Terms */
	public function terms() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Localization_Onetime_Dictionary_Terms::i($this->e());
		}
		return $this->{__METHOD__};
	}

	/**
		<table name='ves_megamenu/megamenu_widget'>
			<column name='params' filters='serialize'>
				<where><![CDATA[params LIKE '%show_name%']]></where>
			</column>
		</table>
	 * @return string
	 */
	public function where() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = (string)$this->e()->descend('where');
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $filter
	 * @param string $direction
	 * @param string $value
	 * @return string
	 */
	private function applyFilter($filter, $direction, $value) {
		/**
		 * @uses decode_base64()
		 * @uses encode_base64()
		 */
		return call_user_func(array(__CLASS__, "{$direction}_{$filter}"), $value);
	}

	/**
	 * @param string $value
	 * @param string $direction
	 * @return string
	 */
	private function applyFilters($value, $direction) {
		/** @var string $result */
		$result = $value;
		/** @var string[] $filters */
		$filters = $this->filters();
		if ('decode' === $direction) {
			$filters = array_reverse($filters);
		}
		foreach ($filters as $filter) {
			$result = $this->applyFilter($filter, $direction, $result);
		}
		return $result;
	}

	/**
	 * 2015-08-23
	 * Поддержка синтаксиса:
	 * <column name='params' filters='serialize,base64'>
	 * Атрибут filters позволяет нам переводить значения,
	 * которые хранятся в базе данных в закодированном виде.
	 * @return string[]
	 */
	private function filters() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_csv_parse(df_nts($this->getAttribute('filters')));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__TABLE, Df_Localization_Onetime_Dictionary_Db_Table::class);
	}
	/**
	 * @used-by Df_Localization_Onetime_Dictionary_Db_Columns::itemClass()
	 * @used-by Df_Localization_Onetime_Processor_Db_Column::_construct()
	 */

	/** @used-by Df_Localization_Onetime_Dictionary_Db_Columns::itemParams() */
	const P__TABLE = 'table';

	/**
	 * @used-by applyFilter()
	 * @param string $value
	 * @return string
	 */
	private static function decode_base64($value) {return base64_decode($value);}

	/**
	 * @used-by applyFilter()
	 * @param string $value
	 * @return string
	 */
	private static function decode_serialize($value) {return unserialize($value);}

	/**
	 * @used-by applyFilter()
	 * @param string $value
	 * @return string
	 */
	private static function encode_base64($value) {return base64_encode($value);}

	/**
	 * @see decode_serialize()
	 * @used-by applyFilter()
	 * @param string $value
	 * @return string
	 */
	private static function encode_serialize($value) {return serialize($value);}
}


 