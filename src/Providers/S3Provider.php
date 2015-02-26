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
        $result = $this->_client->putObject([
            'ACL' => 'public-read',
            'Bucket' => $this->bucket,
            'Body' => $html,
            'CacheControl' => 'max-age=31536000, public',
            'ContentType' => 'text/html',
            'Key' => (!empty($this->directoryPath) ? $this->directoryPath . '/' : '') . $file . '.html',
            'Metadata' => [
                'X-UID-MailHeader' => \yii\helpers\Json::encode($headers),
            ],
            'Expires' => gmdate('D, d M Y H:i:s \G\M\T', strtotime('+5 year')),
        ]);
        return $result['ObjectURL'];
    }

}
