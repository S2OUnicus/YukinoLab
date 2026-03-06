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

/**
 * このスクリプトは Workerman 上で KumbiaPHP を読み込み・実行します
 *
 * @category   Kumbia
 * @package    Core
 */

require_once CORE_PATH.'../../autoload.php';

use Workerman\Timer;

// 出力バッファを開始
//ob_start();

// Kumbia のバージョン情報
require CORE_PATH.'kumbia/kumbia_version.php';

/**
 * ExceptionHandler の初期化 TODO
 * @see KumbiaException
 *
 * @return void
 */
// set_exception_handler(function($e) {
//     KumbiaException::handleException($e);
// });

// @see Autoload
require CORE_PATH.'kumbia/autoload.php';
// @see Config
require CORE_PATH.'kumbia/config.php';

// @see Router
require CORE_PATH.'kumbia/router.php';
require CORE_PATH.'kumbia/static_router.php';
// @see Controller
require APP_PATH.'libs/app_controller.php';
// @see KumbiaView
require APP_PATH.'libs/view.php';
// リクエストを実行
// Dispatch してビューをレンダリング

function kumbiaSend() {
    ob_start();ob_start();
    View::render(StaticRouter::execute($_SERVER['REQUEST_URI']));
    header(WorkerTimer::$date);
    if (ob_get_level() > 1) {
        ob_end_flush();
    }
    return ob_get_clean();
}

class WorkerTimer
{
    public static $date;
    const DATE_FORMAT = 'D, d M Y H:i:s \G\M\T';

    public static function init()
    {
        self::$date = 'Date: '.gmdate(self::DATE_FORMAT);
        Timer::add(1, function() {
            WorkerTimer::$date = 'Date: '.gmdate(self::DATE_FORMAT);
        });
    }
}

function kumbiaInit() {
    WorkerTimer::init();
}
