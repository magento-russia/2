<?php
/**
 * Этот класс очень простой.
 * Просто унаследуйте Ваш класс от этого вместо Df_Core_Model_Abstract
 */
class Df_Core_Model_DestructableSingleton extends Df_Core_Model_Abstract {
	/**
	 * @override
	 * @return bool
	 */
	protected function isDestructableSingleton() {return true;}
}