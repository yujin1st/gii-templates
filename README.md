# Templates for yii2-gii   

Core template extends basic gii template for a few things: 
* Added scenarios and behavior methods to model
* Commented sections in model
* AccessController added to controller
* Alert messages added to controller
* Cancel button added to form

## Install

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
$ php composer.phar require  yujin1st/gii-templates "@dev"
```

or add

```json
"yujin1st/gii-templates": "@dev"
```

to the require section of your `composer.json` file.

## Usage

Setup templates to gii configuration

```php
 $config['modules']['gii'] = [
    'class' => 'yii\gii\Module',
    'generators' => [ 
      'crud' => [
        'class' => 'yii\gii\generators\crud\Generator',
        'templates' => [
          'core' => '@yujin1st/gii/core/crud',
        ]
      ],
      'model' => [
        'class' => 'yii\gii\generators\model\Generator',
        'templates' => [
          'core' => '@yujin1st/gii/core/model',
        ]
      ]
    ],
  ];
```
