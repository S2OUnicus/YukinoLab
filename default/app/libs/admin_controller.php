<?php
/**
 * @see Controller 新しいコントローラ
 */
require_once CORE_PATH . 'kumbia/controller.php';

/**
 * 継承するコントローラを保護するためのコントローラ。
 * セキュリティやモジュールの規約を作り始めるためのベースクラスです。
 *
 * すべてのコントローラは上位レベルでこのクラスを継承するため、
 * ここで定義されたメソッドは任意のコントローラから利用できます。
 *
 * @category Kumbia
 * @package Controller
 */
abstract class AdminController extends Controller
{
    final protected function initialize()
    {
        // 認証や権限チェックのコード
        // 実装は自由ですが、近いうちにデフォルト実装を追加する予定です
        // 必要なデフォルト機能をまとめた抽象クラスを作成する可能性があります
    }

    final protected function finalize()
    {

    }
}
