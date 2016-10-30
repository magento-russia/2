<?php
/**
 * @param Mage_Core_Model_Abstract|string $model
 * @param int|string $id
 * @param string|null $field [optional]
 * @param bool $throwOnError [optional]
 * @return Mage_Core_Model_Abstract|null
 */
function df_load($model, $id, $field = null, $throwOnError = true) {
	/**
	 * Обратите внимание, что идентификатор необязательно является целым числом,
	 * потому что объект может загружаться по нестандартному ключу
	 * (с указанием этого ключа параметром $field).
	 * Так же, и первичный ключ может не быть целым числом (например, при загрузке валют).
	 */
	df_assert($id);
	if (!is_null($field)) {
		df_param_string($field, 2);
	}
	/** @var Mage_Core_Model_Abstract|null $result */
	$result = is_string($model) ? df_model($model) : $model;
	df_assert($result instanceof Mage_Core_Model_Abstract);
	$result->load($id, $field);
	if (!$result->getId()) {
		if (!$throwOnError) {
			$result = null;
		}
		else {
			df_error(
				'Система не нашла в базе данных объект класса «%s» с идентификатором «%d».'
				,get_class($result)
				,$id
			);
		}
	}
	if (!is_null($result)) {
		/** @var mixed $modelId */
		$modelId = is_null($field) ? $result->getId() : $result->getData($field);
		// Обратите внимание, что мы намеренно используем !=, а не !==
		if ($id != $modelId) {
			if (!$throwOnError) {
				$result = null;
			}
			else {
				df_error(
					'При загрузке из базы данных объекта класса «%s» произошёл сбой: '
					.' идентификатор объекта должен быть равен «%s», а вместо этого равен «%s».'
					,get_class($result)
					,$id
					,$modelId
				);
			}
		}
	}
	return $result;
}

/**
 * В качестве параметра $modelClass можно передавать:
 * 1) класс модели в стандартном формате
 * 2) класс модели в формате Magento
 * @param string $modelClass
 * @param array(string => mixed) $parameters [optional]
 * @return Mage_Core_Model_Abstract
 * @throws Exception
 */
function df_model($modelClass = '', $parameters = array()) {
	/**
	 * Удаление df_param_string
	 * ускорило загрузку главной страницы на эталонном тесте
	 * с 1.501 сек. до 1.480 сек.
	 */
	/** @var Mage_Core_Model_Abstract $result */
	$result = null;
	try {
		$result = Mage::getModel($modelClass, $parameters);
		if (!is_object($result)) {
			df_error('Не найден класс «%s»', $modelClass);
		}
		/**
		 * Обратите внимание, что @uses Mage::getModel()
		 * почему-то не устанавливает поле @see Varien_Object::_hasModelChanged в true.
		 * Мы же ранее устанавливали этот флаг в данной функции @see df_model(),
		 * однако теперь это делаем более эффективным способом:
		 * в @see Df_Core_Model::_construct().
		 * Обратите внимание, что у нас после недавнего рефакторинга (январь 2014 года)
		 * большинство моделей теперь содаётся через new, а не через @see df_model(),
		 * поэтому установка флага в @see df_model() теперь не только неэффективна, но и некорректна.
		 */
	}
	catch (Exception $e) {
		Mage::logException($e);
		/** @var array $bt */
		$bt = debug_backtrace();
		/** @var array $caller */
		$caller = dfa($bt, 1);
		/** @var string $className */
		$className = dfa($caller, 'class');
		/** @var string $methodName */
		$methodName = dfa($caller, 'function');
		/** @var string $methodNameWithClassName */
		$methodNameWithClassName = implode('::', array($className, $methodName));
		df_error(
			"%method%[%line%]\nНе могу создать модель класса «%modelClass%»."
			."\nСообщение системы: «%message%»"
			,array(
				'%method%' => $methodNameWithClassName
				,'%line%' => dfa(dfa($bt, 0), "line")
				,'%modelClass%' => $modelClass
				,'%message%' => df_ets($e)
			)
		);
	}
	return $result;
}

/**
 * 2015-04-09
 * В качестве параметра $modelClass можно передавать:
 * 1) класс модели в стандартном формате
 * 2) класс модели в формате Magento
 * @param string $modelClass
 * @param array(string => mixed) $parameters [optional]
 * @return Mage_Core_Model_Abstract
 * @throws Exception
 */
function df_model_insert($modelClass = '', $parameters = array()) {
	/** @var Mage_Core_Model_Abstract $result */
	$result = df_model($modelClass, $parameters);
	/**
	 * Обратите внимание, что конструктор @uses Varien_Object::__construct()
	 * не помечает объект как изменённый даже при передаче в конструктор параметров.
	 */
	$result->setDataChanges(true);
	$result->save();
	return $result;
}