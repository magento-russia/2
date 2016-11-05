<?php
class Df_Adminhtml_Model_Translator_Controller extends Df_Core_Model {
	/**
	 * @param Mage_Adminhtml_Controller_Action $controller
	 * @return string
	 */
	public function getModuleName(Mage_Adminhtml_Controller_Action $controller) {
		/** @var string $controllerClass */
		$controllerClass = get_class($controller);
		if (!isset($this->{__METHOD__}[$controllerClass])) {
			/** @var string[] $classNameParts */
			$classNameParts = df_explode_class($controllerClass);
			if ('Mage' !== $classNameParts[0]) {
				$result = df_module_name($controllerClass);
			}
			else {
				$result = $classNameParts[0] . '_' . $classNameParts[2];
				/**
				 * Однако же, данного модуля может не существовать.
				 * Например, для адреса http://localhost.com:656/index.php/admin/system_design/
				 * алгоритм возвращает название несуществующего модуля «Mage_System».
				 *
				 * В таком случае возвращаемся к алторитму из первой ветки
				 * (по сути, для стандартного кода возвращаем «Mage_Adminhtml»)
				 */
				if (!df_module_enabled($result)) {
					$result = df_module_name($controllerClass);
				}
			}
			df_result_string($result);
			$this->{__METHOD__}[$controllerClass] = $result;
		}
		return $this->{__METHOD__}[$controllerClass];
	}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}