<?php
class Df_Localization_Model_Onetime_Dictionary_Rule_Actions extends Df_Core_Model_SimpleXml_Parser_Entity {
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
	 * @return Df_Localization_Model_Onetime_Dictionary_Term[]
	 */
	public function getCustomTermsByName($termName) {
		df_param_string_not_empty($termName, 0);
		if (!isset($this->{__METHOD__}[$termName])) {
			/** @var @var Df_Localization_Model_Onetime_Dictionary_Term[] $terms */
			$terms = array();
			/** @var @var Df_Varien_Simplexml_Element[] $nodes */
			$nodes = $this->e()->xpath($termName);
			df_assert_ne(false, $nodes);
			foreach ($nodes as $node) {
				/** @var @var Df_Varien_Simplexml_Element[] $node */
				$terms[]= Df_Localization_Model_Onetime_Dictionary_Term::i($node);
			}
			$this->{__METHOD__}[$termName] = $terms;
		}
		return $this->{__METHOD__}[$termName];
	}

	/** @return Df_Localization_Model_Onetime_Dictionary_Terms */
	public function getTerms() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Localization_Model_Onetime_Dictionary_Terms::i($this->e());
		}
		return $this->{__METHOD__};
	}

	/** @return string|null */
	public function getTitleNew() {return $this->getEntityParam('new_title');}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param string $concreteClass
	 * @param Df_Varien_Simplexml_Element $simpleXml
	 * @return Df_Localization_Model_Onetime_Dictionary_Rule_Actions
	 */
	public static function createConcrete($concreteClass, Df_Varien_Simplexml_Element $simpleXml) {
		/** @var Df_Localization_Model_Onetime_Dictionary_Rule_Actions $result */
		$result = new $concreteClass(array(self::P__SIMPLE_XML => $simpleXml));
		df_assert($result instanceof Df_Localization_Model_Onetime_Dictionary_Rule_Actions);
		return $result;
	}
}