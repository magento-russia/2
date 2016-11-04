<?php
namespace Df\C1\Cml2\Import\Data\Entity\Attribute;
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

	/** @return \Df\C1\Cml2\Import\Data\Collection\ReferenceListPart\Items */
	public function getItems() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = \Df\C1\Cml2\Import\Data\Collection\ReferenceListPart\Items::i(
				$this->e()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => null|array(string => string[])) */
	public function getOptionsInMagentoFormat() {
		if (!isset($this->{__METHOD__})) {
			/** @var string[] $values */
			$values = array();
			/** @var int $optionIndex */
			$optionIndex = 0;
			foreach ($this->getItems() as $item) {
				/** @var \Df\C1\Cml2\Import\Data\Entity\ReferenceListPart\Item $item */
				$values['option_' . $optionIndex] = array($item->getName());
				$optionIndex++;
			}
			/** @var string[] $optionNames */
			$optionNames = array_keys($values);
			/** @var array(string => null) $optionStubs */
			$optionStubs = array_fill_keys($optionNames, null);
			$this->{__METHOD__} = array(
				'value' => $values, 'order' => $optionStubs, 'delete' => $optionStubs
			);
		}
		return $this->{__METHOD__};
	}
}