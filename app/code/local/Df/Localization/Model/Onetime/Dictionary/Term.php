<?php
class Df_Localization_Model_Onetime_Dictionary_Term extends Df_Core_Model_SimpleXml_Parser_Entity {
	/**
	 * @override
	 * @return string
	 */
	public function getId() {return $this->getFrom();}

	/**
	 * @used-by Df_Localization_Model_Onetime_Processor_Db_Column::processSimple()
	 * @return string|null
	 */
	public function getFrom() {return $this->getEntityParam('from');}

	/**
	 * Df_Localization_Model_Onetime_Processor_Db_Column::processSimple()
	 * @return string|null
	 */
	public function getTo() {return $this->getEntityParam('to');}

	/**
	 * 2015-08-23
	 * Здесь допустим как двухсторонний, так и односторонний лайк (только справа или только слева).
	 * @used-by Df_Localization_Model_Onetime_Processor_Db_Column::isItLike()
	 * @return bool
	 */
	public function isItLike() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				rm_starts_with($this->getFrom(), '%') || rm_ends_with($this->getFrom(), '%')
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by Df_Localization_Model_Onetime_Processor_Attribute::processTerm()
	 * @used-by Df_Localization_Model_Onetime_Processor_Entity::applyTermToProperty()
	 * @used-by Df_Localization_Model_Onetime_Processor_Poll::processTerm()
	 * @used-by Df_Localization_Model_Onetime_Processor_Cms_Widget::processTerm()
	 * @used-by Df_Localization_Model_Onetime_Processor_Em_Megamenupro::processTerm()
	 * @param string|null|mixed $textOriginal
	 * @return string|null
	 */
	public function translate($textOriginal) {
		/** @var string|null $result */
		$result = null;
		if ($this->needFromBeEmpty() && !$textOriginal) {
			$result = $this->getTo();
		}
		else if ($textOriginal && is_string($textOriginal)) {
			/** @var string $textProcessed */
			if ($this->isItLike2()
				&& (
					// 2015-08-24
					// Допускает выражение <from>%%</from>
					!$this->getFromForLike()
					|| rm_contains($textOriginal, $this->getFromForLike())
				)
			) {
				// Обратите внимание, что символ процента должен стоять с обеих сторон фразы.
				$result = $this->getTo();
			}
			/**
			 * 2015-08-24
			 * Поддержка синтаксиса:
				<term>
					<from>@Phone: +01 888 (000) 1234@</from>
					<to>+7 (495) 745-51-65</to>
				</term>
			 * Такой синтаксис позволяет заменить одну подстроку на другую,
			 * не прибегая к регулярному выражению (при котором пришлось бы следить за спецсимволами).
			 */
			else if ($this->isItNeedle()) {
				$textProcessed = str_replace(
					$this->getFromForNeedle(), $this->getTo(), $textOriginal
				);
				/**
				 * Вызываем setData() только при реальном изменении значения свойства,
				 * чтобы не менять попусту значение hasDataChanges
				 * (что потом приведёт к ненужным сохранениям объектов).
				 */
				if ($textProcessed !== $textOriginal) {
					$result = $textProcessed;
				}
			}
			else if ($this->isItRegEx()) {
				$textProcessed = rm_preg_replace($this->getFrom(), $this->getTo(), $textOriginal);
				/**
				 * Вызываем setData() только при реальном изменении значения свойства,
				 * чтобы не менять попусту значение hasDataChanges
				 * (что потом приведёт к ненужным сохранениям объектов).
				 */
				if ($textProcessed !== $textOriginal) {
					$result = $textProcessed;
				}
			}
			else {
				/** @var string $textOriginalNormalized */
				$textOriginalNormalized = rm_normalize($textOriginal);
				$textProcessed =
					str_replace($this->getFromNormalized(), $this->getTo(), $textOriginalNormalized)
				;
				/**
				 * Вызываем setData() только при реальном изменении значения свойства,
				 * чтобы не менять попусту значение hasDataChanges
				 * (что потом приведёт к ненужным сохранениям объектов).
				 */
				if ($textProcessed !== $textOriginalNormalized) {
					$result = $textProcessed;
				}
			}
		}
		return $result;
	}

	/** @return string */
	private function getFromForLike() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_trim($this->getFrom(), '%');
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-08-24
	 * Поддержка синтаксиса:
		<term>
			<from>@Phone: +01 888 (000) 1234@</from>
			<to>+7 (495) 745-51-65</to>
		</term>
	 * Такой синтаксис позволяет заменить одну подстроку на другую,
	 * не прибегая к регулярному выражению (при котором пришлось бы следить за спецсимволами).
	 * @used-by translate()
	 * @return string
	 */
	private function getFromForNeedle() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_trim($this->getFrom(), '@');
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getFromNormalized() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_normalize($this->getFrom());
		}
		return $this->{__METHOD__};
	}

	/**
	 * Обратите внимание, что символ процента должен стоять с обеих сторон фразы.
	 * Тогда замену проще запрограммировать,
	 * и для большинства практических ситуаций этого достаточно).
	 * @used-by translate()
	 * @return bool
	 */
	private function isItLike2() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				rm_starts_with($this->getFrom(), '%') && rm_ends_with($this->getFrom(), '%')
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-08-24
	 * Поддержка синтаксиса:
		<term>
			<from>@Phone: +01 888 (000) 1234@</from>
			<to>+7 (495) 745-51-65</to>
		</term>
	 * Такой синтаксис позволяет заменить одну подстроку на другую,
	 * не прибегая к регулярному выражению (при котором пришлось бы следить за спецсимволами).
	 * @used-by translate()
	 * @return bool
	 */
	private function isItNeedle() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				rm_starts_with($this->getFrom(), '@') && rm_ends_with($this->getFrom(), '@')
			;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function isItRegEx() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_text()->isRegex($this->getFrom());
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function needFromBeEmpty() {return '{empty}' === $this->getFrom();}

	/** Используется из @see Df_Localization_Model_Onetime_Dictionary_Terms::getItemClass() */
	const _CLASS = __CLASS__;

	/**
	 * @static
	 * @param Df_Varien_Simplexml_Element|string $simpleXml
	 * @return Df_Localization_Model_Onetime_Dictionary_Term
	 */
	public static function i($simpleXml) {return self::_c($simpleXml, __CLASS__);}
}