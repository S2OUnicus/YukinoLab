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
 * フレームワーク全体で利用するユーティリティクラス
 *
 * 文字列の操作や、
 * 名前付きパラメータを配列に変換する処理などを提供します。
 *
 * @category   Kumbia
 */
class Util
{
    /**
     * スペースまたはアンダースコア区切りの文字列を camelCase 形式に変換する
     *
     * @param string $str   変換対象の文字列
     * @param bool   $lower true の場合は lowerCamelCase（先頭小文字）にする
     *
     * @return string
     */
    public static function camelcase($str, $lower = false)
    {
        // lowerCamelCase を返す場合
        if ($lower) {
            return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $str))));
        }

        return str_replace(' ', '', ucwords(str_replace('_', ' ', $str)));
    }

    /**
     * CamelCase 形式の文字列を small_case（スネークケース・小文字）に変換する
     *
     * @param string $str 変換対象の文字列
     *
     * @return string
     */
    public static function smallcase($str)
    {
        return strtolower(preg_replace('/([A-Z])/', '_\\1', lcfirst($str)));
    }

    /**
     * 文字列中のスペースをアンダースコア（_）に置き換える
     *
     * @param string $str 変換対象の文字列
     *
     * @return string
     */
    public static function underscore($str)
    {
        return strtr($str, ' ', '_');
    }

    /**
     * 文字列中のスペースをダッシュ（-、ハイフン）に置き換える
     *
     * @param string $str 変換対象の文字列
     *
     * @return string
     */
    public static function dash($str)
    {
        return strtr($str, ' ', '-');
    }

    /**
     * 文字列中のアンダースコア（_）やダッシュ（-）をスペースに置き換える
     *
     * @param string $str 変換対象の文字列
     *
     * @return string
     */
    public static function humanize($str)
    {
        return strtr($str, '_-', '  ');
    }

    /**
     * 名前付きパラメータ形式の配列を通常の配列に変換する
     *
     * 例: ["name: foo", "id: 10"] → ["name" => "foo", "id" => "10"]
     *     ["foo", "bar"] → インデックス配列として保持
     *
     * @param array $params パラメータ配列
     *
     * @return array 変換後の配列
     */
    public static function getParams($params)
    {
        $data = [];
        foreach ($params as $p) {
            if (is_string($p)) {
                $match = explode(': ', $p, 2);
                if (isset($match[1])) {
                    $data[$match[0]] = $match[1];
                } else {
                    $data[] = $p;
                }
            } else {
                $data[] = $p;
            }
        }

        return $data;
    }

    /**
     * 「item1,item2,item3」という形式の文字列を
     * 「"item1","item2","item3"」という形式に変換する
     *
     * @param string $lista カンマ (,) 区切りのアイテム文字列
     *
     * @return string 各アイテムをダブルクォーテーションで囲み、カンマ区切りにした文字列
     */
    public static function encomillar($lista)
    {
        $items = explode(',', $lista);

        return '"'.implode('","', $items).'"';
    }
}
