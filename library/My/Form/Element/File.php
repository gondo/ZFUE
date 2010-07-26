<?php

class My_Form_Element_File extends My_Form_Element_Uploadify
{
    public function setup()
    {
        $elementID = $this->getId();
        
        $options = array('uploader'     => '/flash/uploadify.swf',
            			 'cancelImg'    => '/images/jquery.uploadify/cancel.png',
            			 'onSelect'	    => 'function() { $(\'#'.$elementID.'Upload\').show(); }',
            			 'onCancel'     => 'function() { $(\'#'.$elementID.'Upload\').hide(); }',
            			 'onComplete'   => 'function() { $(\'#'.$elementID.'Upload\').hide().parents(\'form:first\').submit(); }',
                         'myShowUpload' => false
                   );
        $this->getView()->headLink()->appendStylesheet('/styles/jquery.uploadify/uploadify.css', 'screen');
        $this->getView()->headScript()->appendFile('/scripts/lib/jquery.uploadify.v2.1.0.min.js')
                                      ->appendFile('/scripts/lib/swfobject.js')
                                      ->appendScript($this->getJavaScript($options));

        $this->addFilter('rename', $this->getRandomFileName());
    }
}