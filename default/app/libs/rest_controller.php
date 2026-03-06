<?php

/**
 * REST リクエストを扱うためのコントローラ
 *
 * デフォルトでは、各アクション名はクライアントが使用した
 * メソッド名（GET, POST, PUT, DELETE, OPTIONS, HEADERS, PURGE...）
 * と同じになります。
 *
 * さらに、メソッド名の後ろにアクション名を付けることで
 * 追加のアクションを定義できます（例: put_cancel, post_reset など）。
 *
 * @category Kumbia
 * @package Controller
 * @author kumbiaPHP Team
 */
require_once CORE_PATH . 'kumbia/kumbia_rest.php';

abstract class RestController extends KumbiaRest
{
    /**
     * リクエストの初期化
     * ****************************************
     * ここに API の認証処理などを記述します
     * ****************************************
     */
    final protected function initialize()
    {

    }

    final protected function finalize()
    {

    }
}
