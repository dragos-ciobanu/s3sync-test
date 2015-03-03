<?php

namespace RocketInternet;

/**
 * Class AssetSync
 * @package RocketInternet
 */
class AssetSync
{
    protected $_storage = NULL;

    /**
     * Default constructor
     *
     * @param Storage $storage
     */
    public function __construct(Storage $storage)
    {
        $this->_storage = $storage;

    }

    /**
     * @param $localPath
     * @return array
     */
    private function _getLocalFiles($localPath){
        // get list of local files
        $oldPath = getcwd();
        chdir($localPath);
        $localFiles = glob('{*.*,*}', GLOB_BRACE);
        chdir($oldPath);

        return $localFiles;
    }

    /**
     * @return mixed
     */
    private function _getRemoteFiles() {
        return $this->_storage->getFileList();
    }
    /**
     * @param $files
     * @param $path
     * @return bool
     */
    private function _uploadFiles($files, $path)
    {
        if (!empty($files)) {
            foreach ($files as $obj) {
                $url = $this->_storage->putFile($path . '/' . $obj);
                // TODO decide what we do with link
            }
        }

        return true;
    }

    /**
     * @param $files
     * @return bool
     */
    private function _deleteFiles($files)
    {
        if (!empty($files)) {
            $this->_storage->deleteFiles($files);
        }

        return true;
    }

    /**
     * Syncronize all files from local dir to remote dir
     *
     * @param $localPath
     * @throws AssetSyncException
     */
    public function sync($localPath)
    {
        if (!is_dir($localPath) || !is_readable($localPath)) {
            throw new AssetSyncException('No access to ' . $localPath);
        }

        $localFiles = $this->_getLocalFiles($localPath);
        // if there are some files to upload
        if (!empty($localFiles)) {
            $remoteFiles = $this->_getRemoteFiles();
            if (!empty($remoteFiles)) {
                $toUpload = array_diff($localFiles, $remoteFiles);
                $this->_uploadFiles($toUpload, $localPath);

                $toDelete = array_diff($remoteFiles, $localFiles);
                $this->_deleteFiles($toDelete);
            }
        }
    }
}

