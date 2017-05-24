link pager
==========
link pager with page and pageSize submit

Preview
-----
![Effect picture 1](https://github.com/krissss/yii2-link-pager-with-submit/blob/master/preview.png "Effect picture 1")  

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist kriss/yii2-link-pager-with-submit "*"
```

or add

```
"kriss/yii2-link-pager-with-submit": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your GridView Or ListView and others by  :

```php
<?= yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'pager' => [
        'class' => \kriss\widgets\LinkPagerWithSubmit::className(),
    ],
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'id',
    ],
]); ?>
```
