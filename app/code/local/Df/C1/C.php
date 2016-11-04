<?php
namespace Df\C1;
interface C {
	/**
	 * Имя свойства (или колонки таблицы),
	 * хранящего идентификатор учётного объекта,
	 * используемый системой 1С:Управление торговлей
	 * при информационном обмене с сайтом.
	 */
	const ENTITY_EXTERNAL_ID = 'df_1c_id';
	const ENTITY_EXTERNAL_ID_OLD = 'rm_1c_id';
	const PRODUCT_ATTRIBUTE_GROUP_NAME = '1С';
}