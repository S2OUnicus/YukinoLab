<?php
/**
 * KumbiaPHP Web Framework
 * データベース接続の設定パラメータ
 */
return [
    'development' => [
        /**
         * host: データベースサーバーの IP またはホスト名
         */
        'host'     => 'localhost',
        /**
         * username: データベースにアクセス権限を持つユーザー名
         */
        'username' => 'root', // root ユーザーの使用は推奨されません
        /**
         * password: データベースユーザーのパスワード
         */
        'password' => '',
        /**
         * name: データベース名
         */
        'name'     => 'test',
        /**
         * type: データベースエンジンの種類 (mysql, pgsql, oracle または sqlite)
         */
        'type'     => 'mysql',
        /**
         * charset: 接続に使用する文字コード。例: 'utf8'
         */
        'charset'  => 'utf8',
        /**
         * dsn: データベースへの接続文字列
         */
        //'dsn' => '',
        /**
         * pdo: PDO 接続を有効にするかどうか (On/Off)。使用する場合はコメントを外す
         */
        //'pdo' => 'On',
    ],

    'production' => [
        /**
         * host: データベースサーバーの IP またはホスト名
         */
        'host'     => 'localhost',
        /**
         * username: データベースにアクセス権限を持つユーザー名
         */
        'username' => 'root', // root ユーザーの使用は推奨されません
        /**
         * password: データベースユーザーのパスワード
         */
        'password' => '',
        /**
         * name: データベース名
         */
        'name'     => 'test',
        /**
         * type: データベースエンジンの種類 (mysql, pgsql または sqlite)
         */
        'type'     => 'mysql',
        /**
         * charset: 接続に使用する文字コード。例: 'utf8'
         */
        'charset'  => 'utf8',
        /**
         * dsn: データベースへの接続文字列
         */
        //'dsn' => '',
        /**
         * pdo: PDO 接続を有効にするかどうか (On/Off)。使用する場合はコメントを外す
         */
        //'pdo' => 'On',
    ],
];

/**
 * SQLite の設定例
 */
/*'development' => [
    'type' => 'sqlite',
    'dsn' => 'temp/data.sq3',
    'pdo' => 'On',
] */
