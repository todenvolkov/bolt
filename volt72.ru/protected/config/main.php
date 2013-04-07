<?php
// основной конфигурационный файл Yii и Юпи! (подробнее http://www.yiiframework.ru/doc/guide/ru/basics.application)
return array(
    'params'=>array('adminEmail'=>'info@neo-systems.ru'),
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    // контроллер по умолчанию
    'defaultController' => 'site',
    // название приложения
    'name' => 'Создание сайтов Тюмень',
    // язык по умолчанию
    'language' => 'ru',
    // тема оформления по умолчанию
    'theme' => 'nsystems',
    // preloading 'log' component
    'preload' => array('log','bootstrap', // preload the bootstrap component
    ),

    // подключение путей
    'import' => array(
        'application.components.*',
        'application.models.*',

        // подключение путей из основных модулей 
        'application.modules.user.UserModule',
        'application.modules.user.models.*',
        'application.modules.user.forms.*',
        'application.modules.user.components.*',

        'application.modules.page.models.*',

        'application.modules.news.models.*',
        'application.modules.contentblock.models.*',
        'application.modules.comment.models.*',
        'application.modules.image.models.*',
        'application.modules.vote.models.*',
        'application.modules.blog.models.*',
        'application.modules.menu.models.*',
        'application.modules.menu.controllers.*',
        'application.modules.portfolio.models.*',
        'application.modules.portfolio.controllers.*',
        'application.modules.portfolio.widgets.*',
        'application.modules.businesstypes.models.*',
        'application.modules.businesstypes.controllers.*',
        'application.modules.yupe.controllers.*',
        'application.modules.yupe.widgets.*',
        'application.modules.yupe.helpers.*',
        'application.modules.yupe.models.*',
        'application.modules.yupe.components.*',

        'application.modules.social.widgets.ysc.*',

        'application.modules.social.components.*',
        'application.modules.social.models.*', 
        'application.modules.social.extensions.eoauth.*',
        'application.modules.social.extensions.eoauth.lib.*',
        'application.modules.social.extensions.lightopenid.*',
        'application.modules.social.extensions.eauth.services.*',
    ),

    // конфигурирование основных компонентов (подробнее http://www.yiiframework.ru/doc/guide/ru/basics.component)
    'components' => array(

        'authManager'=>array(
            'class'=>'RDbAuthManager',
        ),
        'bootstrap'=>array(
                'class'=>'ext.bootstrap.components.Bootstrap', // assuming you extracted bootstrap under extensions
            ),

        // Библиотека для работы с картинками через GD/ImageMagick
        // Лучше установите ImageMagick, т.к. он ресайзит анимированные гифы
        'image' => array(
            'class' => 'application.modules.yupe.extensions.image.CImageComponent',
            'driver' => 'GD', // Еще бывает ImageMagick, если используется он, надо указать к нему путь чуть ниже
            'params' => array('directory' => '/usr/bin'), // В этой директории должен быть convert
        ),

        // подключение библиотеки для авторизации через социальные сервисы, подробнее https://github.com/Nodge/yii-eauth
        'loid' => array(
            'class' => 'application.modules.social.extensions.lightopenid.loid',
        ),

        // экстеншн для авторизации через социальные сети подробнее http://habrahabr.ru/post/129804/
        'eauth' => array(
            'class' => 'application.modules.social.extensions.eauth.EAuth',
            'popup' => true, // Use the popup window instead of redirecting.
            'services' => array( // You can change the providers and their classes.
                'google' => array(
                  'class' => 'CustomGoogleService',
                ),
                'yandex' => array(
                   'class' => 'CustomYandexService',
                ),
            ),
        ),

        // компонент для отправки почты
        'mail' => array(
            'class' => 'application.modules.yupe.components.YMail',
        ),

        // конфигурирование urlManager, подробнее http://www.yiiframework.ru/doc/guide/ru/topics.url
        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => true,
            'cacheID' => 'cache',
            'rules' => array(
            	'/audit' => array('page/page/show',"defaultParams"=>array("slug"=>"auditor_software")),
                '/calc/order'=>'calc/default/submit',
                '/calc/<slug>'=>'calc/default',
                '/' => 'site/index',
                '/portfolio'=>'portfolio/portfolio/index',
                '/portfolio/year/<year>'=>'portfolio/portfolio/listYear',
                '/portfolio/sector/<sector>'=>'portfolio/portfolio/listSector',
                '/portfolio/status/<status>'=>'portfolio/portfolio/listStatus',
                '/portfolio/<part>'=>'portfolio/portfolio/list',
                '/portfolio/<part>/<id\d+>'=>'portfolio/portfolio/show',
                
                '/contacts' => array('page/page/show',"defaultParams"=>array("slug"=>"contacts")),
                '/services' => array('page/page/show',"defaultParams"=>array("slug"=>"services")),
                '/benefits' => array('page/page/show',"defaultParams"=>array("slug"=>"benefits")),
                
                '/login' => 'user/account/login',
                '/logout' => 'user/account/logout',
                '/registration' => 'user/account/registration',
                '/feedback' => 'feedback/feedback',
                '/pages/<slug>' => 'page/page/show',
                '/story/<title>' => 'news/news/show/',
                '/post/<slug>.html' => 'blog/post/show/',
                '/blog/<slug>' => 'blog/blog/show/',
                '/blogs/' => 'blog/blog/index/',
                '/users/' => 'user/people/index/',
                '/wiki/<controller:\w+>/<action:\w+>' => '/yeeki/wiki/<controller>/<action>',
            ),
        ),

        // конфигурируем компонент CHttpRequest для защиты от CSRF атак, подробнее http://www.yiiframework.ru/doc/guide/ru/topics.security
        // РЕКОМЕНДУЕМ УКАЗАТЬ СВОЕ ЗНАЧЕНИЕ ДЛЯ ПАРАМЕТРА "csrfTokenName"
        'request' => array(
            'class' => 'CHttpRequest',
            'enableCsrfValidation' => false,
            'csrfTokenName' => 'N_SYSTEMS_CRF_TOKEN',
        ),

        // подключение компонента для генерации ajax-ответов
        'ajax' => array(
            'class' => 'application.modules.yupe.components.YAsyncResponse',
        ),

        // компонент Yii::app()->user, подробнее http://www.yiiframework.ru/doc/guide/ru/topics.auth
        'user' => array(
            'class' => 'application.modules.user.components.YWebUser',
            'loginUrl' => '/user/account/login/'
        ),

         // параметры подключения к базе данных, подробнее http://www.yiiframework.ru/doc/guide/ru/database.overview
        'db' => require(dirname(__FILE__) . '/db.php'),

        // настройки кэширования, подробнее http://www.yiiframework.ru/doc/guide/ru/caching.overview
        'cache' => array(
            //'class' => 'CFileCache',
            'class' => 'CDummyCache',
        ),

        // параметры логирования, подробнее http://www.yiiframework.ru/doc/guide/ru/topics.logging
       'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning, info',
                ),
                //профайлер запросов к базе данных, на продакшн серверах рекомендуется отключить
                array(
                    'class'=>'application.modules.yupe.extensions.db_profiler.DbProfileLogRoute',
                    'countLimit' => 1, // How many times the same query should be executed to be considered inefficient
                    'slowQueryMin' => 0.01, // Minimum time for the query to be slow
                ),
            ),
        ),
    ),

    // конфигурация модулей приложения, подробнее http://www.yiiframework.ru/doc/guide/ru/basics.module
    'modules' => array(
        'menu' => array(
             'class' => 'application.modules.menu.MenuModule',
         ),
        'blog' => array(
            'class' => 'application.modules.blog.BlogModule',
        ),
        'social' => array(
            'class' => 'application.modules.social.SocialModule',
        ),
        'comment' => array(
            'class' => 'application.modules.comment.CommentModule',
        ),
        'dictionary' => array(
            'class' => 'application.modules.dictionary.DictionaryModule',
        ),
        'gallery' => array(
            'class' => 'application.modules.gallery.GalleryModule',
        ),
        'vote' => array(
            'class' => 'application.modules.vote.VoteModule',
        ),
        'contest' => array(
            'class' => 'application.modules.contest.ContestModule',
        ),
        'image' => array(
            'class' => 'application.modules.image.ImageModule',
        ),
        'yupe' => array(
            'class' => 'application.modules.yupe.YupeModule',
            'brandUrl' => 'http://yupe.ru?from=engine',
        ),
      
        'category' => array(
            'class' => 'application.modules.category.CategoryModule',
        ),
        'news' => array(
            'class' => 'application.modules.news.NewsModule',
        ),
        'portfolio' => array(
                    'class' => 'application.modules.portfolio.PortfolioModule',
                ),
        'businesstypes' => array(
                    'class' => 'application.modules.businesstypes.BusinessTypesModule',
                ),
        'user' => array(
            'class' => 'application.modules.user.UserModule',
            'documentRoot' => $_SERVER['DOCUMENT_ROOT'],
            'avatarsDir' => '/yupe/avatars',
            'avatarExtensions' => array('jpg', 'png', 'gif'),
            'notifyEmailFrom' => 'test@test.ru',
            'urlRules' => array(
              'user/people/<username:\w+>/<mode:(topics|comments)>' => 'user/people/userInfo',
              'user/people/<username:\w+>' => 'user/people/userInfo',
              'user/people/' => 'user/people/index',
            ),
        ),
        'page' => array(
            'class' => 'application.modules.page.PageModule',
            'layout' => 'application.views.layouts.column2',
        ),
        'contentblock' => array(
            'class' => 'application.modules.contentblock.ContentBlockModule',
        ),
        'feedback' => array(
            'class' => 'application.modules.feedback.FeedbackModule',
            'types' => array(
                1 => 'Ошибка на сайте',
                2 => 'Предложение о сотрудничестве',
                3 => 'Прочее..',
            ),
            'notifyEmailFrom' => 'test@test.ru',
            'backEnd' => array('email', 'db'),
            'emails'  => 'test_1@test.ru, test_2@test.ru',
        ),
       'calc' => array(
             'class' => 'application.modules.calc.CalcModule',
         ),
        // подключение gii в режиме боевой работы рекомендуется отключить (подробнее http://www.yiiframework.com/doc/guide/1.1/en/quickstart.first-app)
        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'ipFilters'=>array('*'),
            'password' => 'gbp13ltw',
            'generatorPaths'=>array(
                        'bootstrap.gii', // since 0.9.1
                    ),
        ),
    ),

    'behaviors' => array('YupeStartUpBehavior'),
);
