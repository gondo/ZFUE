<?php

class My_Form_Decorator_Uploadify extends Zend_Form_Decorator_Abstract
{
    public function render( $content )
    {
        $text = $this->getOption('text');
        if($text == '')
            $text = 'Upload';
        $elementID = $this->getElement()->getId();
        $link = '<a id="'.$elementID.'Upload" style="display:none;" href="javascript:$(\'#'.$elementID.'\').uploadifyUpload();">'.$text.'</a>';
        return $content . $link;
    }
}