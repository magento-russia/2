<?php
abstract class Df_Localization_Onetime_Dictionary_Rule_Conditions_Abstract
	extends \Df\Xml\Parser\Entity {
	/** @return string */
	abstract protected function getEntityClass();

	/**
	 * @param Mage_Core_Model_Abstract $entity
	 * @return array(string => string)
	 */
	abstract protected function getTestMap(Mage_Core_Model_Abstract $entity);

	/**
	 * @param Mage_Core_Model_Abstract $entity
	 * @return bool
	 */
	public function isApplicable(Mage_Core_Model_Abstract $entity) {
		$this->checkConditionsAreCorrect($entity);
		/** @var string $entityClass */
		$entityClass = $this->getEntityClass();
		df_assert($entity instanceof $entityClass);
		/** @var bool $result */
		$result = true;
		foreach ($this->getTestMap($entity) as $paramName => $expectedValue) {
			/** @var string $paramName */
			/** @var string $expectedValue */
			$result = $this->test($paramName, $expectedValue);
			if (!$result) {
				break;
			}
		}
		return $result;
	}

	/**
	 * Обратите внимание, что метод выполняет проверку только при первом вызове.
	 * При последующих вызовах, даже с другим значением параметра $entity,
	 * проверка не выполняется.
	 * Так сделано потому, что у нас, по сути,
	 * ключи, возвращаемые методом @see getTestMap()
	 * и результат метода @see getAllowedKeys()
	 * не зависят от объекта $entity (хотя теоретически могут зависеть).
	 * @param Mage_Core_Model_Abstract $entity
	 * @return void
	 */
	private function checkConditionsAreCorrect(Mage_Core_Model_Abstract $entity) {
		if (!isset($this->{__METHOD__})) {
			/** @var string[] $allowedTags */
			$allowedTags = $this->getAllowedTags($entity);
			/** @var string[] $notAllowedTags */
			$notAllowedTags = array_diff($this->e()->childrenNames(), $allowedTags);
			if ($notAllowedTags) {
				/** @var bool $isMultiple */
				$isMultiple = 1 < count($notAllowedTags);
				df_error(
					"Следующая ветка правила некорректна, потому что тип условий {type}"
					. " не разрешает {теги} {conditions}."
					. "\nДля данного типа условий разрешены теги: {allowedTags}.{xml}"
					,array(
						'{type}' => get_class($this)
						,'{теги}' => $isMultiple ? 'теги:' : 'тег'
						,'{conditions}' => df_csv_pretty_quote($notAllowedTags)
					  	,'{xml}' => $this->getXmlForReport()
						,'{allowedTags}' => df_csv_pretty_quote($allowedTags)
					)
				);
			}
			$this->{__METHOD__} = true;
		}
	}

	/**
	 * @param Mage_Core_Model_Abstract $entity
	 * @return string[]
	 */
	private function getAllowedTags(Mage_Core_Model_Abstract $entity) {
		return array_merge(array_keys($this->getTestMap($entity)), array('type'));
	}

	/**
	 * @param string $paramName
	 * @param string $expectedValue
	 * @return bool
	 */
	private function test($paramName, $expectedValue) {
		/** @var string|null $paramValue */
		$paramValue = $this->leaf($paramName);
		/**
		 * 2015-01-29
		 * Первая часть условия (!$paramValue) — нужная, но опасная.
		 * Нужная, потому что она позволяет делать универсальные правила.
		 * Например, пусть есть правило:
				<rule>
					<conditions>
						<type>customer_group</type>
						<customer_group><code>Wholesale</code></customer_group>
					</conditions>
						<actions><new_title>оптовый</new_title></actions>
				</rule>
		 * Это правило — не универсальное,
		 * а для объекта типа «группа покупателей» с конкретным кодом «Wholesale».
		 * На базе этого правила можно сделать универсальное правило:
		 * правило сразу для всех объектов типа «группа покупателей»:
				<rule>
					<conditions>
						<type>customer_group</type>
					</conditions>
					<actions>
						<term>
							<from>Retailer</from>
							<to>розничный</to>
						</term>
						<term>
							<from>Wholesale</from>
							<to>оптовый</to>
						</term>
					</actions>
				</rule>
		 * В этом правиле внутри ветки conditions отсутствуют какие-либо условия,
		 * поэтому это правило является универсальным.
		 * Так вот, первая часть условия ниже (!$paramValue)
		 * как раз обрабатывает случай с универсальными правилами.
		 * Она как бы говорит:
		 * «если в данном правиле конкретное условие отсутствует —
		 * значит, объект данному условию удовлетворяет»
		 *
		 * Однако, эта первая часть условия (!$paramValue) — в то же время и опасная.
		 * Пусть у нас есть правило:
				<rule>
					<conditions>
						<type>customer_group</type>
						<customer_group><title>Wholesale</title></customer_group>
					</conditions>
					<actions><new_title>оптовый</new_title></actions>
				</rule>
		 * Это правило — по недосмотру ошибочное:
		 * программист внутри ветки «customer_group» вместо тега «code» поставил тег «title».
		 * Так вот, из-за первой части условия (!$paramValue) система будет считать,
		 * что данное правило выполняется сразу для всех объектов типа «группа покупателей»
		 * и всем им попытается присвоить заголовок «оптовый»,
		 * а так как данный заголовок конкретно для объектов типа «группа покупателей»
		 * должен быть уникальным, то такое в виду невинное правило
		 * приведёт к сбою работы русификатора, поломает сразу весь русификатор полностью:
		 * работа русификатора завершится фатальным сбоем.
		 *
		 * 2015-01-30
		 * Доработал русификатор, теперь в случае описанного выше частного сбоя
		 * русификатор не завершает работу полностью, а вместо этого собирает сбои
		 * и потом, по завершению работы русификатора показывает все сбои администратору.
		 * @see Df_Localization_Onetime_Processor::saveModifiedMagentoEntities()
		 * Теперь ещё бы сделать так,чтобы русификатор предупреждал
		 * об ошибочном использовании тега
		 * (например, тега «title» вместо тега «code» в примере выше).
		 *
		 * 2015-01-30
		 * Ура, сделал и проверку корректности условия.
		 * @see checkConditionsAreCorrect()
		 * Теперь русификатор предупреждает об ошибочном использовании тегов
		 * (например, тега «title» вместо тега «code» в примере выше).
		 */
		return !$paramValue || $paramValue === $expectedValue;
	}

	/**
	 * @param string $class
	 * @param \Df\Xml\X $e
	 * @return Df_Localization_Onetime_Dictionary_Rule_Conditions_Abstract
	 */
	public static function ic($class, \Df\Xml\X $e) {
		return df_ic($class, __CLASS__, array(self::$P__E => $e));
	}
}