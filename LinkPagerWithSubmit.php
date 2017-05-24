<?php

namespace kriss\widgets;

use Yii;
use yii\helpers\Html;

class LinkPagerWithSubmit extends \yii\widgets\LinkPager
{
    /**
     * {pageButtons} {customPage} {customPageSize}
     * custom button will be auto generate if `{customPage}` or `{customPageSize}` is exist
     */
    public $template = '{pageButtons} {customPage} {customPageSize}';
    /**
     * @var array
     */
    public $customGroupContainerOptions = ['style' => 'display:inline-block;margin:20px 0;width:120px;'];
    /**
     * @var array
     */
    public $customGroupInputOptions = ['class' => 'form-control'];
    /**
     * @var array
     */
    public $customButtonContainerOptions = ['style' => 'display:inline-block;margin:20px 0;'];
    /**
     * @var array
     */
    public $customButtonOptions = ['class' => 'btn btn-primary'];
    /**
     * @var int
     */
    public $minPageSize = 10;
    /**
     * @var int
     */
    public $maxPageSize = 100;
    /**
     * @var string
     */
    public $pageSizeLabel = 'Size';
    /**
     * @var string
     */
    public $pageLabel = 'Page';
    /**
     * @var string
     */
    public $submitButtonLabel = 'Submit';

    /**
     * @var string
     */
    private $_submitButtonId;
    /**
     * @var string
     */
    private $_pageSizeInputName;
    /**
     * @var string
     */
    private $_pageInputName;

    public function init()
    {
        parent::init();
        $this->_submitButtonId = 'submit-button-' . $this->id;
        $this->_pageInputName = 'page-input-' . $this->id;
        $this->_pageSizeInputName = 'page-size-input-' . $this->id;
        $this->registerJs();
    }

    public function run()
    {
        if ($this->registerLinkTags) {
            $this->registerLinkTags();
        }
        echo $this->renderPageContent();
    }

    /**
     * @return string
     */
    protected function renderPageContent()
    {
        $pageCount = $this->pagination->getPageCount();
        if ($pageCount < 2 && $this->hideOnSinglePage) {
            return '';
        }
        $useCustomer = false;
        $content = preg_replace_callback('/\\{([\w\-\/]+)\\}/', function ($matches) use (&$useCustomer) {
            $name = $matches[1];
            if ('pageButtons' == $name) {
                return $this->renderPageButtons();
            } else if ('customPage' == $name) {
                $useCustomer = true;
                return $this->renderCustomPage();
            } else if ('customPageSize' == $name) {
                $useCustomer = true;
                return $this->renderCustomPageSize();
            }
            return '';
        }, $this->template);
        if ($useCustomer) {
            $content .= ' ' . $this->renderCustomButton();
        }
        return $content;
    }

    /**
     * @return string
     */
    protected function renderCustomPage()
    {
        $page = 1;
        $maxCount = $this->pagination->getPageCount();
        $params = Yii::$app->getRequest()->queryParams;
        if (isset($params[$this->pagination->pageParam])) {
            $page = intval($params[$this->pagination->pageParam]);
            if ($page < 1) {
                $page = 1;
            } else if ($page > $this->pagination->getPageCount()) {
                $page = $this->pagination->getPageCount();
            }
        }
        $inputOptions = array_merge($this->customGroupInputOptions, [
            'min' => 1, 'max' => $maxCount, 'step' => 1
        ]);
        $input = Html::input('number', $this->_pageInputName, $page, $inputOptions);
        $inputGroupHtml = <<<HTML
<div class="input-group" >
    <span class="input-group-addon">$this->pageLabel</span>
    $input
</div>
HTML;
        return Html::tag('div', $inputGroupHtml, $this->customGroupContainerOptions);
    }

    /**
     * @return string
     */
    protected function renderCustomPageSize()
    {
        $pageSize = $this->pagination->defaultPageSize;
        $params = Yii::$app->getRequest()->queryParams;
        if (isset($params[$this->pagination->pageSizeParam])) {
            $pageSize = intval($params[$this->pagination->pageSizeParam]);
            if (!$pageSize) {
                $pageSize = $this->pagination->defaultPageSize;
            }
        }
        if ($pageSize < $this->minPageSize) {
            $pageSize = $this->minPageSize;
        }
        if ($pageSize > $this->maxPageSize) {
            $pageSize = $this->maxPageSize;
        }
        $inputOptions = array_merge($this->customGroupInputOptions, [
            'min' => $this->minPageSize, 'max' => $this->maxPageSize, 'step' => 1
        ]);
        $input = Html::input('number', $this->_pageSizeInputName, $pageSize, $inputOptions);
        $inputGroupHtml = <<<HTML
<div class="input-group" >
    <span class="input-group-addon">$this->pageSizeLabel</span>
    $input
</div>
HTML;
        return Html::tag('div', $inputGroupHtml, $this->customGroupContainerOptions);
    }

    /**
     * @return string
     */
    protected function renderCustomButton()
    {
        $buttonOptions = array_merge($this->customButtonOptions, [
            'id' => $this->_submitButtonId
        ]);
        $customButtonHtml = Html::tag('div',
            Html::tag('button', $this->submitButtonLabel, $buttonOptions)
            , ['class' => 'input-group']);
        return Html::tag('div', $customButtonHtml, $this->customButtonContainerOptions);
    }

    /**
     *
     */
    protected function registerJs()
    {
        // this `pageSize = 2` is must be equal pageSize in js `urlStr.replac`
        $urlStr = $this->pagination->createUrl(0, 2);
        $pageParam = $this->pagination->pageParam;
        $pageSizeParam = $this->pagination->pageSizeParam;
        $js = <<<JS
$("#$this->_submitButtonId").click(function(){
    var pageValue = $('[name="$this->_pageInputName"]').val(),
        pageSizeValue = $('[name="$this->_pageSizeInputName"]').val(),
        urlStr = '$urlStr';
    urlStr = urlStr.replace('$pageParam=1', '$pageParam='+pageValue);
    window.location.href = urlStr.replace('$pageSizeParam=2', '$pageSizeParam='+pageSizeValue);
});
JS;
        $this->getView()->registerJs($js);
    }
}