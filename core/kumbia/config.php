<?php
/**
 * KumbiaPHP Web & アプリケーションフレームワーク
 *
 * LICENSE
 *
 * このソースファイルは、同梱されている LICENSE ファイルに記載の
 * New BSD License の条件に従います。
 *
 * @category   Config
 *
 * @copyright  Copyright (c) 2005 - 2024 KumbiaPHP Team (http://www.kumbiaphp.com)
 * @license    https://github.com/KumbiaPHP/KumbiaPHP/blob/master/LICENSE   New BSD License
 */

/**
 * 設定ファイル読み込み用クラス
 *
 * シングルトンに近い形で、読み込んだ設定を
 * 配列にキャッシュして再利用します。
 * 同じ設定ファイルを runtime 中に何度も読み込まないことで、
 * パフォーマンスを向上させます。
 *
 * @category   Kumbia
 */
class Config
{
    /**
     * 読み込まれた全設定を保持する配列
     *
     * @var array<array-key,mixed>
     */
    protected static $config = [];

    /**
     * 設定値を取得する
     * -
     * `fichero.sección.variable` 形式で設定を取得します。
     *
     * @param string $var fichero.sección.variable 形式のキー
     *
     * @throws KumbiaException
     * @return mixed
     */
    public static function get($var)
    {
        $sections = explode('.', $var);
        self::$config[$sections[0]] ??= self::load($sections[0]);

        return match(count($sections)) {
            3 => self::$config[$sections[0]][$sections[1]][$sections[2]] ?? null,
            2 => self::$config[$sections[0]][$sections[1]] ?? null,
            1 => self::$config[$sections[0]] ?? null,
            default => throw new KumbiaException('Config::get(ファイル.セクション.変数) の階層は最大 3 つまでです。指定されたキー: '.$var)
        };
    }

    /**
     * 現在読み込まれているすべての設定を取得する
     *
     * @return array<array-key,mixed>
     */
    public static function getAll()
    {
        return self::$config;
    }

    /**
     * 設定値を代入する
     * -
     * `fichero.sección.variable` 形式で設定値を書き込みます。
     *
     * @param string $var   設定キー（fichero.sección.variable）
     * @param mixed  $value 設定する値
     * 
     * @throws KumbiaException
     * @return void
     */
    public static function set($var, $value)
    {
        $sections = explode('.', $var);
        match(count($sections)) {
            3 => self::$config[$sections[0]][$sections[1]][$sections[2]] = $value,
            2 => self::$config[$sections[0]][$sections[1]] = $value,
            1 => self::$config[$sections[0]] = $value,
            default => throw new KumbiaException('Config::set(ファイル.セクション.変数) の階層は最大 3 つまでです。指定されたキー: '.$var)
        };
    }

    /**
     * 設定ファイルを読み込む
     * -
     * 指定された設定ファイルを読み込み、その内容を返します。
     *
     * @param string $file  対象となる .php または .ini ファイル名（拡張子なし）
     * @param bool   $force すでに読み込まれていても再読込する場合は true
     *
     * @return array<array-key,mixed>
     */
    public static function read($file, $force = false)
    {
        if ($force) {
            return self::$config[$file] = self::load($file);
        }

        return self::$config[$file] ??= self::load($file);
    }

    /**
     * 実際に設定ファイルを読み込む内部メソッド
     * -
     * まず `config/$file.php` を試し、
     * なければ `config/$file.ini` を parse_ini_file で読み込みます。
     * （パフォーマンスの観点から .php の使用が推奨されます）
     *
     * @param string $file ファイル名（拡張子なし）
     *
     * @return array<array-key,mixed>
     */
    private static function load($file): array
    {
        if (is_file(APP_PATH."config/$file.php")) {

            return require APP_PATH."config/$file.php";
        }
        // .php が無い場合は .ini を読み込む（パフォーマンス上は非推奨・レガシー用途）
        return parse_ini_file(APP_PATH."config/$file.ini", true);
    }
}
