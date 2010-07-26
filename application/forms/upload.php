<?php

class Form_Upload extends Zend_Form
{
    // DIRECOTRY HAVE TO BE WRITABLE
    const DIR_TMP = '/uploads/';
    const MAX_FILE_SIZE = 5120000;
    
    public function init() {
        $this->setMethod('post');
        $this->setAction('');
        $this->setEnctype('multipart/form-data');
        $this->setAttrib('accept-charset', 'UTF-8');
        
        //$file = new Zend_Form_Element_File('file');
        $file = new My_Form_Element_File('file');
        $file->setOptions(array(
            'required' => true, 
            'label' => 'Upload file:'
        ))->setDestination(realpath(APPLICATION_PATH . self::DIR_TMP))->addValidators(array(
            array('Count', 
                true, 
                1
            ), 
            array('Extension', 
                true, 
                array(
                    'csv',
                    'txt'
                )
            ), 
            array('Size', 
                true, 
                self::MAX_FILE_SIZE
            )
            /*array('MimeType', 
                true, 
                array(
                    'text/anytext', 
                    'text/comma-separated-values', 
                    'text/csv', 
                    'text/plain', 
                    'application/csv', 
                    'application/excel', 
                    'application/msexcel', 
                    'application/vnd.ms-excel', 
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 
                    'application/x-excel', 
                    'application/x-msexcel', 
                    'application/x-ms-excel', 
                    'application/xls', 
                    'application/xlt', 
                    'application/octet-stream'
                )
            )*/
        ))->addPrefixPath('My_Form_Decorator', 'My/Form/Decorator/', 'decorator')->setDecorators(array(
            'File', 
            'Description', 
            'Label', 
            array('Uploadify', 
                array(
                    'text' => 'Nahrať súbor'
                )
            ), 
            array('Errors', 
                array(
                    'placement' => 'prepend'
                )
            )
        ))->create();
        $this->addElement($file);
        
        $submit = new Zend_Form_Element_Submit('send');
        $submit->setDecorators(array('Tooltip', 
            'ViewHelper'
        ));
        $this->addElement($submit);
    }
}