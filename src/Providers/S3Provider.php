<?php

/**
 * S3Provider class file.
 * 
 * @author Petra Barus <petra.barus@gmail.com>
 * @since 2015.02.26
 */

namespace PetraBarus\Yii2\ArchivableMailer\Providers;

use Aws\S3\S3Client;

/**
 * S3Provider provides implementation to upload the content to S3.
 * 
 * @author Petra Barus <petra.barus@gmail.com>
 * @since 2015.02.26
 */
class S3Provider extends Provider {

    /**
     * The bucket name.
     * @var type 
     */
    public $bucket;

    /**
     * The prefix for the path, this is useful if the same bucket is used by
     * multiple component.
     * @var string
     */
    public $directoryPath;

    /**
     * S3Client configs
     * @var array
     */
    public $config;

    /**
     * Override upload options.
     * @var array
     */
    public $uploadOptions = [];

    /**
     * @var S3Client
     */
    private $_client;

    /**
     * Initialize the S3 client.
     */
    public function init() {
        parent::init();
        $this->_client = S3Client::factory($this->config);
    }

    /**
     * @param string $html the content to be uploaded.
     * @return string the URL of the uploaded content.
     */
    public function uploadHtml($headers, $html) {
        $file = date('YmdHis-') . uniqid("", true);
        $key = (!empty($this->directoryPath) ? $this->directoryPath . '/' : '') . $file . '.html';
        $url = $this->_client->getObjectUrl($this->bucket, $key);
        $result = $this->_client->putObject(array_merge([
            'ACL' => 'public-read',
            'Bucket' => $this->bucket,
            'Body' => str_replace($this->archiveUrlTag, $url, $html),
            'CacheControl' => 'max-age=31536000, public',
            'ContentType' => 'text/html',
            'Key' => $key,
            'Metadata' => [
                'X-UID-MailHeader' => \yii\helpers\Json::encode($headers),
            ],
            'Expires' => gmdate('D, d M Y H:i:s \G\M\T', strtotime('+5 year')),
                        ], $this->uploadOptions));
        if ($result) {
            return $url;
        } else {
            return false;
        }
    }

}
