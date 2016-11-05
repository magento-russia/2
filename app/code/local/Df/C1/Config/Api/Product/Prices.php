<?php
namespace Df\C1\Config\Api\Product;
class Prices extends \Df\C1\Config\Api\Cml2 {
	/** @return string */
	public function getMain() {return $this->v('main');}

	/**
	 * @used-by \Df\C1\Cml2\Import\Data\Entity\PriceType::getCustomerGroup()
	 * @param string $названиеТиповогоСоглашения
	 * @return \Df_Customer_Model_Group|null
	 */
	public function getCustomerGroup($названиеТиповогоСоглашения) {return
		dfa($this->_map(), $названиеТиповогоСоглашения)
	;}

	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_1c/product__prices/';}

	/** @return array(string => Df_Customer_Model_Group) */
	private function _map() {return dfc($this, function() {return $this->map(
		'map'
		, \Df\C1\Config\MapItem\PriceType::class
		/** @uses \Df\C1\Config\MapItem\PriceType::getCustomerGroup() */
		, 'getCustomerGroup'
		/** @uses \Df\C1\Config\MapItem\PriceType::getНазваниеТиповогоСоглашения() */
		, 'getНазваниеТиповогоСоглашения'
	);});}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}