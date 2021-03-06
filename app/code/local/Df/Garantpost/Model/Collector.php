<?php
class Df_Garantpost_Model_Collector extends Df_Shipping_Model_Collector {
	/**
	 * @override
	 * @return Df_Garantpost_Model_Method[]
	 */
	protected function getMethods() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Garantpost_Model_Method[] $result */
			$result = array();
			foreach (parent::getMethods() as $method) {
				/** @var bool $isApplicable */
				$isApplicable = false;
				try {
					$isApplicable = $method->isApplicable();
					if ($isApplicable) {
						try {
							$method->getCost();
						}
						catch(Exception $e) {
							// Намеренно ничего не делаем.
							// Раньше тут происходила запись исключительной ситуации в системный журнал.
							// Но нам это не надо, потому что там исключительная ситуация всегда одна —
							// "Укажите город или хотя бы индекс".
							// В журнал это писать не надо, а в интерфейсе покупатель всё равно увидит
							// данное сообщение.
						}
					}
				}
				catch(Exception $e) {
					// Сюда мы попадаем, если способ неприменим.
					// Надо бы, конечно, сообщить посетителю о неприменимости, в том случае,
					// если соответствующую опцию включил администратор,
					// но пока неясно, как это сделать, не нарушая цикла.
				}
				if ($isApplicable || $method->needDisplayDiagnosticMessages()) {
					$result[]= $method;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
}