<?php
/**
 * @param int $levelsToSkip
 * Позволяет при записи стека вызовов пропустить несколько последних вызовов функций,
 * которые и так очевидны (например, вызов данной функции, вызов df_bt() и т.п.)
 * @return void
 */
function df_bt($levelsToSkip = 0) {
	/** @var array $bt */
	$bt = array_slice(debug_backtrace(), $levelsToSkip);
	/** @var array $compactBT */
	$compactBT = array();
	/** @var int $traceLength */
	$traceLength = count($bt);
	/**
	 * 2015-07-23
	 * 1) Удаляем часть файлового пути до корневой папки Magento.
	 * 2) Заменяем разделитель папок на унифицированный.
	 */
	/** @var string $bp */
	$bp = BP . DIRECTORY_SEPARATOR;
	/** @var bool $nonStandardDS */
	$nonStandardDS = DIRECTORY_SEPARATOR !== '/';
	for ($traceIndex = 0; $traceIndex < $traceLength; $traceIndex++) {
		/** @var array $currentState */
		$currentState = df_a($bt, $traceIndex);
		/** @var array(string => string) $nextState */
		$nextState = df_a($bt, 1 + $traceIndex, array());
		/** @var string $file */
		$file = str_replace($bp, '', df_a($currentState, 'file'));
		if ($nonStandardDS) {
			$file = str_replace(DIRECTORY_SEPARATOR, '/', $file);
		}
		$compactBT[]= array(
			'Файл' => $file
			,'Строка' => df_a($currentState, 'line')
			,'Субъект' =>
				!$nextState
				? ''
				: rm_concat_clean('::', df_a($nextState, 'class'), df_a($nextState, 'function'))
			,'Объект' =>
				!$currentState
				? ''
				: rm_concat_clean('::', df_a($currentState, 'class'), df_a($currentState, 'function'))
		);
	}
	rm_report('bt-{date}-{time}.log', print_r($compactBT, true));
}

/**
 * @param string $nameTemplate
 * @param string $message
 * @return void
 */
function rm_report($nameTemplate, $message) {
	rm_file_put_contents(rm_file_name(Mage::getBaseDir('var') . DS . 'log', $nameTemplate), $message);
}