<?php
/**
 * KumbiaPHP Web & アプリケーションフレームワーク
 *
 * LICENSE
 *
 * このソースファイルは、同梱されている LICENSE ファイルに記載の
 * New BSD License の条件に従います。
 *
 * @category   Kumbia
 * @package    Core
 *
 * @copyright  Copyright (c) 2005 - 2023 KumbiaPHP Team (http://www.kumbiaphp.com)
 * @license    https://github.com/KumbiaPHP/KumbiaPHP/blob/master/LICENSE   New BSD License
 */

// @see Util
require CORE_PATH.'kumbia/util.php';

/**
 * クラスのオートロードを行う
 */
function kumbia_autoload($class)
{
    // 読み込みを最適化するために、よく使うクラスを事前に定義
    static $classes;
    $classes ??= [
        'ActiveRecord'    => APP_PATH.'libs/active_record.php',
        'Load'            => CORE_PATH.'kumbia/load.php',
        'KumbiaException' => CORE_PATH.'kumbia/kumbia_exception.php',
        'KumbiaRouter'    => CORE_PATH.'kumbia/kumbia_router.php',
        'KumbiaFacade'    => CORE_PATH.'kumbia/kumbia_facade.php'
    ];

    if (isset($classes[$class])) {
        include $classes[$class];
        return;
    }
    // 名前空間を含む場合は PSR-0 形式として扱う
    if (str_contains($class, '\\')) {
        kumbia_autoload_vendor($class);
        return;
    }
    // レガシーアプリケーション向けの特別対応
    if ($class === 'Flash') {
        kumbia_autoload_helper('Flash');
        return;
    }

    // クラス名を小文字に変換
    $sclass = Util::smallcase($class);
    if (is_file(APP_PATH."models/$sclass.php")) {
        include APP_PATH."models/$sclass.php";
        return;
    }
    if (is_file(APP_PATH."libs/$sclass.php")) {
        include APP_PATH."libs/$sclass.php";
        return;
    }
    if (is_file(CORE_PATH."libs/$sclass/$sclass.php")) {
        include CORE_PATH."libs/$sclass/$sclass.php";
        return;
    }
    // PEAR や Zend Framework 1 など、外部ライブラリのクラスである可能性がある場合
    kumbia_autoload_vendor($class);
}

/**
 * vendor ディレクトリ内のクラスを PSR-0 形式でオートロードする
 */
function kumbia_autoload_vendor($class): void
{
    // PSR-0 形式のオートロード
    $psr0 = dirname(APP_PATH, 2).'/vendor/'.str_replace(['_', '\\'], DIRECTORY_SEPARATOR, $class).'.php';
    if (is_file($psr0)) {
        include $psr0;
    }
}

/**
 * ヘルパークラスのオートロード
 */
function kumbia_autoload_helper($class): void
{
    $sclass = Util::smallcase($class);
    if (is_file(APP_PATH."extensions/helpers/$sclass.php")) {
        include APP_PATH."extensions/helpers/$sclass.php";
        return;
    }
    if (is_file(CORE_PATH."extensions/helpers/$sclass.php")) {
        include CORE_PATH."extensions/helpers/$sclass.php";
    }
}

// オートローダーを登録
spl_autoload_register('kumbia_autoload');
