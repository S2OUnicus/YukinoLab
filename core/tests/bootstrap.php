<?php
/**
 * KumbiaPHP web & app Framework
 *
 * LICENSE
 *
 * このソースファイルは、同梱されている LICENSE ファイルに記載された
 * New BSD ライセンスの条件に従います。
 *
 * @category   Test        テスト
 * @package    Core        コア
 *
 * @copyright  Copyright (c) 2005 - 2023 KumbiaPHP Team
 * @license    https://github.com/KumbiaPHP/KumbiaPHP/blob/master/LICENSE   New BSD License
 */

// セッションが開始されていない場合は開始する
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// コアディレクトリへのパス定数
defined('CORE_PATH') || define('CORE_PATH', dirname(__DIR__) . '/');
// アプリケーションディレクトリへのパス定数（テスト用）
defined('APP_PATH') || define('APP_PATH', __DIR__ . '/');
// パブリックパス（ベース URL）
defined('PUBLIC_PATH') || define('PUBLIC_PATH', 'http://127.0.0.1/');

// KumbiaPHP のオートロード
require_once CORE_PATH.'kumbia/autoload.php';
// Composer のオートロード（外部ライブラリ）
require_once __DIR__.'/../../vendor/autoload.php';

// Kumbia のヘルパー用オートローダーを登録
spl_autoload_register('kumbia_autoload_helper', true, true);
