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

/**
 * モデルを操作するためのコンソールクラス
 *
 * @category   Kumbia
 * @package    Console
 */
class ModelConsole
{
    /**
     * モデルを作成するコンソールコマンド
     *
     * @param array  $params コンソールから渡された名前付きパラメータ
     * @param string $model  モデル名
     * @throw KumbiaException
     */
    public function create($params, $model)
    {
        // ファイルのベースパス
        $file = APP_PATH . 'models';

        // パスを取得
        $path = explode('/', trim($model, '/'));

        // モデル名を取得
        $model_name = array_pop($path);

        // サブディレクトリが指定されている場合
        if (count($path)) {
            $dir = implode('/', $path);
            $file .= "/$dir";
            if (!is_dir($file) && !FileUtil::mkdir($file)) {
                throw new KumbiaException("ディレクトリ \"$file\" を作成できませんでした");
            }
        }
        $file .= "/$model_name.php";

        // ファイルが存在しない、または上書きする場合
        if (!is_file($file) ||
            Console::input('モデルは既に存在します。上書きしますか？ (s/n): ', array('s', 'n')) == 's') {

            // クラス名
            $class = Util::camelcase($model_name);

            // モデルのコード生成
            ob_start();
            include __DIR__ . '/generators/model.php';
            $code = '<?php' . PHP_EOL . ob_get_clean();

            // ファイルを生成
            if (file_put_contents($file, $code)) {
                echo "-> モデル $model_name を作成しました: $file" . PHP_EOL;
            } else {
                throw new KumbiaException("ファイル \"$file\" を作成できませんでした");
            }
        }
    }

    /**
     * モデルを削除するコンソールコマンド
     *
     * @param array  $params コンソールから渡された名前付きパラメータ
     * @param string $model  モデル名
     * @throw KumbiaException
     */
    public function delete($params, $model)
    {
        // ファイルパス
        $file = APP_PATH . 'models/' . trim($model, '/');

        // ディレクトリかどうか
        if (is_dir($file)) {
            $success = FileUtil::rmdir($file);
        } else {
            // ディレクトリでなければファイルとして扱う
            $file = "$file.php";
            $success = unlink($file);
        }

        // 結果メッセージ
        if ($success) {
            echo "-> 削除しました: $file" . PHP_EOL;
        } else {
            throw new KumbiaException("\"$file\"を削除できませんでした");
        }
    }
}
