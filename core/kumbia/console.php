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
 * @package    Core
 *
 * @copyright  Copyright (c) 2005 - 2023 KumbiaPHP Team (http://www.kumbiaphp.com)
 * @license    https://github.com/KumbiaPHP/KumbiaPHP/blob/master/LICENSE   New BSD License
 */
/**
 * @see Util
 */
require CORE_PATH . 'kumbia/util.php';
/**
 * @see KumbiaException
 */
require CORE_PATH . 'kumbia/kumbia_exception.php';
/**
 * @see Config
 */
require CORE_PATH . 'kumbia/config.php';
/**
 * @see Load
 */
require CORE_PATH . 'kumbia/load.php';

/**
 * nelsonrojas による修正
 *
 * 問題: console controller create を使用すると 85 行目でエラーが発生し、
 *       FileUtil が見つからない。
 * 解決: 次の行でライブラリを読み込む。
 */
require CORE_PATH . 'libs/file_util/file_util.php';

/**
 * KumbiaPHP 用コンソールマネージャ
 *
 * モデル生成用コンソール。
 * コントローラー生成用コンソール。
 * キャッシュ操作用コンソール。
 *
 * @category   Kumbia
 * @package    Core
 */
class Console
{

    /**
     * コンソール用の引数リストを生成する
     *
     * 返り値の最初の要素は、ターミナルから渡された
     * 名前付きパラメータ（--key=value 形式）の配列になります。
     *
     * @param array $argv ターミナル引数
     * @return array
     */
    private static function _getConsoleArgs($argv)
    {
        $args = array(array());

        foreach ($argv as $p) {
            if (is_string($p) && preg_match("/--([a-z_0_9]+)[=](.+)/", $p, $regs)) {
                // 名前付きパラメータ配列に格納
                $args[0][$regs[1]] = $regs[2];
            } else {
                // 単純な位置引数として格納
                $args[] = $p;
            }
        }

        return $args;
    }

    /**
     * 指定された名前のコンソールクラスのインスタンスを生成する
     *
     * @param string $console_name コンソール名
     * @return object
     * @throws KumbiaException
     */
    public static function load($console_name)
    {
        // コンソールクラス名
        $Console = Util::camelcase($console_name) . 'Console';

        if (!class_exists($Console)) {
            // コンソールクラスファイルの読み込みを試みる
            $file = APP_PATH . "extensions/console/{$console_name}_console.php";

            if (!is_file($file)) {
                $file = CORE_PATH . "console/{$console_name}_console.php";

                if (!is_file($file)) {
                    throw new KumbiaException('コンソール "' . $file . '" が見つかりませんでした');
                }
            }

            // コンソールをインクルード
            include_once $file;
        }

        // オブジェクトインスタンスを生成
        $console = new $Console();

        // コンソールの初期化メソッドがあれば呼び出す
        if (method_exists($console, 'initialize')) {
            $console->initialize();
        }

        return $console;
    }

    /**
     * ターミナルの引数からコンソールをディスパッチして実行する
     *
     * @param array $argv ターミナルから受け取った引数
     * @throws KumbiaException
     */
    public static function dispatch($argv)
    {
        // 最初の要素（スクリプト名）を取り除く
        array_shift($argv);

        // コンソール名を取得
        $console_name = array_shift($argv);
        if (!$console_name) {
            throw new KumbiaException('実行するコンソールが指定されていません');
        }

        // 実行するコマンド名を取得（省略時は main）
        $command = array_shift($argv);
        if (!$command) {
            $command = 'main';
        }

        // コンソール用の引数リストを取得
        // 最初の要素は名前付きパラメータ配列
        $args = self::_getConsoleArgs($argv);

        // アプリケーションパスを確認
        if (isset($args[0]['path'])) {
            $dir = realpath($args[0]['path']);
            if (!$dir) {
                throw new KumbiaException("パス \"{$args[0]['path']}\" は無効です");
            }
            // path パラメータを配列から削除
            unset($args[0]['path']);
        } else {
            // カレントディレクトリを取得
            $dir = getcwd();
        }

        // アプリケーションのパスを定義
        define('APP_PATH', rtrim($dir, '/') . '/');

        // 設定ファイルを読み込む
        $config = Config::read('config');

        // アプリケーションが本番環境かどうかを示す定数
        define('PRODUCTION', $config['application']['production']);

        // コンソールインスタンスを生成
        $console = self::load($console_name);

        // コンソールにコマンドが定義されているか確認
        if (!method_exists($console, $command)) {
            throw new KumbiaException("コマンド \"$command\" はコンソール \"$console_name\" に存在しません");
        }

        // initialize コマンドの実行は禁止（予約コマンド）
        if ($command == 'initialize') {
            throw new KumbiaException('initialize コマンドは予約されており、直接実行はできません');
        }

        // コンソールアクションのパラメータ数を検証
        $reflectionMethod = new ReflectionMethod($console, $command);
        if (count($args) < $reflectionMethod->getNumberOfRequiredParameters()) {
            throw new KumbiaException("コンソール \"$console_name\" のコマンド \"$command\" を実行するためのパラメータ数が不正です");
        }

        // コマンドを実行
        call_user_func_array(array($console, $command), $args);
    }

    /**
     * コンソールから標準入力を 1 行読み取る
     *
     * @param string $message 表示するメッセージ
     * @param array  $values  許可する入力値の配列（null の場合は何でも可）
     * @return string コンソールから読み取った値
     */
    public static function input($message, $values = null)
    {
        // 標準入力を開く
        $stdin = fopen('php://stdin', 'r');

        do {
            // メッセージを表示
            echo $message;

            // ターミナルから 1 行読み取る
            $data = str_replace(PHP_EOL, '', fgets($stdin));
        } while ($values && !in_array($data, $values));

        // リソースをクローズ
        fclose($stdin);

        return $data;
    }

}
