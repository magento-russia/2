<?php
namespace Df\C1\Config\Source;
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class WhichDescriptionFieldToUpdate extends \Df_Admin_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {return df_map_to_options([
		self::V__DESCRIPTION => 'описание',
		self::V__SHORT_DESCRIPTION => 'краткое описание',
		self::V__BOTH => 'описание и краткое описание',
		'none' => 'никакое'
	]);}

	/**
	 * @used-by toOptionArrayInternal()
	 * @used-by \Df\C1\Cml2\Import\Processor\Product\Type::getDescription()
	 * @used-by \Df\C1\Cml2\Import\Processor\Product\Type::getDescriptionShort()
	 */
	const V__BOTH = 'both';
	/**
	 * @used-by toOptionArrayInternal()
	 * @used-by \Df\C1\Cml2\Import\Processor\Product\Type::getDescription()
	 */
	const V__DESCRIPTION = 'description';
	/**
	 * @used-by toOptionArrayInternal()
	 * @used-by \Df\C1\Cml2\Import\Processor\Product\Type::getDescriptionShort()
	 */
	const V__SHORT_DESCRIPTION = 'short_description';
}