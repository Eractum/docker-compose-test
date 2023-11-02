<?php
class Route
{
    static function start()
    {
        // контроллер и действие по умолчанию
        $controllerName = 'Main';
        $actionName = 'Index';

        $routes = explode('/', $_SERVER['REQUEST_URI']);

        // получаем имя контроллера
        if ( !empty($routes[1]) )
        {
            $controllerName = $routes[1];
        }

        // получаем имя экшена
        if ( !empty($routes[2]) )
        {
            $actionName = $routes[2];
        }


        // добавляем префиксы
        $serviceName = 'Service'.$controllerName;
        $serviceFileName = 'service_'.$controllerName;
        $controllerFileName = 'controller_'.$controllerName;
        $controllerName = 'Controller'.$controllerName;
        $actionName = 'action'.$actionName;

        // подцепляем файл с классом модели (файла модели может и не быть)

        $serviceFile = strtolower($serviceFileName).'.php';
        $servicePath = "app/services/".$serviceFile;
        if(file_exists($servicePath))
        {
            include "app/services/".$serviceFile;
        }

        // подцепляем файл с классом контроллера
        $controllerFile = strtolower($controllerFileName).'.php';
        $controllerPath = "app/controllers/".$controllerFile;
        if(file_exists($controllerPath))
        {
            include "app/controllers/".$controllerFile;
        }
        else
        {
            /*
            правильно было бы кинуть здесь исключение,
            но для упрощения сразу сделаем редирект на страницу 404
            */
            Route::ErrorPage404();
        }

        // создаем контроллер
        $controller = new $controllerName;
        $action = $actionName;

        if(method_exists($controller, $action))
        {
            // вызываем действие контроллера
            $controller->$action();
        }
        else
        {
            // здесь также разумнее было бы кинуть исключение
            Route::ErrorPage404();
        }

    }

    function ErrorPage404()
    {
        $host = 'http://'.$_SERVER['HTTP_HOST'].'/';
        header('HTTP/1.1 404 Not Found');
        header("Status: 404 Not Found");
        header('Location:'.$host.'404');
    }
}