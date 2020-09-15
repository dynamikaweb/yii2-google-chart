<?php

namespace dynamikaweb\googlechart;

use yii\helpers\ArrayHelper;

class GoogleChart extends \yii\base\Widget
{
    public $title = '';

    public $containerId;

    public $visualization;

    public $dataProvider;

    public $pluginOptions = [];

    public $clientOptions = [];

    public $htmlOptions = [];
    
    public function run()
    {
        $visualization = ArrayHelper::getValue($this->pluginOptions, 'visualization', $this->visualization);
        $clientOptions = ArrayHelper::getValue($this->pluginOptions, 'options', $this->clientOptions);
        $htmlOptions = ArrayHelper::getValue($this->pluginOptions, 'htmlOptions', $this->htmlOptions);
        $containerId = ArrayHelper::getValue($this->pluginOptions, 'containerId', $this->containerId);

        $this->pluginOptions = ArrayHelper::merge($this->pluginOptions,
            [
                'options' => ['title' => $this->title]
            ],
            [
                'data' => $this->data,
            ],
            [
                'containerId' => $containerId,
                'visualization' => $visualization,
                'htmlOptions' => $htmlOptions,  
                'options' => $clientOptions,
            ]
        );

        echo HtmlChart::widget($this->pluginOptions);
    }


    protected function getData()
    {
        $data = array(['label_0', $this->title]);
        
        foreach($this->dataProvider->models as $model){
            $data [] = [
                strip_tags(array_shift($model)),
                array_shift($model)
            ];
        }
        
        return $data;
    }
}