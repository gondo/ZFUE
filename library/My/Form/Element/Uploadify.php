<?php

/**
 * Zend Framework Uplaodify Extension
 * 
 * @author  gondo
 * @email   gondo@webdesigners.sk
 * @link    http://gondo.webdesigners.sk/zend-framework-uploadify-extension
 * @license WTFPL http://en.wikipedia.org/wiki/WTFPL
 *
 */
class My_Form_Element_Uploadify extends Zend_Form_Element_File
{
    
    const SESSION_ORIGINAL_FILE_NAME = 'original';
    const SESSION_FILE_NAME = 'fileName';
    
    /**
     * Custom Uploadify Form Element identifier
     *
     * @var unknown_type
     */
    protected $_identifier;
    
    /**
     * Random generated file name
     *
     * @var string
     */
    protected $_newName;
    
    /**
     * Determinate if Uploadify was used
     *
     * @var bool
     */
    protected $_isUploadify;
    
    /**
     * File path, to what was file uploaded via uplaodify 
     *
     * @var string
     */
    protected $_uplaodifyFile;

    /**
     * Originla file name 
     *
     * @var string
     */
    protected $_originalFile;
    
    /**
     * Setup Uploadify
     *
     */
    public function setup() {}
    
    /**
     * Initialize Uploadify
     * All custom settings are done here
     *
     */
    public function create() {
        $this->setup();
        $this->upolad();
    }
    
    /**
     * Overwritten validation method to bypass validation
     * when file was uploaded via Uploadify
     *
     * @param  string $value   File, can be optional, give null to validate all files
     * @param  mixed  $context
     * @return bool
     */
    public function isValid($value, $context = null) {
        if ($this->isUploadify()) {
            $this->_validated = true;
            return true;
        }
        return parent::isValid($value, $context);
    }
    
    /**
     * Overwritten function to return correct name when Uploadify was used
     *
     * @param  string  $value (Optional) Element or file to return
     * @param  boolean $path  (Optional) Return also the path, defaults to true
     * @return string
     */
    public function getFileName($value = null, $path = true) {
        if ($this->isUploadify()) {
            return $this->_uplaodifyFile;
        }
        return parent::getFileName($value, $path);
    }
    
    public function getOriginalFileName() {
        return $this->_originalFile;
    }
    
    /**
     * Check if file was uploaded via Uploadify
     * If so, then get file path from $_SESSION
     *
     * @return bool
     */
    public function isUploadify() {
        if ($this->_isUploadify) {
            return $this->_isUploadify;
        }
        if (isset($_SESSION[$this->getIdentifier()])) {
            $this->_uplaodifyFile = $_SESSION[$this->getIdentifier()][self::SESSION_FILE_NAME];
            $this->_originalFile = $_SESSION[$this->getIdentifier()][self::SESSION_ORIGINAL_FILE_NAME];
            unset($_SESSION[$this->getIdentifier()]);
            $this->_isUploadify = true;
            return true;
        }
        $this->_isUploadify = false;
        return false;
    }
    
    /**
     * Bypass authentification session
     *
     */
    public static function bypassSession() {
        if (isset($_POST[session_name()])) {
            $_COOKIE[session_name()] = $_POST[session_name()];
        }
    }
    
    /**
     * Create JavaScript code to load Uploadify
     * Options http://www.uploadify.com/documentation/
     * Automatically setup options:
     * 'sizeLimit', 'fileExt', 'fileDataName', 'script', 'scriptData'
     * Custom options:
     * 'myShowUpload' - bool - determinates if upload link will be displayed
     * 
     * @param array $options 
     */
    public function getJavaScript($options = null) {
        $ext = $this->getExtensionsString();
        $sizeLimit = $this->getSizeLimit();
        $scriptPath = $this->getScriptUrl();
        $fileDataName = $this->getName();
        $elementID = $this->getId();
        
        $script = 'jQuery(document).ready(function(){jQuery(\'#' . $elementID . '\').uploadify({' . "\n";
        foreach ($options as $k => $v) {
            if (substr($k, 0, 2) != 'my') {
                if ($this->isFunctionObject($v)) {
                    $script .= '\'' . $k . '\':' . $v . ',' . "\n";
                } else {
                    $script .= '\'' . $k . '\':\'' . str_replace('\'', '\\\'', $v) . '\',' . "\n";
                }
            }
        }
        if (! isset($options['fileDesc']) && $ext) {
            $script .= '\'fileDesc\':\' \',' . "\n";
        }
        if ($sizeLimit) {
            $script .= '\'sizeLimit\':\'' . $sizeLimit . '\',' . "\n";
        }
        if ($ext) {
            $script .= '\'fileExt\':\'' . $ext . '\',' . "\n";
        }
        $script .= '\'fileDataName\':\'' . $fileDataName . '\',' . "\n";
        $script .= '\'scriptData\':{\''. session_name() . '\': \'' . session_id() . '\'},' . "\n";
        $script .= '\'script\':\'' . $scriptPath . '\'' . "\n";
        $script .= '});';
        if (isset($options['myShowUpload']) && $options['myShowUpload']) {
            $script .= 'jQuery(\'#' . $elementID . 'Upload\').show();';
        }
        $script .= '});';
        
        return $script;
    }
    
    /**
     * Return string of extensions fo JavaScript output
     *
     * @return comma separated list of extensions in format *.ext
     */
    public function getExtensionsString() {
        $validator = $this->getValidator('Extension');
        if ($validator) {
            $aExt = $validator->getExtension();
            foreach ($aExt as $k => $v) {
                $aExt[$k] = '*.' . $v;
            }
            return implode(';', $aExt);
        } else {
            return null;
        }
    }
    
    /**
     * Return max file size
     *
     * @return string
     */
    public function getSizeLimit() {
        $validator = $this->getValidator('Size');
        if ($validator) {
            return $validator->getMax('raw');
        } else {
            return null;
        }
    }
    
    /**
     * Generate unique random filename
     *
     * @return string
     */
    public function getRandomFileName() {
        if (empty($this->_newName)) {
            $this->_newName = time() . rand(0, 255);
        }
        return $this->_newName;
    }
    
    /**
     * Determinates if passed value is JavaScript function or object
     *
     * @param string $v
     * @return bool
     */
    private function isFunctionObject($v) {
        if (substr($v, 0, 8) == 'function') {
            return true;
        } elseif (substr($v, 0, 1) == '{') {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Return url for JavaScript 'script' option 
     *
     * @return string
     */
    private function getScriptUrl() {
        $url = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
        if (strpos($url, '?')) {
            $url .= '&amp;' . $this->getIdentifier();
        } else {
            $url .= '?' . $this->getIdentifier();
        }
        return $url;
    }
    
    /**
     * Return Custom Uploadify Form Element Identifier
     *
     * @return string
     */
    private function getIdentifier() {
        if (empty($this->_identifier)) {
            $this->_identifier = md5($this->getId());
        }
        return $this->_identifier;
    }
    
    /**
     * Check if file was uploaded via Uploadify
     * If so, than receive uploaded file
     * Receiving automatically validate file
     *
     */
    private function upolad() {
        if (isset($_GET[$this->getIdentifier()])) {
            $this->_originalFile = parent::getFileName();
            if ($this->receive()) {
                $this->saveSession();
                //self::log('ok');
                header("HTTP/1.0 200 ok");
                // something must be returned
                die('1');
            } else {
                $this->error();
            }
        }
    }
    
    /**
     * Remember file path into session
     *
     */
    private function saveSession() {
        $_SESSION[$this->getIdentifier()][self::SESSION_FILE_NAME] = parent::getFileName();
        $_SESSION[$this->getIdentifier()][self::SESSION_ORIGINAL_FILE_NAME] = $this->_originalFile;
    }
    
    /**
     * Return error to flash
     *
     */
    private function error() {
        self::log('error');
        header("HTTP/1.0 409 uploadify error");
        die();
    }
    
    /**
     * Log anything to file
     *
     * @param string $msg
     * @param string $file
     */
    private static function log($msg, $file = '') {
        if ($file == '') {
            $file = APPLICATION_PATH . '/logs/uplodify.txt';
        }
        $msg .= "\n";
        $h = fopen($file, 'a');
        fwrite($h, $msg);
        fclose($h);
    }
}