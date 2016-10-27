<?php
namespace Df\Shipping\Config\Backend\Validator\Strategy;
use Df\Shipping\Origin as O;
class Origin extends \Df\Shipping\Config\Backend\Validator\Strategy {
	/**
	 * @override
	 * @return bool
	 */
	public function validate() {return $this->strategy()->validate();}

	/**
	 * @used-by \Df\Shipping\Config\Backend\Validator\Strategy\Origin\SpecificCountry::validate()
	 * @return O
	 */
	protected function origin() {return dfc($this, function() {
		/** @var string|int $region */
		$region = $this->p('region_id');
		/** @var bool $hasId */
		$hasId = df_check_integer($region);
		return O::i([
			O::P__CITY => $this->p('city')
			,O::P__COUNTRY_ID =>
				$this->p('country_id')
			,O::P__POSTAL_CODE =>
				$this->p('postcode')
			,O::P__REGION_ID => $hasId ? df_nat0($region) : null
			,O::P__REGION_NAME => $hasId ? null : $region
		]);
	});}

	/**
	 * @param string $name
	 * @param string $d [optional]
	 * @return string
	 */
	private function p($name, $d = '') {return
		$this->store()->getConfig("shipping/origin/{$name}") ?: $d
	;}

	/**
	 * У стратегии тоже есть стратегии
	 * @return $this
	 */
	private function strategy() {return dfc($this, function() {return df_ic(
		$this->getBackend()->getFieldConfigParam('df_origin_validator'), __CLASS__, $this->getData()
	);});}

}