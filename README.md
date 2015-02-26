# yii2-archivable-mailer
Yii2 extension for archiving mail HTML content


[![Latest Stable Version](https://poser.pugx.org/petrabarus/yii2-archivable-mailer/v/stable.svg)](https://packagist.org/packages/petrabarus/yii2-archivable-mailer)
[![Total Downloads](https://poser.pugx.org/petrabarus/yii2-archivable-mailer/downloads.svg)](https://packagist.org/packages/petrabarus/yii2-archivable-mailer)
[![Latest Unstable Version](https://poser.pugx.org/petrabarus/yii2-archivable-mailer/v/unstable.svg)](https://packagist.org/packages/petrabarus/yii2-archivable-mailer)


## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist petrabarus/yii2-archivable-mailer "*"
```

or add

```
"petrabarus/yii2-archivable-mailer": "*"
```

to the require section of your `composer.json` file.

## Usage

At the moment there is only one provider: AWS S3. Put the behavior in the 
mailer configuration.

```php
    'mailer' => [
        /* @var $mailer yii\swiftmailer\Mailer */
        'viewPath' => '@app/mail',
        'useFileTransport' => true,
        'as archivable' => [
            'class' => '\PetraBarus\Yii2\ArchivableMailer\ArchivableBehavior',
            'provider' => [
                'class' => '\PetraBarus\Yii2\ArchivableMailer\Providers\S3Provider',
                'bucket' => 'bucket',
                'directoryPath' => 'email',
                'config' => [
                    'key' => 'AKIAI123456789012345',
                    'secret' => '1234567890123456789012345678901234567890',
                    'region' => 'ap-southeast-1',
                ]
            ]
        ]
    ],
```

## Road Map

- Add more providers.