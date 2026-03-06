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
 * 永続（常駐）アプリケーションで Router の上に動作するクラス
 *
 * Router クラスに対するキャッシュレイヤーを提供します。
 *
 * @category   Kumbia
 * @package    Router
 */
class StaticRouter extends Router
{
    /**
     * ルーティング結果のキャッシュ
     *
     * @var array
     */
    protected static $routes = [];

    /**
     * 指定された URL に対してルーター処理を実行する
     *
     * キャッシュされたルートがあればそれを利用し、
     * なければ親クラス（Router）の execute を呼び出します。
     *
     * @param string $url 対象の URL
     * @return Controller 実行されたコントローラのインスタンス
     */
    public static function execute($url)
    {
        if (isset(self::$routes[$url])) {
            $cont = self::$routes[$url];
            $cont['vars']['method'] = $_SERVER['REQUEST_METHOD'];
            return parent::dispatch(new $cont['name'](self::$vars = $cont['vars']));
        }

        return parent::execute($url);
    }

    /**
     * ルートのディスパッチ処理（キャッシュ登録を含む）
     *
     * コントローラ情報をキャッシュに保存しつつ、
     * 親クラスの dispatch を呼び出します。
     *
     * @param Controller $cont ディスパッチ対象のコントローラ
     * @return Controller 実行後のコントローラインスタンス
     */
    protected static function dispatch($cont)
    {
        self::$routes[self::$vars['route']] = [
            'name' => $cont::class,
            'vars' => self::$vars
        ];
        // キャッシュ件数が 256 件を超えた場合は最古のエントリを削除
        if (\count(self::$routes) > 256) {
            unset(self::$routes[key(self::$routes)]);
        }

        return parent::dispatch($cont);
    }
}
