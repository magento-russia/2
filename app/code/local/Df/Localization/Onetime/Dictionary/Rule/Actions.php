<?php
class Df_Localization_Onetime_Dictionary_Rule_Actions extends \Df\Xml\Parser\Entity {
	/**
	 * Обратите внимание, что для одного свойства может быть несколько нстатндартных правил,
	 * например:
		<actions>
			<title>
				<from>About Us</from>
				<to>О Российской сборке Magento</to>
			</title>
			<title>
				<from>About  Us</from>
				<to>О Российской сборке Magento</to>
			</title>
			<content_heading>
				<from>{empty}</from>
				<to>О Российской сборке Magento</to>
			</content_heading>
	    </actions>
	 * Поэтому данный метод возвращает массив.
	 * @param string $termName
	 * @return Df_Localization_Onetime_Dictionary_Term[]
	 */
	public function getCustomTermsByName($termName) {
		df_param_string_not_empty($termName, 0);
		if (!isset($this->{__METHOD__}[$termName])) {
			/** @var @var Df_Localization_Onetime_Dictionary_Term[] $terms */
			$terms = [];
			/** @var @var \Df\Xml\X[] $nodes */
			$nodes = $this->e()->xpath($termName);
			df_assert_ne(false, $nodes);
			foreach ($nodes as $node) {
				/** @var @var \Df\Xml\X[] $node */
				$terms[]= Df_Localization_Onetime_Dictionary_Term::i($node);
			}
			$this->{__METHOD__}[$termName] = $terms;
		}
		return $this->{__METHOD__}[$termName];
	}

	/** @return Df_Localization_Onetime_Dictionary_Terms */
	public function terms() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Localization_Onetime_Dictionary_Terms::i($this->e());
		}
		return $this->{__METHOD__};
	}

	/** @return string|null */
	public function getTitleNew() {return $this->leaf('new_title');}

	/**
	 * @used-by Df_Localization_Onetime_Type::getActionsClass()
	 * @used-by Df_Localization_Onetime_Processor_Entity::_construct()
	 */

	/**
	 * @static
	 * @param string $concreteClass
	 * @param \Df\Xml\X $e
	 * @return Df_Localization_Onetime_Dictionary_Rule_Actions
	 */
	public static function createConcrete($concreteClass, \Df\Xml\X $e) {
		return df_ic($concreteClass, __CLASS__, array(self::$P__E => $e));
	}
}