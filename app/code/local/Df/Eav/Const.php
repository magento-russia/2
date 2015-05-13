<?php
interface Df_Eav_Const {
	/**
	 * Имя свойства (или колонки таблицы),
	 * хранящего идентификатор учётного объекта,
	 * используемый системой 1С: Управление торговлей
	 * при информационном обмене с сайтом.
	 */
	const ENTITY_EXTERNAL_ID = 'rm_1c_id';
	const ENTITY_EXTERNAL_ID_OLD = 'df_1c_id';
	const FRONTEND_CLASS__NATURAL_NUMBER = 'validate-number validate-greater-than-zero';
}