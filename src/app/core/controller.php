<?php
class Controller {

    public $service;
    public $view;

    function __construct()
    {
        $this->view = new View();
    }

    function actionIndex()
    {
    }
}