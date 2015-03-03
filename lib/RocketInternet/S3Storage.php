<?php

namespace RocketInternet;

// imported dependencies
use Aws\S3\Model\ClearBucket;
use Aws\S3\S3Client;
use Guzzle\Iterator\FilterIterator;

/**
 * Class S3Storage
 * @package RocketInternet
 */
class S3Storage extends Storage
{
    /**
     * S3Client instance
     *
     * @var null
     */
    protected $_instance = NULL;
    /**
     * S3Client bucket
     *
     * @var null
     */
    protected $_bucket = NULL;
    /**
     * S3Client prefix, actually dir at remote side
     *
     * @var null|string
     */
    protected $_prefix = NULL;

    /**
     * Default constructor
     *
     * @param \StdClass $options
     * @throws StorageException
     */
    public function __construct(\StdClass $options)
    {
        // check if all ok with credentials
        if (!isset($options->key) || !isset($options->secret) || !isset($options->bucket)) {
            throw new StorageException('No credentials');
        }

        $this->_instance = S3Client::factory(array(
            'key' => $options->key,
            'secret' => $options->secret,
        ));
        $this->_bucket = $options->bucket;
        $this->_prefix = isset($options->prefix) ? $options->prefix : 'assets';
    }

    /**
     *
     */
    public function getFileList()
    {
        $result = array();

        $iterator = $this->_instance->getIterator('ListObjects', array(
            'Bucket' => $this->_bucket,
            'Prefix' => $this->_prefix . '/',
        ));

        foreach ($iterator as $obj) {
            // check if not a directory
            if (substr($obj['Key'], -1) !== '/') {
                // drop prefix before
                $result[] = strtr($obj['Key'], array($this->_prefix . '/' => ''));
            }
        }

        return $result;
    }

    /**
     * @param $files
     * @return bool|mixed
     */
    public function deleteFiles(array $files)
    {
        $clear = new ClearBucket($this->_instance, $this->_bucket);
        $iterator = $this->_instance->getIterator('ListObjects', array(
            'Bucket' => $this->_bucket,
            'Prefix' => $this->_prefix . '/'
        ));

        // add prefix to path of remote files
        $files = array_map(function($str) { return $this->_prefix . '/' . $str; }, $files);

        $iterator = new FilterIterator($iterator, function ($current) use ($files) {
            return in_array($current['Key'], $files);
        });

        $clear->setIterator($iterator);

        return $clear->clear();
    }

    /**
     * @param $filename
     * @return bool|mixed
     */
    public function putFile($filename)
    {
        $basename = pathinfo($filename, PATHINFO_BASENAME);
        $upload = $this->_instance->upload($this->_bucket, $this->_prefix . '/' . $basename, file_get_contents($filename), 'public-read');
        return $upload ? $upload->get('ObjectURL') : false;
    }
}

