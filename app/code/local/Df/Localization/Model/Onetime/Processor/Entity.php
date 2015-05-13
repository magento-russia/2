<?php
abstract class Df_Localization_Model_Onetime_Processor_Entity extends Df_Core_Model_Abstract {
	/** @return string[] */
	abstract protected function getTitlePropertyName();

	/** @return void */
	public function process() {
		/**
		 * Обратите внимание, что описанный в словаре объект
		 * запросто может отсутствовать в базе данных интернет-магазина
		 * (например, если он был удалён администратором).
		 */
		if ($this->getEntity()) {
			$this->updateTitle();
			$this->updateProperties();
		}
	}

	/** @return Df_Localization_Model_Onetime_Dictionary_Rule_Actions */
	protected function getActions() {return $this->cfg(self::$P__ACTIONS);}

	/** @return Mage_Core_Model_Abstract */
	protected function getEntity() {return $this->cfg(self::$P__ENTITY);}

	/** @return string[] */
	protected function getTranslatableProperties() {return array();}

	/**
	 * Перечислите здесь свойства,
	 * для которых будут применяться не только общие правила для всех свойств,
	 * но и правила, индивидуальные для данных свойств.
	 * Например:
			<rule>
				<conditions>
					<type>page</type>
					<page>
						<url_key>privacy-policy-cookie-restriction-mode</url_key>
					</page>
				</conditions>
				<actions>
					<title>
						<from>Privacy Policy</from>
						<to>Правила покупки</to>
					</title>
					<content_heading>
						<from>Privacy Policy</from>
						<to>Правила покупки</to>
					</content_heading>
					<term>
						<from>%collect%</from>
						<to>Опишите здесь правила покупки в Вашем магазине,
		либо разместите какую-либо другую полезную покупателям информацию.</to>
					</term>
				</actions>
			</rule>
	 * Здесь имеются индивидуальные правила для свойств
	 *
	 * @return string[]
	 */
	protected function getTranslatablePropertiesCustom() {return array();}

	/**
	 * @param Df_Localization_Model_Onetime_Dictionary_Term $term
	 * @return void
	 */
	protected function processTerm(Df_Localization_Model_Onetime_Dictionary_Term $term) {
		foreach ($this->getTranslatableProperties() as $property) {
			/** @var string $property */
			$this->applyTermToProperty($term, $property);
		}
	}

	/**
	 * @param string $newTitle
	 * @return void
	 */
	protected function setTitle($newTitle) {
		$this->getEntity()->setData($this->getTitlePropertyName(), $newTitle);
	}

	/**
	 * @param string|null|mixed $textOriginal
	 * @param Df_Localization_Model_Onetime_Dictionary_Term $term
	 * @return string|null
	 */
	protected function translate($textOriginal, Df_Localization_Model_Onetime_Dictionary_Term $term) {
		/** @var string|null $result */
		$result = null;
		if ($term->needFromBeEmpty() && !$textOriginal) {
			$result = $term->getTo();
		}
		else if ($textOriginal && is_string($textOriginal)) {
			/** @var string $textProcessed */
			if ($term->isItLike() && rm_contains($textOriginal, $term->getFromForLike())) {
				// Обратите внимание, что символ процента должен стоять с обеих сторон фразы.
				$result = $term->getTo();
			}
			else if ($term->isItRegEx()) {
				$textProcessed = preg_replace($term->getFrom(), $term->getTo(), $textOriginal);
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
					str_replace($term->getFromNormalized(), $term->getTo(), $textOriginalNormalized)
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

	/** @return void */
	protected function updateProperties() {
		$this->updatePropertiesCustom();
		foreach ($this->getActions()->getTerms() as $term) {
			/** @var Df_Localization_Model_Onetime_Dictionary_Term $term */
			$this->processTerm($term);
		}
	}

	/** @return void */
	protected function updatePropertiesCustom() {
		/** Сначала применяем индивидуальные правила перевода для конкретных свойств. */
		foreach ($this->getTranslatablePropertiesCustom() as $customProperty) {
			/** @var string $customProperty */
			/** @var Df_Localization_Model_Onetime_Dictionary_Term[] $customTerms */
			$customTerms = $this->getActions()->getCustomTermsByName($customProperty);
			foreach ($customTerms as $customTerm) {
				/** @var Df_Localization_Model_Onetime_Dictionary_Term $customTerm */
				$this->applyTermToProperty($customTerm, $customProperty);
			}
		}
	}

	/** @return void */
	protected function updateTitle() {
		if ($this->getActions()->getTitleNew()) {
			$this->setTitle($this->getActions()->getTitleNew());
		}
	}

	/**
	 * @param Df_Localization_Model_Onetime_Dictionary_Term $term
	 * @param $propertyName
	 */
	private function applyTermToProperty(
		Df_Localization_Model_Onetime_Dictionary_Term $term
		,$propertyName
	) {
		/** @var string|null $textProcessed */
		$textProcessed = $this->translate($this->getEntity()->getData($propertyName), $term);
		if (!is_null($textProcessed)) {
			$this->getEntity()->setData($propertyName, $textProcessed);
		}
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__ACTIONS, Df_Localization_Model_Onetime_Dictionary_Rule_Actions::_CLASS);
		$this->_prop(self::$P__ENTITY, 'Mage_Core_Model_Abstract');
	}
	/** @var string */
	protected static $P__ENTITY = 'entity';
	/** @var string */
	private static $P__ACTIONS = 'actions';

	/**
	 * @param string $className
	 * @param Mage_Core_Model_Abstract $entity
	 * @param Df_Localization_Model_Onetime_Dictionary_Rule_Actions $actions
	 * @return void
	 */
	public static function processStatic(
		$className
		, Mage_Core_Model_Abstract $entity
		, Df_Localization_Model_Onetime_Dictionary_Rule_Actions $actions
	) {
		/** @var Df_Localization_Model_Onetime_Processor_Entity $processor */
		$processor = new $className(array(self::$P__ENTITY => $entity, self::$P__ACTIONS => $actions));
		df_assert($processor instanceof Df_Localization_Model_Onetime_Processor_Entity);
		$processor->process();
	}
}