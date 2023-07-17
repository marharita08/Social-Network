<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                $response->headers->set('Access-Control-Allow-Origin', 'http://localhost:3000');
            },
        ],
		'jwt' => [
				'class' => \sizeg\jwt\Jwt::class,
				'key' => 'C1D1C045F1753BAEE77416D0E13FC02FF10A58A2C940EEC8F5F730E91317EA07', 
		],
        'request' => [
            'cookieValidationKey' => '1',
			'parsers' => [
				'application/json' => 'yii\web\JsonParser',
				'multipart/form-data' => 'yii\web\MultipartFormDataParser'
			],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\Users',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => [
                ['class' => 'yii\rest\UrlRule', 'controller' => 'articles', 
					'extraPatterns' => [
						'GET {id}/likes' => 'get-likes',
						'GET all-news' => 'all-news',
						'GET all-news/amount' => 'all-news-amount',
						'GET news/{id}' => 'news',
						'GET news/amount/{id}' => 'news-amount',
						'GET {id}/comments' => 'get-comments',
						'GET {id}/comments-count'=> 'comments-count',
						'GET {id}/likes-count'=> 'likes-count',
						'OPTIONS news/amount/{id}' => 'options',
						'OPTIONS news/{id}' => 'options',
						'OPTIONS {id}/likes' => 'options',
						'OPTIONS all-news' => 'options',
						'OPTIONS all-news/amount' => 'options',
						'OPTIONS {id}/comments' => 'options',
						'OPTIONS {id}/comments-count'=> 'options',
						'OPTIONS {id}/likes-count'=> 'options',
					],
				],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'users',
					'extraPatterns' => [
						'GET {id}/friends' => 'friends',
						'GET {id}/incoming-requests' => 'incoming-requests',
						'GET {id}/outgoing-requests' => 'outgoing-requests',
						'GET {id}/search' => 'search',
						'POST login' => 'login',
						'POST refresh' => 'refresh',
						'POST logout' => 'logout',
						'OPTIONS refresh' => 'options',
						'OPTIONS {id}/friends' => 'options',
						'OPTIONS {id}/incoming-requests' => 'options',
						'OPTIONS {id}/outgoing-requests' => 'options',
						'OPTIONS {id}/search' => 'options',
						'OPTIONS login' => 'options',
						'OPTIONS logout' => 'options',
					],
				],
				['class' => 'yii\rest\UrlRule', 'controller' => 'a-visibilities'],
				['class' => 'yii\rest\UrlRule', 'controller' => 'f-visibilities'],
				['class' => 'yii\rest\UrlRule', 'controller' => 'universities'],
				['class' => 'yii\rest\UrlRule', 'controller' => 'comments'],
				['class' => 'yii\rest\UrlRule', 'controller' => 'likes',
					'extraPatterns' => [
						'GET {id}/is-liked' => 'is-liked',
						'OPTIONS {id}/is-liked' => 'options'
					]
				],
				['class' => 'yii\rest\UrlRule', 'controller' => 'friends',
					'extraPatterns' => ['GET request/{id}' => 'request', 'OPTIONS request/{id}' => 'options']],
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
