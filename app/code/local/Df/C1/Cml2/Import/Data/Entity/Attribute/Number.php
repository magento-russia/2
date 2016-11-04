<?php
namespace Df\C1\Cml2\Import\Data\Entity\Attribute;
class Number extends \Df\C1\Cml2\Import\Data\Entity\Attribute {
	/**
	 * @override
	 * @return string
	 */
	public function getBackendModel() {return '';}

	/**
	 * @override
	 * @return string
	 */
	public function getBackendType() {return 'varchar';}

	/**
	 * @override
	 * @return string
	 */
	public function getFrontendInput() {return 'text';}

	/**
	 * @override
	 * @return string
	 */
	public function getSourceModel() {return '';}
}