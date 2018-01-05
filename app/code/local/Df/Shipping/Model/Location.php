<?php
abstract class Df_Shipping_Model_Location extends Df_Core_Model_Abstract {
	/** @return string */
	abstract public function getRegion();

	/** @return bool */
	public function hasRegion() {return !!$this->getRegion();}

	/**
	 * @param string|string[] $name
	 * @return string|string[]
	 */
	public function normalizeName($name) {
		/** @var string|string[] $result */
		$result = null;
		/** @var bool $firstParamIsArray */
		$firstParamIsArray = is_array($name);
		/** @var mixed[] $arguments */
		$arguments = $firstParamIsArray ? $name : func_get_args();
		if ((1 < count($arguments)) || $firstParamIsArray) {
			$result = array_map(array($this, 'normalizeNameSingle'), $arguments);
		}
		else {
			$result = $this->normalizeNameSingle($name);
		}
		return $result;
	}

	/**
	 * @param string $name
	 * @param string[] $partsToRemove
	 * @param bool $isCaseSensitive [optional]
	 * @return string
	 */
	protected function cleanName($name, array $partsToRemove, $isCaseSensitive = false) {
		$name = $isCaseSensitive ? $name : $this->normalizeName($name);
		$partsToRemove = $isCaseSensitive ? $partsToRemove : $this->normalizeName($partsToRemove);
		return
			df_trim(
				strtr(
					$name
					,array_combine(
						$partsToRemove
						,array_fill(0, count($partsToRemove), '')
					)
				)
			)
		;
	}

	/** @return bool */
	protected function isRegionCleaningCaseSensitive() {return false;}

	/** @return string[] */
	protected function getRegionPartsToClean() {
		return array('край', 'область', 'республика', 'авт. округ', 'край', 'обл.', 'респ.');
	}

	/** @return array(string => string) */
	protected function getRegionReplacementMap() {
		return array(
			'Саха' => 'Саха (Якутия)'
			,'Северная Осетия' => 'Северная Осетия — Алания'
			,'Тыва' => 'Тыва (Тува)'
		);
	}

	/**
	 * @param string $regionName
	 * @return string
	 */
	protected function normalizeRegionName($regionName) {
		return
			$this->replaceInName(
				$this->cleanName(
					$regionName
					,$this->getRegionPartsToClean()
					,$this->isRegionCleaningCaseSensitive()
				)
				,$this->getRegionReplacementMap()
			)
		;
	}

	/**
	 * @param string $name
	 * @param array(string => string) $replacementMap
	 * @return string
	 */
	protected function replaceInName($name, array $replacementMap) {
		return
			strtr(
				$this->normalizeName($name)
				,array_combine(
					$this->normalizeName(array_keys($replacementMap))
					,$this->normalizeName(array_values($replacementMap))
				)
			)
		;
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * http://php.net/manual/en/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * http://3v4l.org/OipEQ
	 * 2018-01-05
	 * "Метод `Df_Shipping_Model_Location::normalizeNameSingle()` не должен быть приватным,
	 * потому что его использует (и перекрывает) метод `Df_Cdek_Model_Location::normalizeNameSingle()`":
	 * https://github.com/magento-russia/2/issues/13
	 * @used-by normalizeName()
	 * @used-by Df_Cdek_Model_Location::normalizeNameSingle()
	 * @see Df_Cdek_Model_Location::normalizeNameSingle()
	 * @param string $name
	 * @return string
	 */
	protected function normalizeNameSingle($name) {return strtr(mb_strtoupper($name), array('Ё' => 'Е'));}

	const _CLASS = __CLASS__;
}