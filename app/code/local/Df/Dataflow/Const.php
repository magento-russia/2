<?php
interface Df_Dataflow_Const {
	const BATCH_IMPORT__PARAM__ID = 'id';
	const BATCH_IMPORT__PARAM__BATCH_ID = 'batch_id';
	const BATCH_IMPORT__PARAM__STATUS = 'status';
	const BATCH_EXPORT__PARAM__ID = 'id';
	const BATCH_EXPORT__PARAM__BATCH_ID = 'batch_id';
	const BATCH_EXPORT__PARAM__STATUS = 'status';
	/**
	 * Счётчик импортированных строк данных.
	 * Хранится в сессии.
	 */
	const P__COUNTER = 'rm.dataflow.counter';
}