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
 * 選択的ローダークラス
 *
 * コアおよびアプリケーション双方のライブラリを読み込むためのクラス。
 * アプリケーションのモデルを読み込む機能も提供します。
 *
 * @category   Kumbia
 */
class Load
{
    /**
     * APP 側のライブラリを読み込み、存在しない場合は CORE 側から読み込む
     *
     * @param string $lib 読み込むライブラリ名
     * @throw KumbiaException
     */
    public static function lib($lib)
    {
        $file = APP_PATH."libs/$lib.php";
        if (is_file($file)) {
            return include $file;
        }

        return self::coreLib($lib);
    }

    /**
     * コア側のライブラリを読み込む
     *
     * @param string $lib 読み込むライブラリ名
     * @throw KumbiaException
     */
    public static function coreLib($lib)
    {
        if (!include CORE_PATH."libs/$lib/$lib.php") {
            throw new KumbiaException("ライブラリ \"$lib\" が見つかりません");
        }
    }

    /**
     * モデルのインスタンスを取得する
     *
     * @param string $model  small_case 形式のモデル名
     * @param array  $params モデルのコンストラクタに渡すパラメータ
     *
     * @return object モデルインスタンス
     */
    public static function model($model, array $params = [])
    {
        // クラス名（キャメルケースに変換）
        $Model = Util::camelcase(basename($model));
        // クラスがまだ読み込まれていない場合
        if (!class_exists($Model, false)) {
            // クラスを読み込む
            if (!include APP_PATH."models/$model.php") {
                throw new KumbiaException($model, 'no_model');
            }
        }

        return new $Model($params);
    }

    /**
     * 複数のモデルを読み込む
     *
     * @param string|array $model small_case 形式のモデル名（配列または可変引数）
     * @throw KumbiaException
     */
    public static function models($model)
    {
        $args = is_array($model) ? $model : func_get_args();
        foreach ($args as $model) {
            $Model = Util::camelcase(basename($model));
            // すでにクラスが読み込まれている場合は次へ
            if (class_exists($Model, false)) {
                continue;
            }
            if (!include APP_PATH."models/$model.php") {
                throw new KumbiaException($model, 'no_model');
            }
        }
    }
}
