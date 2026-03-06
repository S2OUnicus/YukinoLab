<?php
/**
 * KumbiaPHP Web Framework
 * ルート定義ファイル（任意）
 *
 * このファイルを使って、コントローラとアクション間の
 * 静的なルーティングを定義します。
 * コントローラ同士は、ワイルドカード '*' を使って
 * 次のようにルーティングできます:
 *
 * '/controlador1/accion1/valor_id1'  =>  'controlador2/accion2/valor_id2'
 *
 * 例:
 * 任意のリクエスト posts/adicionar を posts/insertar/* へルーティング
 * '/posts/adicionar/*' => 'posts/insertar/*'
 *
 * その他の例:
 *
 * '/prueba/ruta1/*' => 'prueba/ruta2/*',
 * '/prueba/ruta2/*' => 'prueba/ruta3/*',
 */
return [
    'routes' => [
        /**
         * フレームワークに関する情報を表示
         */
        '/' => 'index/index',
        /**
         * config.php / config.ini のステータス表示
         */
        '/status' => 'pages/kumbia/status'
    ],
];
