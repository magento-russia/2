<?php
class Df_Localization_Onetime_Processor_Db_Column extends Df_Core_Model {
	/** @return Df_Localization_Onetime_Dictionary_Db_Column */
	private function column() {return $this->cfg(self::$P__COLUMN);}

	/**
	 * @used-by p()
	 * @return void
	 */
	private function process() {
		/** @var string $primaryKey */
		$primaryKey = $this->column()->table()->primaryKey();
		/** @var string $column */
		$column = $this->column()->getName();
		/** @var Varien_Db_Select $select */
		$select = rm_select()->from($this->tableName(), array($primaryKey, $column));
		if ($this->column()->where()) {
			$select->where($this->column()->where());
		}
		/** @var array(array(string => string)) $rows */
		$rows = rm_conn()->fetchAssoc($select);
		foreach ($rows as $row) {
			self::$changed = false;
			/** @var array(string => string) $row */
			/** @var string $valueBefore */
			// 2015-08-23
			// Поддержка синтаксиса: <column name='params' filters='serialize,base64'>
			// Атрибут filters позволяет нам переводить значения,
			// которые хранятся в базе данных в закодированном виде
			$valueBefore = $this->column()->decode($row[$column]);
			/** @var string $valueAfter */
			if (!$this->column()->isComplex()) {
				$valueAfter = self::translate($valueBefore, $this->column()->terms());
				self::$changed = $valueAfter !== $valueBefore;
			}
			/**
			 * Тот случай, когда значением поля является сложная структура.
			 * В таком случае мы сначала посредством фильтров должны были привести
			 * хранившееся в БД строковое значение сложной структуры к массиву, например:
			 * <column name='params' filters='serialize,base64'>
			 * Далее мы переводим элементы этого массива, используя правила path, например:
			 *
				<table name='ves_layerslider/banner'>
					<column name='params' filters='serialize,base64'>
						<path value='звёздочка/звёздочка/itemData/content'>
							<term>
								<from>#The ultimate in modem design#</from>
								<to>В связи с этим нужно подчеркнуть, что субтехника просветляет форшлаг. Гипнотический рифф неравномерен. </to>
							</term>
						</path>
					</column>
				</table>
			*/
			else {
				$valueAfter = $valueBefore;
				foreach ($this->column()->paths() as $path) {
					/** @var Df_Localization_Onetime_Dictionary_Db_Path $path */
					df_a_deep_walk(
						$valueAfter
						, $path->value()
						/** @uses translate() */
						, array(__CLASS__, 'translate')
						, $path->terms()
					);
				}
			}
			if (self::$changed) {
				rm_conn()->update(
					$this->tableName()
					, array($column => $this->column()->encode($valueAfter))
					, array("? = {$primaryKey}" => $row[$primaryKey])
				);
			}
		}
	}

	/** @return string */
	private function tableName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->column()->table()->getName();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__COLUMN, Df_Localization_Onetime_Dictionary_Db_Column::_C);
	}
	/** @var string */
	protected static $P__COLUMN = 'column';

	/**
	 * @used-by Df_Localization_Onetime_Processor::applyDictionary()
	 * @param Df_Localization_Onetime_Dictionary_Db_Column $column
	 * @return void
	 */
	public static function p(Df_Localization_Onetime_Dictionary_Db_Column $column) {
		/** @var Df_Localization_Onetime_Processor_Db_Column $i */
		$i = new self(array(self::$P__COLUMN => $column));
		$i->process();
	}

	/**
	 * @used-by df_a_deep_walk()
	 * @used-by process()
	 * @param mixed $valueBefore
	 * @param Df_Localization_Onetime_Dictionary_Terms $terms
	 * @return mixed
	 */
	public static function translate($valueBefore, Df_Localization_Onetime_Dictionary_Terms $terms) {
		/** @var mixed $result */
		$result = $valueBefore;
		if (!is_array($result) && !is_object($result)) {
			foreach ($terms as $term) {
				/** @var Df_Localization_Onetime_Dictionary_Term $term */
				$result = $term->translate($valueBefore);
				if (is_null($result)) {
					$result = $valueBefore;
				}
				else if ($result !== $valueBefore) {
					$valueBefore = $result;
					/**
					 * Обратите внимание, что это выражение нельзя упрощать до
					 * self::$changed = $result !== $valueBefore
					 * потому что в этот метод мы попадаем много раз,
					 * и далеко не каждая попытка перевода является успешной,
					 * а флаг @uses $changed должен быть равен true
					 * если хотя бы одна попытка была успешной.
					 */
					self::$changed = true;
				}
			}
		}
		return $result;
	}

	/**
	 * @used-by process()
	 * @used-by translate()
	 * @var bool
	 */
	private static $changed = false;
}