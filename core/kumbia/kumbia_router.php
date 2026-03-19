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
 * @package    KumbiaRouter
 *
 * @copyright  Copyright (c) 2005 - 2023 KumbiaPHP Team (http://www.kumbiaphp.com)
 * @license    https://github.com/KumbiaPHP/KumbiaPHP/blob/master/LICENSE   New BSD License
 */

class KumbiaRouter
{

    /**
     * 渡された $url を (モジュール), コントローラ, アクション, 引数 に分解する
     *
     * @param string $url
     * @return array
     */
    public static function rewrite(string $url): array
    {
        $router = [];
        // デフォルト値
        if ($url === '/') {
            return $router;
        }

        // URL の末尾が controller/action/ のように値なしで終わっている場合なども含めてトリムして分割する
        // URL のすべてのパラメータを取得して配列に格納
        $urlItems = explode('/', trim($url, '/'));

        // URL の最初の要素はモジュールか？
        if (is_dir(APP_PATH."controllers/$urlItems[0]")) {
            $router['module'] = $urlItems[0];

            // これ以上パラメータがない場合はここで終了
            if (next($urlItems) === false) {
                $router['controller_path'] = "$urlItems[0]/index";
                return $router;
            }
        }

        // コントローラ名（ハイフンをアンダースコアへ変換）
        $router['controller']      = str_replace('-', '_', current($urlItems));
        $router['controller_path'] = isset($router['module']) ? "$urlItems[0]/".$router['controller'] : $router['controller'];

        // これ以上パラメータがない場合はここで終了
        if (next($urlItems) === false) {
            return $router;
        }

        // アクション名
        $router['action'] = current($urlItems);

        // これ以上パラメータがない場合はここで終了
        if (next($urlItems) === false) {
            return $router;
        }

        // 残りをパラメータとして扱う
        $router['parameters'] = array_slice($urlItems, key($urlItems));
        return $router;
    }

    /**
     * config/routes.ini に定義されたルーティングテーブルを参照し、
     * 現在のコントローラ・アクション・ID に対応するルートがあるかを検索する
     *
     * @param string $url ルーティング対象の URL
     * @return string 変換後の URL
     */
    public static function ifRouted(string $url): string
    {
        $routes = Config::get('routes.routes');

        // 完全一致するルートがあればそれを返す
        if (isset($routes[$url])) {
            return $routes[$url];
        }

        // ワイルドカード * を含むルート定義があれば、それを使って新しいルートを生成
        foreach ($routes as $key => $val) {
            if ($key === '/*') {
                return rtrim($val, '*').$url;
            }

            if (strripos($key, '*', -1)) {
                $key = rtrim($key, '*');
                if (strncmp($url, $key, strlen($key)) == 0) {
                    return str_replace($key, rtrim($val, '*'), $url);
                }
            }
        }
        return $url;
    }

    /**
     * コントローラファイルを読み込み、インスタンスを生成して返す
     *
     * @param array $params ルーターで解析されたパラメータ
     *
     * @throws KumbiaException コントローラが存在しない場合
     *
     * @return Controller
     */
    public static function getController(array $params): Controller
    {
        if (!include_once APP_PATH."controllers/{$params['controller_path']}_controller.php") {
            // 変数を展開して扱いやすくする
            extract($params, EXTR_OVERWRITE);
            throw new KumbiaException('', 'no_controller');
        }
        // アクティブなコントローラ名を決定
        $controller = Util::camelcase($params['controller']).'Controller';
        return new $controller($params);
    }
}
