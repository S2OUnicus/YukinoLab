<?php
/**
 * KumbiaPHP Web & アプリケーションフレームワーク
 *
 * LICENSE
 *
 * このソースファイルは、同梱されている LICENSE ファイルに記載の
 * New BSD License の条件に従います。
 *
 * @category   View
 *
 * @copyright  Copyright (c) 2005 - 2023 KumbiaPHP Team (http://www.kumbiaphp.com)
 * @license    https://github.com/KumbiaPHP/KumbiaPHP/blob/master/LICENSE   New BSD License
 */

/**
 * ビューをレンダリングするクラス
 *
 * @category   View
 */
class KumbiaView
{
    /**
     * 出力コンテンツ
     *
     * @var string|null
     */
    protected static $_content;

    /**
     * レンダリング対象のビュー名
     *
     * @var string
     */
    protected static $_view;

    /**
     * 使用するテンプレート名
     *
     * @var string|null
     */
    protected static $_template = 'default';

    /**
     * コントローラによって生成されるレスポンスの種類
     * （xml などの拡張子として利用）
     *
     * @var string
     */
    protected static $_response;

    /**
     * ビューファイルのベースパス（この後ろにビュー名などが連結される）
     *
     * @var string
     */
    protected static $_path;

    /**
     * 現在のビューをキャッシュする時間（分）
     *
     * type: キャッシュ種別 (view, template)
     * time: キャッシュの有効時間
     * group: キャッシュのグループ名
     *
     * @var array
     */
    protected static $_cache = ['type' => false, 'time' => false, 'group' => false];

    /**
     * 現在のコントローラのデータ
     *
     * @var array
     */
    protected static $_controller;

    /**
     * ビューの初期化
     *
     * @param string $view ビュー名
     * @param string $path ビューのパス
     * @return void
     */
    public static function init($view, $path)
    {
        self::$_view = $view;
        self::$_path = $path.'/';
        //self::$init = true;
        // TODO init が実行された場合のデフォルト値を追加 (workerman, ngx-php,...)
    }

    /**
     * 表示する view と、必要に応じて template を変更する
     *
     * @param string|null $view     利用するビュー名（拡張子 .phtml は不要）
     * @param string|null $template 利用するテンプレート名（拡張子 .phtml は不要）
     *
     * @return void
     */
    public static function select($view, $template = '')
    {
        self::$_view = $view;

        // template が指定されているか確認
        if ($template !== '') {
            self::$_template = $template;
        }
    }

    /**
     * ビューで使用するテンプレートを設定する
     *
     * @param string|null $template 利用するテンプレート名（拡張子 .phtml は不要）
     *
     * @return void
     */
    public static function template($template)
    {
        self::$_template = $template;
    }

    /**
     * コントローラからのレスポンス種別を指定し、
     * その拡張子に対応する view を探す
     * 例: View::response('xml');
     * → views/controller/action.xml.phtml を探す。
     *
     * @param string      $response レスポンス種別
     * @param string|null $template 任意のテンプレート名（拡張子 .phtml なし）
     *
     * @return void
     */
    public static function response($response, $template = null)
    {
        self::$_response = $response;

        // template が指定されているか確認
        if ($template !== null) {
            self::$_template = $template;
        }
    }

    /**
     * ビューのパスを設定する
     *
     * @param string $path 拡張子 .phtml を除いたビューのパス
     *
     * @return void
     */
    public static function setPath($path)
    {
        self::$_path = $path.'/';
    }

    /**
     * ビューのパス（拡張子 .phtml を含む）を取得する
     *
     * @return string
     */
    public static function getPath()
    {
        if (self::$_response) {
            return self::$_path.self::$_view.'.'.self::$_response.'.phtml';
        }

        return self::$_path.self::$_view.'.phtml';
    }

    /**
     * KumbiaView の属性値を取得する
     *
     * @param string $atribute 属性名（template, response, path など）
     *
     * @return mixed
     */
    public static function get($atribute)
    {
        return self::${"_$atribute"};
    }

    /**
     * ビューまたはテンプレートのキャッシュ設定を行う
     *
     * @param string|null $time  キャッシュの有効時間
     * @param string      $type  キャッシュ種別 (view, template)
     * @param string      $group キャッシュグループ名
     *
     * @return bool 本番環境かつ view キャッシュの場合には、キャッシュが有効かどうかを返す
     */
    public static function cache($time, $type = 'view', $group = 'kumbia.view')
    {
        if ($time === null) { // TODO キャッシュ削除処理
            return self::$_cache['type'] = false;
        }
        self::$_cache['type']  = $type;
        self::$_cache['time']  = $time;
        self::$_cache['group'] = $group;
        // 本番環境かつビューキャッシュの場合
        if (PRODUCTION && $type === 'view') {
            return self::getCache(); // キャッシュがあれば TRUE
        }

        return false;
    }

    /**
     * ビューのキャッシュを取得する
     *
     * @return bool キャッシュが取得できたかどうか
     */
    protected static function getCache()
    {
        // キャッシュが存在しない、または期限切れの場合は $_content は null のまま
        self::$_content = Cache::driver()->get(Router::get('route'), self::$_cache['group']);

        return self::$_content !== null;
    }

    /**
     * ビューのファイルパスを取得する
     *
     * @return string ビューのファイルパス
     */
    protected static function getView()
    {
        $file = APP_PATH.'views/'.self::getPath();
        // ビューが存在せず、scaffold 指定がある場合
        if (!is_file($file) && ($scaffold = self::$_controller['scaffold'] ?? null)) {
            $file = APP_PATH."views/_shared/scaffolds/$scaffold/".self::$_view.'.phtml';
        }

        return $file;
    }

    /**
     * ビューまたはテンプレートをキャッシュに保存する
     *
     * @param string $type view または template
     *
     * @return void
     */
    protected static function saveCache($type)
    {
        // 本番環境かつ指定された種別のキャッシュを行う場合
        if (PRODUCTION && self::$_cache['type'] === $type) {
            Cache::driver()->save(ob_get_contents(), self::$_cache['time'], Router::get('route'), self::$_cache['group']);
        }
    }

    /**
     * ビューをレンダリングする
     *
     * @param Controller $controller 現在のコントローラインスタンス
     *
     * @return void
     */
    public static function render(Controller $controller)
    {
        if (!self::$_view && !self::$_template) {
            ob_end_flush();

            return;
        }

        // コントローラのプロパティを保存し、それを元に出力を生成
        self::generate(self::$_controller = get_object_vars($controller));
    }

    /**
     * ビューの生成処理
     *
     * @param array $controller コントローラのプロパティ配列
     *
     * @return void
     */
    protected static function generate($controller)
    {
        // ヘルパーのオートロードを登録
        spl_autoload_register('kumbia_autoload_helper', true, true);
        // コントローラの属性をローカルスコープへ展開
        extract($controller, EXTR_OVERWRITE);

        // ビューが指定されており、かつキャッシュされていない場合
        if (self::$_view && self::$_content === null) {
            // 現時点の出力バッファの内容を取得
            self::$_content = ob_get_clean();
            // ビューのレンダリングを開始
            ob_start();

            // ビューファイルを読み込む
            if (!include self::getView()) {
                throw new KumbiaException('ビュー「'.self::getPath().'」が見つかりません', 'no_view');
            }

            // ビューキャッシュの保存（必要な場合）
            self::saveCache('view');

            // テンプレートが指定されていない場合は、そのまま出力して終了
            if (!self::$_template) {
                ob_end_flush();

                return;
            }

            // ビューの出力内容をコンテンツとして保持
            self::$_content = ob_get_clean();
        }

        // テンプレートのレンダリング
        if ($__template = self::$_template) {
            ob_start();

            // テンプレートを読み込む
            if (!include APP_PATH."views/_shared/templates/$__template.phtml") {
                throw new KumbiaException("テンプレート $__template が見つかりません");
            }

            // テンプレートキャッシュの保存（必要な場合）
            self::saveCache('template');
            ob_end_flush();

            return;
        }

        echo self::$_content;
    }

    /**
     * バッファされたコンテンツを出力する
     *
     * @return void
     */
    public static function content()
    {
        if (isset($_SESSION['KUMBIA.CONTENT'])) {
            echo $_SESSION['KUMBIA.CONTENT'];
            unset($_SESSION['KUMBIA.CONTENT']);
        }
        echo self::$_content;
    }

    /**
     * 部分テンプレート（パーシャル）をレンダリングする
     *
     * @throw KumbiaException
     * @param  string            $partial レンダリングするパーシャル名
     * @param  string            $__time  キャッシュ有効時間
     * @param  array|string|null $params  パーシャルに渡す変数
     * @param  string            $group   キャッシュグループ名
     * @return void
     */
    public static function partial($partial, $__time = '', $params = null, $group = 'kumbia.partials')
    {
        // 本番環境かつキャッシュ時間指定があり、キャッシュ開始に成功した場合はその内容を使用して戻る
        if (PRODUCTION && $__time && !Cache::driver()->start($__time, $partial, $group)) {
            return;
        }

        // まず app 側の partials ディレクトリを探す
        $__file = APP_PATH."views/_shared/partials/$partial.phtml";

        if (!is_file($__file)) {
            // 見つからなければ core 側の partials ディレクトリを探す
            $__file = CORE_PATH."views/partials/$partial.phtml";
        }

        if ($params) {
            if (is_string($params)) {
                $params = Util::getParams(explode(',', $params));
            }

            // パラメータをローカルスコープへ展開
            extract($params, EXTR_OVERWRITE);
        }

        // パーシャルを読み込む
        if (!include $__file) {
            throw new KumbiaException('パーシャルビュー「'.$__file.'」が見つかりません', 'no_partial');
        }

        // キャッシュが指定されている場合は、ここでキャッシュを完了させる
        if (PRODUCTION && $__time) {
            Cache::driver()->end();
        }
    }

    /**
     * コントローラの public 属性、もしくは全属性を取得する
     *
     * @param string $var 変数名（省略した場合は全体を返す）
     *
     * @return mixed 変数の値
     */
    public static function getVar($var = '')
    {
        if (!$var) {
            return self::$_controller;
        }

        return self::$_controller[$var] ?? null;
    }
}

/**
 * htmlspecialchars のショートカット関数
 * デフォルトでアプリケーションの charset を使用します。
 *
 * @param string $string エスケープ対象の文字列
 * @param string $charset 文字コード
 *
 * @return string エスケープ後の文字列
 */
function h($string, $charset = APP_CHARSET)
{
    return htmlspecialchars($string, ENT_QUOTES, $charset);
}
