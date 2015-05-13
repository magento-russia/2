<?php
/**
 * Оказывается, на Unix-подобном сервере (в частности, я заметил на Debian)
 * mb_substr работает в 21000 раз быстрее, чем iconv_substr:
 * http://evertpot.com/iconv_substr-vs-mbstring_substr/
 *
 * В моей практике это привело к 2-кратному замедлению загрузки товарной страницы
 * http://intex-optom.com/hth/hthbriquette25.html
 * (товар «hth BRIQUETTE 7G Хлор в пластинках по 7г 25кг»)
 * в сравнении со другой товарной страницей
 * http://intex-optom.com/bed3/nasos-intex-quick-fill-220v-68620.html
 * (товар «Насос ручной Hi-Output Hand Pump Intex 68615»).
 *
 * Для первой товарной страницы тормозит, в частности, программный код обработки товарного описания
 * (соответствующий товар имеет длинное описание):
 * @see Mage_Catalog_Block_Product_View::_prepareLayout():
 * $headBlock->setDescription(Mage::helper('core/string')->substr($product->getDescription(), 0, 255));
 *
 * Оказалось, что вызов @see Mage_Core_Helper_String::substr() на сервере Debian в тысячи раз медленнее,
 * чем тот же вызов на локальном веб-сервере с Windows.
 * Причина тормозов: на Unix-подобном сервере
 * функции PHP iconv_* по умолчанию реализованы через библиотеку glibc
 * (так запрограммировано в ext/iconv/config.m4 исходного кода PHP),
 * а на Windows — через библиотеку libiconv.
 *
 * Ускорить работу системы на Unix-подобном сервере можно двояко:
 * 1) перекомпилировав PHP таким образом,
 * чтобы для функций PHP iconv_* использовалась библиотека libiconv вместо glibc.
 * Это решение — оптимальное, но может быть сложновато для конкретного администратора
 * и для конкретных условий (вдруг будут проблемы со сборкой PHP для конкретной Unix-подобной среды).
 * В целом, это реализуется так:
 * 1.1) качаем, компилируем и устанавливаем libiconv: http://www.gnu.org/software/libiconv/
		cd $(mktemp -d)
		wget http://ftp.gnu.org/pub/gnu/libiconv/libiconv-1.14.tar.gz
		tar -zvxf libiconv-1.14.tar.gz
		cd libiconv-1.14
		./configure --prefix=/usr/local
		make
		make install
 * Обратите внимание на команду ./configure --prefix=/usr/local
 * Префикс лучше делать именно таким!
 * При использовании других префиксов я заметил глюки при компиляции PHP.
 *
 *
 * 1.2) Качаем исходные коды PHP.
 *
 * 1.3) Редактируем ext/iconv/config.m4
 * Надо удалить оттуда следующий кусок:
		AC_MSG_CHECKING([if iconv is glibc's])
		AC_TRY_LINK([#include <gnu/libc-version.h>],[gnu_get_libc_version();],
		[
		  AC_MSG_RESULT(yes)
		  iconv_impl_name="glibc"
		],[
		  AC_MSG_RESULT(no)
		])
 * Как раз этот кусок назначает для функций iconv_* библиотеку glibc, а нам это не нужно.
 *
 * 1.4) Затем из корня скачанного дистрибутива PHP выполняем команды:
		rm -rf autom4te.cache
		autoconf
 *
 * 1.5) Затем собираем PHP вручную с ключами:
 	--with-iconv=/usr/local \
 	--with-iconv-dir=/usr/local	\

	Я использовал следующую команду:

./configure \
	--enable-bcmath \
	--enable-calendar \
	--enable-cli \
	--enable-dba \
	--enable-exif \
	--enable-fpm \
	--enable-ftp \
	--enable-gd-native-ttf \
	--enable-mbstring \
	--enable-opcache \
	--enable-pcntl \
	--enable-pdo=shared \
	--enable-soap \
	--enable-soap \
	--enable-sockets \
	--enable-wddx \
	--enable-zip \
	--with-bz2 \
	--with-config-file-path=/etc/php5/fpm/ \
	--with-config-file-scan-dir=/etc/php5/fpm/conf.d/ \
	--with-curl=shared \
	--with-fpm-group=www-data \
	--with-fpm-user=www-data \
	--with-freetype-dir=/usr \
	--with-gd=shared \
	--with-gettext \
	--with-iconv=/usr/local \
	--with-iconv-dir=/usr/local	\
	--with-jpeg-dir=/usr \
	--with-libdir=lib \
	--with-mcrypt=shared \
	--with-mhash \
	--with-mysql=shared \
	--with-mysqli=shared \
	--with-openssl \
	--without-mssql \
	--without-pi3web \
	--with-pcre-regex \
	--with-pdo-mysql=shared \
	--with-pdo-sqlite=shared \
	--with-pear \
	--with-png-dir=/usr \
	--with-tidy=shared \
	--with-xmlrpc=shared \
	--with-xsl=shared \
	--with-zlib > configure.log

 * 1.6) Выполняем make и make install.
 *
 * 1.7) Для Debian надо еще чуточку перенастроить PHP-FPM:
 * 1.7.1) mv /usr/local/sbin/php-fpm   /usr/local/sbin/php5-fpm
 * 1.7.2) в файле /etc/init.d/php5-fpm отредактировать строки:
 * PATH=/usr/local/sbin:/sbin:/usr/sbin:/bin:/usr/bin
 * DAEMON=/usr/local/sbin/$NAME
 *
 * 2) Как видно, первый путь может быть сложноват для администраторов.
 * Второй путь, который решает задачу частично —
 * то как раз перекрытие класса @see Mage_Core_Helper_String
 * и замена там функций iconv_* на mb_*.
 * Этот способ решает проблему лишь отчасти,
 * потому Magento использует функции iconv_* не только в классе @see Mage_Core_Helper_String,
 * но и в других местах, а особенно широко — через библиотеку Zend Framework.
 * Но лучше хоть так, чем ничего, когда администатор не в состоянии следовать по первому пути.
 */
class Df_Core_Helper_String extends Mage_Core_Helper_String {
	/**
	 * @override
	 * @param string $string
	 * @return int
	 */
	public function strlen($string) {return mb_strlen($string, self::ICONV_CHARSET);}

	/**
	 * @override
	 * @param string $haystack
	 * @param string $needle
	 * @param null $offset
	 * @return int|bool
	 */
	public function strpos($haystack, $needle, $offset = null) {
		return mb_strpos($haystack, $needle, $offset, self::ICONV_CHARSET);
	}

	/**
	 * @override
	 * @param string $string
	 * @param int $offset
	 * @param int|null $length [optional]
	 * @return string
	 */
	public function substr($string, $offset, $length = null) {
		$string = $this->cleanString($string);
		if (is_null($length)) {
			$length = $this->strlen($string) - $offset;
		}
		return mb_substr($string, $offset, $length, self::ICONV_CHARSET);
	}
}