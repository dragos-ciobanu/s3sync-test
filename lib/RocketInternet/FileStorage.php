<?php

namespace RocketInternet;

/**
 * Class FileStorage
 * @package RocketInternet
 */
class FileStorage extends Storage {
    /**
     *
     */
    public function getFileList()
    {
        $result = array();

       // glob
        return $result;
    }

    /**
     * @param array $files
     * @return bool|mixed
     */
    public function deleteFiles(array $files){
        // unlink
        return true;
    }

    /**
     * @param $filename
     * @return bool|mixed
     */
    public function putFile($filename) {
        // copy
        return true;
    }
}
