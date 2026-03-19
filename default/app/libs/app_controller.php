<?php
/**
 * @see Controller ベースコントローラ
 */
require_once CORE_PATH . 'kumbia/controller.php';

/**
 * 各コントローラが継承するメインコントローラ
 *
 * すべてのコントローラは上位レベルでこのクラスを継承するため、
 * ここで定義されたメソッドは任意のコントローラから利用できます。
 *
 * @category Kumbia
 * @package Controller
 */
abstract class AppController extends Controller
{
    final protected function initialize()
    {

    }

    final protected function finalize()
    {

    }
}
