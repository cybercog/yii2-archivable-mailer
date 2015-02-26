<?php

/**
 * ArchivableBehavior class file.
 * 
 * @author Petra Barus <petra.barus@gmail.com>
 * @since 2015.02.26
 */

namespace PetraBarus\Yii2\ArchivableMailer;

/**
 * ArchivableBehavior uploads the HTML before sending.
 * 
 * @author Petra Barus <petra.barus@gmail.com>
 * @since 2015.02.26
 */
class ArchivableBehavior extends \yii\base\Behavior {

    /**
     * @var Providers\Provider
     */
    public $provider;

    /**
     * Tag inserted in the HTML content to be replaced with the archive URL.
     * @var string
     */
    public $archiveUrlTag = '{{archiveurl}}';

    /**
     * Initializes the provider.
     */
    public function init() {
        parent::init();
        $this->provider = \Yii::createObject($this->provider);
        $this->provider->archiveUrlTag = $this->archiveUrlTag;
    }

    /**
     * @return array
     */
    public function events() {
        return [
            \yii\mail\BaseMailer::EVENT_BEFORE_SEND => 'archiveMail',
        ];
    }

    /**
     * @param \yii\mail\MailEvent $event
     */
    public function archiveMail($event) {
        $message = $event->message;
        /* @var $message \yii\mail\BaseMessage */
        $html = self::getHtmlBody($message);
        $headers = self::getHeaders($message);
        if ($html !== null) {
            $url = $this->provider->uploadHtml($headers, $html);
            if ($url) {
                $message->setHtmlBody(str_replace($this->archiveUrlTag, $url,
                                $html));
            }
        }
    }

    /**
     * Get headers.
     * @param \yii\mail\BaseMessage $message
     * @return array
     */
    private static function getHeaders($message) {
        $headers = [];
        if ($message instanceof \yii\swiftmailer\Message) {
            $swiftMessage = $message->getSwiftMessage();
            foreach ($swiftMessage->getHeaders()->getAll() as $header) {
                /* @var $header \Swift_Mime_Header */
                $headers[] = $header->toString();
            }
        }
        return $headers;
    }

    /**
     * Get the HTML part from the email.
     * 
     * This is a current restriction since the BaseMessage class doesn't provide
     * method to get the rendered HTML.
     * 
     * This only works in \yii\swiftmailer\Message.
     * 
     * @param \yii\mail\BaseMessage $message
     * @return string|null the HTML string or null if not found.
     */
    private static function getHtmlBody($message) {
        if ($message instanceof \yii\swiftmailer\Message) {
            $swiftMessage = $message->getSwiftMessage();
            $r = new \ReflectionObject($swiftMessage);
            $parentClassThatHasBody = $r->getParentClass()
                    ->getParentClass()
                    ->getParentClass(); //\Swift_Mime_SimpleMimeEntity
            $body = $parentClassThatHasBody->getProperty('_immediateChildren');
            $body->setAccessible(true);
            $children = $body->getValue($swiftMessage);
            foreach ($children as $child) {
                if ($child instanceof \Swift_MimePart &&
                        $child->getContentType() == 'text/html') {
                    return $child->getBody();
                }
            }
        }
        return null;
    }

}
