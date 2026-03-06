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
 * @package    Router
 *
 * @copyright  Copyright (c) 2005 - 2023 KumbiaPHP Team (http://www.kumbiaphp.com)
 * @license    https://github.com/KumbiaPHP/KumbiaPHP/blob/master/LICENSE   New BSD License
 */

/**
 * フロントコントローラ用ルータークラス
 *
 * リクエストのルーティング（振り分け）を行います。
 * リクエストされた URL に関する情報
 * （モジュール、コントローラ、アクション、パラメータ等）を保持します。
 *
 * @category   Kumbia
 * @package    Router
 */
class Router
{

    /**
     * ルーターの各種情報を格納する静的配列
     *
     * @var array
     */
    protected static $vars = [
        // 'method'          => '',      // 使用された HTTP メソッド (GET, POST, ...)
        // 'route'           => '',      // URL から渡されたルート
        // 'module'          => '',      // 現在のモジュール名
        // 'controller'      => 'index', // 現在のコントローラ名（デフォルト: index）
        // 'action'          => 'index', // 現在のアクション名（デフォルト: index）
        // 'parameters'      => [],      // URL の追加パラメータ一覧
        // 'controller_path' => 'index'  // コントローラのパス
    ];

    /**
     * ルーターのデフォルト値を格納する静的配列
     * TODO: 定数化する
     *
     * @var array
     */
    protected static $default = [
        'module'          => '',       // 現在のモジュール名
        'controller'      => 'index',  // 現在のコントローラ名（デフォルト: index）
        'action'          => 'index',  // 現在のアクション名（デフォルト: index）
        'parameters'      => [],       // URL の追加パラメータ一覧
        'controller_path' => 'index'   // コントローラのパス
    ];

    /**
     * 使用するルータクラス名
     * @var string
     */
    protected static $router = 'KumbiaRouter';
    // デフォルトのルータクラス

    /**
     * Dispatcher によるルート実行が保留されているかどうか
     *
     * @var boolean
     */
    protected static $routed = false;

    /**
     * ルーターの基本的な初期処理
     *
     * @param string $url
     *
     * @throws KumbiaException
     * @return void
     */
    public static function init($url)
    {
        // セキュリティ上の確認（ディレクトリトラバーサル等の簡易チェック）
        if (stripos($url, '/../') !== false) {
            throw new KumbiaException("URL に対する不正アクセスの可能性があります: '$url'");
        }
        // TODO: 不正アクセスの可能性があれば IP や Referer をログに残す
        self::$default['route'] = $url;
        // 使用された HTTP メソッド
        self::$default['method'] = $_SERVER['REQUEST_METHOD'];
    }

    /**
     * 指定された URL を実行する
     *
     * @param string $url
     *
     * @throws KumbiaException
     * @return Controller
     */
    public static function execute($url)
    {
        self::init($url);
        // エイリアスとしてルータクラス名を取得
        $router = self::$router;
        $conf   = Config::get('config.application.routes');

        // config.ini で routes が有効になっている場合はルーティングを確認
        if ($conf) {
            /* ルータが有効 */
            /* 古いバージョンとの互換性のための処理 */
            if ($conf === '1') {
                $url = $router::ifRouted($url);
            } else {
                /* 別のルータクラスが指定されている場合 */
                $router = self::$router = $conf;
            }
        }

        // URL を分解してルーティング情報に変換
        self::$vars = $router::rewrite($url) + self::$default;

        // 現在のルートをディスパッチ
        return static::dispatch($router::getController(self::$vars));
    }

    /**
     * 現在のルートをディスパッチ（実行）する
     *
     * @param Controller $cont  実行対象のコントローラ
     *
     * @throws KumbiaException
     * @return Controller
     */
    protected static function dispatch($cont)
    {
        // initialize と before_filter を実行
        if ($cont->k_callback(true) === false) {
            return $cont;
        }

        if (method_exists($cont, $cont->action_name)) {
            // 予約メソッド k_callback を直接実行しようとした場合はエラー
            if (strcasecmp($cont->action_name, 'k_callback') === 0) {
                throw new KumbiaException('KumbiaPHP の予約メソッドを実行しようとしています');
            }

            // PHP 5.6 の可変長引数導入後は削除可能
            if ($cont->limit_params) {
                $reflectionMethod = new ReflectionMethod($cont, $cont->action_name);
                $num_params = count($cont->parameters);

                if ($num_params < $reflectionMethod->getNumberOfRequiredParameters() ||
                    $num_params > $reflectionMethod->getNumberOfParameters()) {

                    // パラメータ数エラー（メッセージはビュー側で処理）
                    throw new KumbiaException('', 'num_params');
                }
            }
        }

        // 対象アクションを実行
        call_user_func_array([$cont, $cont->action_name], $cont->parameters);

        // after_filter と finalize を実行
        $cont->k_callback();

        // 内部ルーティングが設定されている場合は再度実行
        self::isRouted();

        return $cont;
    }

    /**
     * 内部ルーティングをトリガーする
     *
     * @throws KumbiaException
     * @return void
     */
    protected static function isRouted()
    {
        if (self::$routed) {
            self::$routed = false;
            $router = self::$router;
            // 現在のルートを再ディスパッチ
            self::dispatch($router::getController(self::$vars));
        }
    }

    /**
     * ルーターの属性値、またはすべての属性配列を取得する
     *
     * 使用例:
     * <code>Router::get()</code>  // すべての情報を取得
     *
     * 使用例:
     * <code>Router::get('controller')</code>  // コントローラ名のみ取得
     *
     * @param string $var (任意) 取得したい属性名:
     *                     route, module, controller, action, parameters, routed 等
     *
     * @return array|string 指定した属性の値、またはすべての属性配列
     */
    public static function get($var = '')
    {
        return ($var) ? static::$vars[$var] : static::$vars;
    }

    /**
     * 内部、または外部からルーティング情報を上書きする
     *
     * @param array   $params $vars と同形式の配列（module, controller, action, params など）
     * @param boolean $intern 内部リダイレクトかどうか
     *
     * @return void
     */
    public static function to(array $params, $intern = false)
    {
        if ($intern) {
            self::$routed = true;
        }
        static::$vars = $params + self::$default;
    }
}
