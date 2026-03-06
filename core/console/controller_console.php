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
 * コントローラーを操作するためのコンソールクラス
 *
 * @category   Kumbia
 * @package    Console
 */
class ControllerConsole
{

    /**
     * コントローラーを作成するコンソールコマンド
     *
     * @param array  $params     コンソールから渡された名前付きパラメータ
     * @param string $controller コントローラー名
     * @throw KumbiaException
     */
    public function create($params, $controller)
    {
        // ファイル名のベースパス
        $file = APP_PATH . 'controllers';

        // コントローラーのパスをトリム
        $clean_path = trim($controller, '/');

        // パスを分解
        $path = explode('/', $clean_path);

        // コントローラー名を取得
        $controller_name = array_pop($path);

        // コントローラーをディレクトリでグループ化している場合
        if (count($path)) {
            $dir = implode('/', $path);
            $file .= "/$dir";
            if (!is_dir($file) && !FileUtil::mkdir($file)) {
                throw new KumbiaException("ディレクトリ \"$file\" を作成できませんでした");
            }
        }
        $file .= "/{$controller_name}_controller.php";

        // ファイルが存在しない、または上書きする場合
        if (!is_file($file) ||
                Console::input("コントローラーは既に存在します。上書きしますか？ (s/n): ", array('s', 'n')) == 's') {

            // クラス名
            $class = Util::camelcase($controller_name);

            // コントローラーのコード生成
            ob_start();
            include __DIR__ . '/generators/controller.php';
            $code = '<?php' . PHP_EOL . ob_get_clean();

            // ファイルを出力
            if (file_put_contents($file, $code)) {
                echo "-> コントローラー $controller_name を作成しました: $file" . PHP_EOL;
            } else {
                throw new KumbiaException("ファイル \"$file\" を作成できませんでした");
            }

            // ビュー用ディレクトリ
            $views_dir = APP_PATH . "views/$clean_path";

            // ビュー用ディレクトリが存在しない場合
            if (!is_dir($views_dir)) {
                if (FileUtil::mkdir($views_dir)) {
                    echo "-> ビュー用ディレクトリを作成しました: $views_dir" . PHP_EOL;
                } else {
                    throw new KumbiaException("ディレクトリ \"$views_dir\" を作成できませんでした");
                }
            }
        }
    }

    /**
     * コントローラーを削除するコンソールコマンド
     *
     * @param array  $params     コンソールから渡された名前付きパラメータ
     * @param string $controller コントローラー名
     * @throw KumbiaException
     */
    public function delete($params, $controller)
    {
        // コントローラーへのパスをトリム
        $clean_path = trim($controller, '/');

        // ファイル／ディレクトリのベースパス
        $file = APP_PATH . "controllers/$clean_path";

        // ディレクトリかどうかを判定
        if (is_dir($file)) {
            $success = FileUtil::rmdir($file);
        } else {
            // ディレクトリでなければファイルとして扱う
            $file = "{$file}_controller.php";
            $success = unlink($file);
        }

        // 結果メッセージ
        if ($success) {
            echo "-> 削除しました: $file" . PHP_EOL;
        } else {
            throw new KumbiaException("\"$file\"を削除できませんでした");
        }

        // ビュー用ディレクトリ
        $views_dir = APP_PATH . "views/$clean_path";

        // ビュー用ディレクトリの削除を試みる
        if (is_dir($views_dir)
                && Console::input('ビュー用ディレクトリも削除しますか？ (s/n): ', array('s', 'n')) == 's') {

            if (!FileUtil::rmdir($views_dir)) {
                throw new KumbiaException("\"$views_dir\"を削除できませんでした");
            }

            echo "-> 削除しました: $views_dir" . PHP_EOL;
        }
    }

}
