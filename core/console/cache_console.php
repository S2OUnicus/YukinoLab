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
 * @package    Console
 *
 * @copyright  Copyright (c) 2005 - 2023 KumbiaPHP Team (http://www.kumbiaphp.com)
 * @license    https://github.com/KumbiaPHP/KumbiaPHP/blob/master/LICENSE   New BSD License
 */
// キャッシュ操作用ライブラリを読み込む
Load::lib('cache');

/**
 * キャッシュを操作するためのコンソールクラス
 *
 * @category   Kumbia
 * @package    Console
 */
class CacheConsole
{

    /**
     * キャッシュを全体／グループ単位で削除するコンソールコマンド
     *
     * @param array  $params コンソールから渡された名前付きパラメータ
     * @param string $group  グループ名（省略時は全体）
     * @throw KumbiaException
     */
    public function clean($params, $group = '')
    {
        // キャッシュドライバを取得
        $cache = $this->setDriver($params);

        // キャッシュを削除
        if ($cache->clean($group)) {
            if ($group) {
                echo "-> グループ $group のキャッシュを削除しました", PHP_EOL;
            } else {
                echo "-> キャッシュを削除しました", PHP_EOL;
            }
        } else {
            throw new KumbiaException('キャッシュの内容を削除できませんでした');
        }
    }

    /**
     * キャッシュされた要素を 1 件削除するコンソールコマンド
     *
     * @param array  $params コンソールから渡された名前付きパラメータ
     * @param string $id     要素のID
     * @param string $group  グループ名
     * @throw KumbiaException
     */
    public function remove($params, $id, $group = 'default')
    {
        // キャッシュドライバを取得
        $cache = $this->setDriver($params);

        // 要素を削除
        if ($cache->remove($id, $group)) {
            echo '-> キャッシュ要素を削除しました', PHP_EOL;
        } else {
            throw new KumbiaException("ID \"$id\" を持つ要素をグループ \"$group\" から削除できませんでした");
        }
    }

    /**
     * 指定されたドライバのキャッシュインスタンスを返す
     *
     * @param array $params 名前付きパラメータ配列
     */
    private function setDriver($params)
    {
        if (isset($params['driver'])) {
            return Cache::driver($params['driver']);
        }
        return Cache::driver();

    }

}
