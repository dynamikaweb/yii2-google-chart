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
        // adapter as title as optional
        $data = array($this->title? ['label_0', $this->title]: ['label_0']);
        
        foreach($this->dataProvider->models as $key => $model){
            // name column
            $data [$key + 1] = [strip_tags(array_shift($model))];
            // values columns
            foreach($model as $attribute => $value) {
                $data[$key + 1][] = $value;
                // add columns legends
                if(count($data[0]) < count($data[$key + 1])) {
                    $data[0][] = $attribute;
                }
            }
        }
        
        return $data;
    }
}
