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
 * @package    Console
 *
 * @copyright  Copyright (c) 2005 - 2023 KumbiaPHP Team (http://www.kumbiaphp.com)
 * @license    https://github.com/KumbiaPHP/KumbiaPHP/blob/master/LICENSE   New BSD License
 */

/**
 * KumbiaPHP 用コンソールスクリプト
 *
 * @category   Kumbia
 * @package    Console
 */
// CORE_PATH を定義（フレームワークのコアディレクトリ）
define('CORE_PATH', dirname(__DIR__) . '/');

/**
 * コンソールクラスを読み込み
 *
 * @see Console
 */
require CORE_PATH . 'kumbia/console.php';

// コンソールディスパッチャを実行
Console::dispatch($argv);
