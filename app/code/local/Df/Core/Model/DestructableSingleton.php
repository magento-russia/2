<?php
class Df_Core_Model_DestructableSingleton extends Df_Core_Model {
	/**
	 * @override
	 * @see Df_Core_Model::isDestructableSingleton()
	 * @used-by __destruct()
	 * @used-by _construct()
	 * @return bool
	 */
	protected function isDestructableSingleton() {return true;}
}
