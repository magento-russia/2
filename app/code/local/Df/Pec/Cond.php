<?php
// 2016-10-31
namespace Df\Pec;
class Cond extends \Df\Shipping\Request {
	/**
	 * 2016-10-31
	 * @return void
	 * @throws \Exception
	 */
	private function _collect() {
		/** @var mixed[] $ra */
		$ra = $this->response()->json();
		try {
			/** @var float[] $add */
			$add = dfa($ra, 'ADD', array());
			df_assert_array($add);
			/** @var float $addRate */
			$addRate = df_float(dfa($add, 2));
			/** @var float[] $take */
			$take = dfa($ra, 'take', array());
			df_assert_array($take);
			/** @var float $takeRate */
			$takeRate = df_float(dfa($take, 2));
			/** @var float[] $deliver */
			$deliver = dfa($ra, 'deliver', array());
			df_assert_array($deliver);
			/** @var float $deliverRate */
			$deliverRate = df_float(dfa($deliver, 2));
			/** @var array|null $avia */
			$avia = dfa($ra, 'avia');
			if (!is_null($avia)) {
				df_assert_array($avia);
				/** @var float $airBaseRate */
				$airBaseRate = df_float(dfa($avia, 2));
				$this->cl()->rate(
					$airBaseRate + $takeRate + $deliverRate  + $addRate, 2, 3, 'air', 'воздушная'
				);
			}
			/** @var float[]|null $auto */
			$auto = dfa($ra, 'auto');
			if (!is_null($auto)) {
				df_assert_array($auto);
				/** @var float $airBaseRate */
				$autoBaseRate = df_float(dfa($auto, 2));
				df_assert_float($autoBaseRate);
				/** @var float $methodAutoRate */
				$methodAutoRate = $autoBaseRate + $takeRate + $deliverRate + $addRate;
				/** @var string $deliveryTimeAsText */
				$deliveryTimeAsText = dfa($ra, 'periods');
				df_assert_string($deliveryTimeAsText);
				/** @var string $deliveryTimeAsTextWithoutTags */
				$deliveryTimeAsTextWithoutTags = strip_tags($deliveryTimeAsText);
				df_assert_string($deliveryTimeAsTextWithoutTags);
				/** @var int $deliveryTime */
				$deliveryTime =
					df_preg_match_int(
						'#Количество суток в пути: (\d+)#u'
						, $deliveryTimeAsTextWithoutTags
						, false
					)
				;
				/**
				 * Иногда ПЭК не указывает сроки, но тарифы выдаёт.
				 * Заметил такое для перевозки Москва => Москва (которая смысла не имеет),
				 * но вдруг ПЭК так себя ведёт и в других ситуациях?
				 */
				$this->cl()->rate(
					$methodAutoRate
					, $deliveryTime ?: null
					, $deliveryTime ? $deliveryTime + 3 : null
					, 'ground'
					, 'наземная'
				);
			}
		}
		catch (\Exception $e) {
			df_notify(
				"Получили следующий неожиданный ответ от ПЭК:\n%s"
				, df_print_params($ra)
			);
			df_error($e);
		}
	}

	/**
	 * 2016-10-31
	 * @override
	 * @see \Df\Shipping\Request::method()
	 * @return string
	 */
	protected function method() {return \Zend_Http_Client::POST;}

	/**
	 * 2016-10-31
	 * @override
	 * @see \Df\Shipping\Request::post()
	 * @return string
	 */
	protected function post() {return dfc($this, function() {return [
		'deliver' => [
			'gidro' => df_01($this->s()->needCargoTailLoaderAtDestination())
			,'moscow' => 0
			,'speed' => 0
			,'tent' => df_01($this->s()->needRemoveAwningAtDestination())
			,'town' => $this[self::$P__DEST]
		]
		,'take' => [
			'gidro' => df_01($this->s()->needCargoTailLoaderAtOrigin())
			,'moscow' => $this->s()->moscowReceptionPoint()
			,'speed' => 0
			,'tent' => df_01($this->s()->needRemoveAwningAtOrigin())
			,'town' => $this[self::$P__ORIG]
		]
		,'fast' => 0
		,'fixedbox' => df_01($this->s()->needRigidContainer())
		,'night' => df_01($this->s()->needOvernightDelivery())
		,'places' => $this->places()
		,'plombir' => $this->s()->getSealCount()
		,'strah' => $this->cl()->declaredValue()
	];});}

	/**
	 * 2016-10-31
	 * @override
	 * @see \Df\Shipping\Request::uri()
	 * @used-by \Df\Shipping\Request::zuri()
	 * @return string
	 */
	protected function uri() {return 'https://calc.pecom.ru/bitrix/components/pecom/calc/ajax.php';}

	/**
	 * 2016-10-31
	 * @return Collector
	 */
	private function cl() {return $this[self::$P__COLLECTOR];}

	/**
	 * 2016-10-31
	 * @return array(array(float|int))
	 */
	private function places() {
		/** @var array(array(float|int)) $result */
		$result = [];
		foreach ($this->rr()->getQuoteItemsSimple() as $quoteItem) {
			/** @var \Mage_Sales_Model_Quote_Item $quoteItem */
			/** @var \Df_Catalog_Model_Product $product */
			$product = $this->rr()->getProductsWithDimensions()->getItemById(
				$quoteItem->getProductId()
			);
			df_assert($product);
			/** @var float $weight */
			$weight = $product->getWeightInKg();
			/**
			 * 2015-01-25
			 * Обратите внимание,
			 * что для некоторых типов (например, виртуальных и скачиваемых) нормально не иметь вес.
			 * Передавать такие товары в службу доставку не нужно.
			 * Обратите также внимание,
			 * что если товар по своему типу (например, простой или настраиваемый) способен иметь вес,
			 * но информация о весе данного в базе данных интернет-магазина остутствует,
			 * то @see Df_Catalog_Model_Product::getWeight()
			 * вернёт не нулевой вес, а то значение, которое администратор интернет-магазина
			 * указал в качестве веса по умолчанию.
			 */
			if ($weight) {
				/** @var float[] $dim */
				$dim = df_length()->inMetres(
					$product->getLength(), $product->getWidth(), $product->getHeight()
				);
				df_assert_array($dim);
				/** @var array(float|int) $place */
				$place = array_merge($dim, [
					array_product($dim)
					,$weight
					// габаритен ли груз?
					,1
					,df_01($this->s()->needRigidContainer())
				]);
				/** @var int $qty */
				$qty = \Df_Sales_Model_Quote_Item_Extended::i($quoteItem)->getQty();
				for ($index = 0; $index < $qty; $index++) {
					$result[]= $place;
				}
			}
		}
		return $result;
	}

	/**
	 * 2016-10-31
	 * @return \Df\Shipping\Rate\Request
	 */
	private function rr() {return $this->cl()->rr();}

	/**
	 * 2016-10-31
	 * @return Config\Area\Service
	 */
	private function s() {return $this->cl()->configS();}

	/**
	 * 2016-10-31
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__COLLECTOR, Collector::class)
			->_prop(self::$P__DEST, DF_V_STRING_NE)
			->_prop(self::$P__ORIG, DF_V_STRING_NE)
		;
	}

	/** @var string */
	private static $P__COLLECTOR = 'collector';
	/** @var string */
	private static $P__DEST = 'dest';
	/** @var string */
	private static $P__ORIG = 'orig';

	/**
	 * 2016-10-31
	 * @param Collector $cl
	 * @param string $orig
	 * @param string $dest
	 * @return void
	 */
	public static function collect(Collector $cl, $orig, $dest) {(new self([
		self::$P__COLLECTOR => $cl, self::$P__DEST => $dest, self::$P__ORIG => $orig
	]))->_collect();}
}