<?php
/**
 * KumbiaPHP web & app Framework.
 *
 * ライセンス
 *
 * このソースファイルは、同梱されている LICENSE ファイルに記載された
 * New BSD ライセンスの条件に従います。
 *
 * © 2005 - 2023 KumbiaPHP Team (http://www.kumbiaphp.com)
 * @license    https://github.com/KumbiaPHP/KumbiaPHP/blob/master/LICENSE   New BSD License
 */

/**
 * このセクションでは実行環境を準備します。
 * ここで行っている設定は、サーバー／PHP 側の設定から
 * 行うこともできます。サーバー設定で対応できない場合は、
 * 以下の行のコメントを外して利用してください。
 */

// *ロケール*
//setlocale(LC_ALL, 'es_ES');

// *タイムゾーン*
//ini_set('date.timezone', 'America/New_York');

/**
 * @TODO
 * このセクションを要確認
 */
const APP_CHARSET = 'UTF-8';

/*
 * アプリケーションが本番環境かどうかを
 * index.php から直接指定します。
 *
 * 【警告】
 * production=false から production=true に変更した場合、
 * メタデータを更新するために、アプリケーションの
 * キャッシュディレクトリ (/app/tmp/cache/*) 内のファイルを
 * 削除する必要があります。
 */
const PRODUCTION = false;

/*
 * エラーを表示したい場合はコメントを外してください。
 */
//error_reporting(E_ALL ^ E_STRICT);ini_set('display_errors', 'On');

/*
 * APP_PATH を定義します。
 *
 * APP_PATH:
 * - アプリケーションディレクトリへのパス（デフォルトは app ディレクトリ）
 * - アプリケーションのファイルを読み込む際に使用されるパスです。
 * - 本番環境では const を使って手動で設定することを推奨します。
 */
define('APP_PATH', dirname(__DIR__).'/app/');
//const APP_PATH = '/path/to/app/';

/*
 * CORE_PATH を定義します。
 *
 * CORE_PATH:
 * - Kumbia のコアを含むディレクトリへのパス（デフォルトは core ディレクトリ）
 * - 本番環境では const を使って手動で設定することを推奨します。
 */
define('CORE_PATH', dirname(APP_PATH, 2).'/core/');
//const CORE_PATH = '/path/to/core/';

/*
 * PUBLIC_PATH を定義します。
 *
 * PUBLIC_PATH:
 * - コントローラやアクションへのリンク URL を生成するためのベースパス
 * - このパスは、クライアント側（ブラウザ）からアクセスするための URL を
 *   Kumbia が生成する際の基準となり、Web サーバの DOCUMENT_ROOT からの
 *   相対パスになります。
 *
 *  本番環境では、この定数を手動で設定することを推奨します。
 */
define('PUBLIC_PATH', substr($_SERVER['SCRIPT_NAME'], 0, -9)); // - 'index.php' (9文字)

/**
 * 本番環境では上の define をコメントアウトし、
 * 代わりに下記のように const を使うことを推奨します。
 * '/'          ドメイン直下（推奨）
 * '/carpeta/'  サブディレクトリ配下
 * 'https://www.midominio.com/'  固定ドメインを使用する場合
 */
//const PUBLIC_PATH = '/';

/**
 * PATH_INFO を使って URL を取得する。
 */
$url = $_SERVER['PATH_INFO'] ?? '/';

/**
 * $_GET['_url'] を使って URL を取得する場合はこちらを使用。
 * この場合、.htaccess の設定も合わせて変更する必要があります。
 */
//$url = $_GET['_url'] ?? '/';

/**
 * ブートストラップを読み込む。
 * デフォルトではコアのブートストラップを使用します。
 *
 * @see Bootstrap
 */
//require APP_PATH . 'libs/bootstrap.php'; // アプリ側の bootstrap
require CORE_PATH.'kumbia/bootstrap.php'; // コア側の bootstrap
