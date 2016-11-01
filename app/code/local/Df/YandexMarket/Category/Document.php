<?php
namespace Df\YandexMarket\Category;
class Document extends \Df_Core_Model {
	/** @return string[][] */
	private function _rows() {return dfc($this, function() {
		/** @var string[][] $result  */
		$result = [];
		\Df_Core_Lib::load('Excel');
		$this->downloadFile();
		/** @var \PHPExcel_Worksheet $sheet */
		$sheet = \PHPExcel_IOFactory::load($this->path())->getSheet(0);
		$highestRow = $sheet->getHighestRow();
		$highestColumn = $sheet->getHighestColumn();
		for ($rowIndex = 1; $rowIndex <= $highestRow; $rowIndex++){
			/** @var int $rowIndex */
			/** @var string[] $row */
			$row =
				df_first(
					$sheet->rangeToArray(
						$pRange = 'A' . $rowIndex . ':' . $highestColumn . $rowIndex
						,$nullValue = ''
						,$calculateFormulas = false
						,$formatData = false
						,$returnCellRef = false
					)
				)
			;
			$result[]= $row;
		}
		return $result;
	});}

	/** @return void */
	private function downloadFile() {
		/** @var string $fileUrl */
		$fileUrl = df_cfgr()->yandexMarket()->other()->getCategoriesReferenceBookUrl();
		/** @var string|bool $contents */
		$contents = @file_get_contents($fileUrl);
		if (false === $contents) {
			/** @var string $fileUrl */
			$fileUrl = 'http://download.cdn.yandex.net/support/ru/partnermarket/files/market_categories.xls';
			$contents = @file_get_contents($fileUrl);
			if (false === $contents) {
				/**
				 * Вероятно, Яндекс.Маркет поменял адрес документа.
				 * Такое уже было:
				 * http://magento-forum.ru/topic/4121/
				 *
				 * 2015-09-15
				 * Обновил адрес на новый:
				 * http://download.cdn.yandex.net/support/ru/partnermarket/files/market_categories.xls
				 */
				df_error(
					strtr(
						'Не могу найти справочник категорий товарных предложений по адресу '
						.'<a href="{categories-url}">{categories-url}</a>'
						.'<br/>Вероятно, Яндекс.Маркет сменил адрес этого справочника.'
						.'<br/>Вы можете вручную указать адрес этого справочника в административном разделе'
						.' «Система» → «Настройки» → «Российская сборка» → «Яндекс.Маркет» → «Другое»'
						.' → «<a href="{settings-url}">Веб-адрес справочника категорий товарных предложений</a>».'
						.'<br/>Также, сообщите о факте смены Яндекс.Маркетом веб-адреса'
						. ' своего справочника категорий товарных предложений на форуме Российской сборки Magento'
						. ' в <a href="http://magento-forum.ru/forum/270/">разделе модуля «Яндекс.Маркет»</a>.'
						,array(
							'{categories-url}' =>
								df_cfgr()->yandexMarket()->other()->getCategoriesReferenceBookUrl()
							,'{settings-url}' =>
								df_mage()->adminhtmlHelper()->getUrl(
									'system_config/edit/section/df_yandex_market'
								)
						)
					)
				);
			}
		}
		df_file_put_contents($this->path(), $contents);
	}
	
	/** @return string */
	private function path() {return \Mage::getBaseDir('var') . '/rm/yandex.market/categories.xls';}

	/** @return self */
	public static function rows() {return dfcf(function() {return (new self)->_rows();});}
}