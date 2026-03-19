<?php
/**
 * 静的ページを扱うためのコントローラ。
 * ただし、Templates・Layouts・Partials を利用することで、
 * 他のコントローラと同じように扱うこともできます。
 *
 * 渡されたパラメータは views/pages/ 以下にあるビューを指し、
 * ディレクトリ構造をそのまま反映します。
 * 例:
 *
 * 例1:
 * dominio.com/pages/organizacion/privacidad
 * → views/pages/organizacion/privacidad.phtml のビューを表示します
 *
 * 例2:
 * dominio.com/pages/aviso
 * → views/pages/aviso.phtml のビューを表示します
 *
 * また、routes.ini を使って別名で呼び出すこともできます。
 *   /aviso = pages/show/aviso
 * と定義しておけば、
 * dominio.com/aviso にアクセスした際に views/pages/aviso.phtml のビューを表示します。
 *
 *   /organizacion/* = pages/organizacion/*
 * と定義した場合、
 * dominio.com/organizacion/privacidad にアクセスすると
 * views/organizacion/privacidad.phtml のビューを表示します。
 *
 * さらに、Helper も利用できます:
 * <?= Html::link_to('pages/aviso', 'Ir Aviso') ?>
 * これはクリックすると dominio.com/pages/aviso に移動するリンクを表示します。
 */
class PagesController extends AppController
{
    protected function before_filter()
    {
        // AJAX の場合はビューのみを返す
        if (Input::isAjax()) {
            View::template(null);
        }
    }

    public function __call($name, $params)
    {
        View::select(implode('/', [$name, ...$params]));
    }
}
