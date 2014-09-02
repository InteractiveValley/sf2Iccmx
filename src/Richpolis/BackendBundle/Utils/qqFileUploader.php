<?php
namespace Richpolis\BackendBundle\Utils;

use Symfony\Component\HttpFoundation\Request;

/**
 * Handle file uploads via XMLHttpRequest
 */
class qqUploadedFileXhr {
    var $server;
    
    public function __construct($servidor) {
        $this->server=$servidor;
    }
    
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    
    
    function save($path) {
        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);

        if ($realSize != $this->getSize()) {
            return false;
        }

        $target = fopen($path, "w");
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);

        return true;
    }

    function getName() {
        return $_GET['qqfile'];
    }

    function getSize() {
        return $this->server->get("CONTENT_LENGTH");
        
        /*if (isset($_SERVER["CONTENT_LENGTH"])) {
            return (int) $_SERVER["CONTENT_LENGTH"];
        } else {
            throw new Exception('Getting content length is not supported.');
        }*/
    }

}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class qqUploadedFileForm {

    var $file;
    var $request=null;
    
    public function __construct($file="qqfile",$request=null) {
        $this->file=$file;
        $this->request=$request;
    }
    
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {
        if (move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)) {
            return true;
        }
        return false;
    }

    function getName() {
        return $_FILES['qqfile']['name'];
    }

    function getSize() {
        return $_FILES['qqfile']['size'];
    }

}

class qqFileUploader {

    private $allowedExtensions = array();
    private $sizeLimit = 10485760;
    private $file;

    function __construct(array $allowedExtensions = array(), 
            $sizeLimit = 10485760,$server, $file="qqfile") {
        $allowedExtensions = array_map("strtolower", $allowedExtensions);

        $this->allowedExtensions = $allowedExtensions;
        $this->sizeLimit = $sizeLimit;
        
        $request = Request::createFromGlobals();
        
        if (isset($_FILES[$file])) {
            $this->file = new qqUploadedFileForm();
        } elseif (isset($_GET[$file])) {
            $this->file = new qqUploadedFileXhr($server);
        } else {
            $this->file = false;
        }
    }

    /**
     * Returns array('success'=>true) or array('error'=>'error message')
     */
    function handleUpload($uploadDirectory, $replaceOldFile = FALSE) {
        if (!is_writable($uploadDirectory)) {
            chmod($uploadDirectory, 777);

            return array('error' => "Server error. Upload directory isn't writable.");
        }

        if (!$this->file) {
            return array('error' => 'No files were uploaded.');
        }

        $size = $this->file->getSize();

        if ($size == 0) {
            return array('error' => 'File is empty');
        }

        /*if ($size > $this->sizeLimit) {
            return array('error' => 'File is too large');
        }*/

        $pathinfo = pathinfo($this->file->getName());
        $filename = $pathinfo['filename'];
        $infoarchivo = explode("-", $filename);
        if (is_array($infoarchivo)) {
            if (!isset($infoarchivo[0])) {
                $infoarchivo[0] = $filename;
            } else {
                $infoarchivo[0] = trim($infoarchivo[0]);
            }
            if (!isset($infoarchivo[1])) {
                $infoarchivo[1] = '';
            } else {
                $infoarchivo[1] = trim($infoarchivo[1]);
            }
        }

        //$filename = md5(uniqid());
        $ext = strtolower($pathinfo['extension']);

        if ($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)) {
            $these = implode(', ', $this->allowedExtensions);
            return array('error' => 'File has an invalid extension, it should be one of ' . $these . '.');
        }

        if (!$replaceOldFile) {
            /// don't overwrite previous files that were uploaded
            do {
                $filename = sha1($filename . rand(11111, 99999));
            } while (file_exists($uploadDirectory . $filename . '.' . $ext));
        }
        try {
            if ($this->file->save($uploadDirectory . $filename . '.' . $ext)) {
                return array(
                    'success' => true,
                    'filename' => $filename . '.' . $ext,
                    'original' => $pathinfo['filename'],
                    'titulo' => $infoarchivo[0],
                    'contenido' => $infoarchivo[1],
                );
            } else {
                return array('error' => 'Could not save uploaded file.' .
                    'The upload was cancelled, or server error encountered');
            }
        } catch (Exception $e) {
            return array('error' => $e->getMessage());
        }
    }

}