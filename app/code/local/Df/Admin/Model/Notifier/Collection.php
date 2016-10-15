<?php
class Df_Admin_Model_Notifier_Collection extends Df_Varien_Data_Collection_Singleton {
	/**
	 * @override
	 * @see Df_Varien_Data_Collection::itemClass()
	 * @used-by Df_Varien_Data_Collection::addItem()
	 * @return string
	 */
	protected function itemClass() {return Df_Admin_Model_Notifier::class;}

	/**
	 * @override
	 * @return void
	 */
	protected function loadInternal() {
		foreach ($this->getClasses() as $className) {
			/** @var string $className */
			/** @var Df_Admin_Model_Notifier $notifier */
			$notifier = df_model($className);
			df_assert($notifier instanceof Df_Admin_Model_Notifier);
			if ($notifier->needToShow()) {
				$this->addItem($notifier);
			}
		}
	}

	/** @return array(string => string) */
	private function getClasses() {return df_config_a('df/admin/notifiers');}

	/** @return Df_Admin_Model_Notifier_Collection */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}