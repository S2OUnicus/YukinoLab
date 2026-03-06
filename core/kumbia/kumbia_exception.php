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
 *
 * @copyright  Copyright (c) 2005 - 2023 KumbiaPHP Team (http://www.kumbiaphp.com)
 * @license    https://github.com/KumbiaPHP/KumbiaPHP/blob/master/LICENSE   New BSD License
 */

/**
 * 例外処理を行うメインクラス
 *
 * @category   Kumbia
 */
class KumbiaException extends Exception
{
    /**
     * 例外発生時に使用するエラービュー名
     *
     * @var string
     */
    protected $view = 'exception';

    /**
     * 404 エラーとして扱うビュー名一覧
     *
     * @var array
     */
    protected static $view404 = ['no_controller', 'no_action', 'num_params', 'no_view'];

    /**
     * 例外テンプレートのパス
     *
     * @var string
     */
    protected $template = 'views/templates/exception.phtml';

    /**
     * コンストラクタ
     *
     * @param string $message エラーメッセージ
     * @param string $view    使用するビュー名
     */
    public function __construct(string $message = '', string $view = 'exception')
    {
        $this->view = $view;
        parent::__construct($message);
    }

    /**
     * キャッチされていない例外を処理する
     *
     * @param Exception|KumbiaException $e
     * 
     * @return void
     */
    public static function handleException($e)
    {
        self::setStatus($e);

        // 本番環境、または信頼されていない IP からのアクセスの場合
        if (PRODUCTION || self::untrustedIp()) {
            self::cleanBuffer();
            include APP_PATH.'views/_shared/errors/404.phtml';

            return;
        }
        // 開発環境＋信頼された IP の場合は詳細情報を表示
        self::showDev($e);
    }

    /**
     * アクセス元 IP がローカルホスト・信頼済み IP でないかを判定する
     *
     * @return bool true: 信頼されていない / false: 信頼されている
     */
    private static function untrustedIp(): bool
    {
        $trusted = ['127.0.0.1', '::1']; // ローカルホスト IP
        // 古いアプリケーション向けの設定ファイルが存在するか確認
        if (is_file(APP_PATH.'config/exception.php')) {
            $trusted = array_merge($trusted, (array) Config::get('exception.trustedIp'));
        }

        return !in_array($_SERVER['REMOTE_ADDR'], $trusted);
    }

    /**
     * 開発時用の例外画面を表示する
     *
     * @param Exception|KumbiaException $e
     *
     * @return void
     */
    private static function showDev($e)
    {
        $data = Router::get();
        // 出力に用いる値をサニタイズ
        array_walk_recursive($data, function (&$value) {
            $value = htmlspecialchars($value, ENT_QUOTES, APP_CHARSET);
        });
        extract($data, EXTR_OVERWRITE);

        // ヘルパーのオートロードを登録
        spl_autoload_register('kumbia_autoload_helper', true, true);

        $Controller = Util::camelcase($controller);
        ob_start();

        $view = $e instanceof self ? $e->view : 'exception';
        $tpl  = $e instanceof self ? $e->template : 'views/templates/exception.phtml';
        // REST 利用時のアクション名の問題を修正
        $action = $e->getMessage() ?: $action;
        $action = htmlspecialchars($action, ENT_QUOTES, APP_CHARSET);

        include CORE_PATH."views/errors/{$view}.phtml";

        $content = ob_get_clean();
        self::cleanBuffer();
        include CORE_PATH.$tpl;
    }

    /**
     * 出力バッファをクリアする
     * 開いているすべてのバッファを終了させる
     */
    private static function cleanBuffer()
    {
        while (ob_get_level()) {
            ob_end_clean();
        }
    }

    /**
     * HTTP エラーステータスコードを設定する
     *
     * @param Exception $e
     * @return void
     */
    private static function setStatus($e)
    {
        // 自前の KumbiaException かつ 404 対象ビューの場合は 404
        if ($e instanceof self && in_array($e->view, self::$view404)) {
            http_response_code(404);
            return;
        }
        // それ以外は 500 とする
        http_response_code(500);
    }
}
