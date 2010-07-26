<?php

class IndexController extends Zend_Controller_Action
{
    private $_session;
    
    public function init() {}
    
    public function preDispatch() {
        My_Form_Element_Uploadify::bypassSession();
        $this->_session = new Zend_Session_Namespace('uplodify');
    }
    
    public function indexAction() {
        // no output before this declaration
        $form = new Form_Upload();
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getParams())) {
                if (method_exists($form->getElement('file'), 'isUploadify')) {
                    if (! $form->getElement('file')->isUploadify()) {
                        // Uploadify was not used even it was meant to be, f.e. javascript was disabled
                        $form->getElement('file')->addFilter('rename', $form->getElement('file')->getRandomFileName())->receive();
                    }
                    
                    /**
                     * Here you can rename/copy/process files
                     * ideal situation is, that you upload file to temporary directory,
                     * than processes it, copy it to directory, where you want to store it
                     * and maybe rename to original filename or something else
                     */
                    
                    // filename on HDD after upload was processed
                    //echo $form->getElement('file')->getFileName() . '<br/>';
                    // if was used rename filter in library/My/Form/Element/File.php, this will return original file name
                    //echo $form->getElement('file')->getOriginalFileName() . '<br/>';
                    
                } else {
                    /**
                     * Uplodify was not used, Zend_Form_Element_File was used instead.
                     * You dont need this code when you decide to use My_Form_Element_File.
                     * If you use this code, replace "random" with your custom random file name, to prevent file collision.
                     */
                    $form->getElement('file')->addFilter('rename', 'random')->receive();
                }
                $this->_redirect('/uploaded');
            }
        }
        $this->view->form = $form;
    }
}