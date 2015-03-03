<?php 

namespace RocketInternet;

/**
 * Class Storage
 * @package RocketInternet
 */
abstract class Storage
{
    /**
     * Fetches list of files from storage
     *
     * @return mixed
     */
    abstract function getFileList();

    /**
     * Deletes group of files
     *
     * @param $files
     * @return mixed
     */
    abstract function deleteFiles(array $files);

    /**
     * Uploads file to storage
     *
     * @param $filename
     * @return mixed
     */
    abstract function putFile($filename);
}
