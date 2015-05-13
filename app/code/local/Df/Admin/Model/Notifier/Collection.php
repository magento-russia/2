<?php
class Df_Admin_Model_Notifier_Collection extends Df_Varien_Data_Collection_Singleton {
	/**
	 * @override
	 * @return string
	 */
	protected function getItemClass() {return Df_Admin_Model_Notifier::_CLASS;}

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
	private function getClasses() {
		return Df_Core_Model_Config::s()->getStringNodes('df/admin/notifiers');
	}

	/** @return Df_Admin_Model_Notifier_Collection */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}