<?php
/**
 * KumbiaPHP Web Framework
 * アプリケーションの設定パラメータ
 */
return [
    'application' => [
        /**
         * name: アプリケーション名
         */
        'name' => 'YUKINO Lab',
        /**
         * author: サイト作者の名前
         */
        'author' => 'YUKINO, 如月キセキ (@S2OUnicus, @S2OValor, https://s2o.me/)',
        /**
         * pureVer: プロジェクト開発バージョン
         */
        'pureVer' => 'v0.0.1',
        /**
         * themeColor: テーマカラー
         */
        'themeColor' => '#EFE6C6',
        /**
         * database: 使用するデータベース接続名
         */
        'database' => 'development',
        /**
         * dbdate: アプリケーションで使用する日付フォーマットのデフォルト値
         */
        'dbdate' => 'YYYY-MM-DD',
        /**
         * debug: 画面上にエラーを表示するかどうか (On/Off)
         */
        'debug' => 'On',
        /**
         * log_exceptions: 例外を画面に表示するかどうか (On/Off)
         */
        'log_exceptions' => 'On',
        /**
         * cache_template: テンプレートキャッシュを有効にする場合はコメントを外す
         */
        //'cache_template' => 'On',
        /**
         * cache_driver: キャッシュで使用するドライバ (file, sqlite, memsqlite)
         */
        'cache_driver' => 'file',
        /**
         * metadata_lifetime: キャッシュに保存されたメタデータの有効期間
         */
        'metadata_lifetime' => '+1 year',
        /**
         * namespace_auth: Auth で使用するデフォルトの名前空間
         */
        'namespace_auth' => 'ylpro',
        /**
         * routes: routes.php のルーティング設定を有効にする場合はコメントを外す
         */
        'routes' => '1',
    ],
];
