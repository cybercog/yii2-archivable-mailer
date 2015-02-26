<?php

/**
 * Provider class file.
 * 
 * @author Petra Barus <petra.barus@gmail.com>
 * @since 2015.02.26
 */

namespace PetraBarus\Yii2\ArchivableMailer\Providers;

/**
 * Provider provides abstract method for implementation to upload the HTML
 * and return the public URL.
 * 
 * @author Petra Barus <petra.barus@gmail.com>
 * @since 2015.02.26
 */
abstract class Provider extends \yii\base\Component {

    /**
     * @param string $html the content to be uploaded.
     * @return string the URL of the uploaded content.
     */
    public abstract function uploadHtml($headers, $html);
}
