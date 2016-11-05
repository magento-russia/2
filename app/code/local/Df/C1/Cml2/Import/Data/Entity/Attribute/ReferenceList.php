<?php
namespace Df\C1\Cml2\Import\Data\Entity\Attribute;
use Df\C1\Cml2\Import\Data\Entity\ReferenceListPart\Item;
use Df\C1\Cml2\Import\Data\Collection\ReferenceListPart\Items;
class ReferenceList extends \Df\C1\Cml2\Import\Data\Entity\Attribute {
	/**
	 * @override
	 * @return string
	 */
	public function getBackendModel() {return '';}
	/**
	 * @override
	 * @return string
	 */
	public function getBackendType() {return 'int';}
	/**
	 * @override
	 * @return string
	 */
	public function getFrontendInput() {return 'select';}
	/**
	 * @override
	 * @return string
	 */
	public function getSourceModel() {return '';}

	/** @return Items */
	public function getItems() {return dfc($this, function() {return Items::i($this->e());});}

	/** @return array(string => null|array(string => string[])) */
	public function getOptionsInMagentoFormat() {return dfc($this, function() {
		/** @var string[] $values */
		$values = [];
		/** @var int $optionIndex */
		$optionIndex = 0;
		foreach ($this->getItems() as $item) {
			/** @var Item $item */
			$values['option_' . $optionIndex] = [$item->getName()];
			$optionIndex++;
		}
		/** @var string[] $optionNames */
		$optionNames = array_keys($values);
		/** @var array(string => null) $optionStubs */
		$optionStubs = array_fill_keys($optionNames, null);
		return ['value' => $values, 'order' => $optionStubs, 'delete' => $optionStubs];
	});}
}