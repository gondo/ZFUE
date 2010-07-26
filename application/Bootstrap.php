<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected $_view;
    
    protected function _initAutoload() {
        $moduleLoader = new Zend_Application_Module_Autoloader(array(
            'namespace' => '', 
            'basePath' => APPLICATION_PATH
        ));
        
        // add custom library classes
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('My_');
        
        return $moduleLoader;
    }
    
    protected function _initViewHelpers() {
        $this->bootstrap('layout');
        $layout = $this->getResource('layout');
        $view = $layout->getView();
        $this->_view = $view;
        $view->setEncoding('UTF-8');
        $view->headScript()->appendFile('/scripts/lib/jquery-1.4.2.min.js');
        $view->doctype('HTML5');
        $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');
        $view->headTitle('ZFUE');
    }
}

