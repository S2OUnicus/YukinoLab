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
  * ファサード（Facade）パターン用の基底クラス
  *
  * 各コンポーネントに対する静的インターフェースを提供します。
  *
  * @category   Kumbia
  */
abstract class KumbiaFacade
{
    /**
     * プロバイダ（実体オブジェクト）を保持する配列
     *
     * @var array
     */
    protected static $providers = [];

    /**
     * プロバイダを一括設定する
     *
     * @param array $p コンポーネント名をキー、インスタンスを値とする配列
     */
    public static function providers(array $p)
    {
        self::$providers = $p;
    }

    /**
     * ファサードが利用するコンポーネントのエイリアスを取得する
     *
     * 継承クラスで実装する必要があります。
     *
     * @return string
     *
     * @throws KumbiaException 継承先で未実装の場合にスローされます
     */
    protected static function getAlias()
    {
        throw new KumbiaException('getAlias が実装されていません');
    }

    /**
     * 指定された名前のインスタンスを取得する
     *
     * @param string $name コンポーネント名（エイリアス）
     * @return mixed|null  対応するインスタンス。存在しない場合は null
     */
    protected static function getInstance($name)
    {
        return self::$providers[$name] ?? null;
    }

    /**
     * 動的な static メソッド呼び出しをハンドリングする
     *
     * ファサード経由で呼ばれた static メソッドを
     * 対応する実体オブジェクトへ委譲します。
     *
     * @param string $method 呼び出されたメソッド名
     * @param array  $args   引数配列
     *
     * @return mixed
     *
     * @throws KumbiaException ファサードのルートインスタンスが設定されていない場合
     */
    public static function __callStatic($method, $args)
    {
        $instance = self::getInstance(static::getAlias());
        if (!$instance) {
            throw new KumbiaException('ファサードのルートオブジェクトが設定されていません');
        }

        return call_user_func_array([$instance, $method], $args);
    }
}
