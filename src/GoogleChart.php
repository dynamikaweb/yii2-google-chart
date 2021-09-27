<?php

namespace dynamikaweb\googlechart;

use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

class GoogleChart extends \yii\base\Widget
{
    public $title = '';

    public $estimate;

    public $columns;

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

    protected function getDataColumns()
    {
        if (empty($this->columns)) {
            return $this->dataProvider->models;
        }

        $allModels = [];

        foreach ($this->dataProvider->models as $index => $model) {
            $allModels[$index] = [];
            foreach($this->columns as $column) {
                $allModels[$index][Inflector::slug($column, '_')] = ArrayHelper::getValue($model, $column, null);
            }
        }

        return $allModels;
    }

    public function getEstimateData()
    {
        if (empty($this->estimate)) {
            return $this->dataColumns;
        }

        $allModels = [];
        $total = max(1, array_sum(array_map(fn($model) => next($model), $this->dataColumns)));
        
        foreach($this->dataColumns as $index => $model)
        {
            $keys = array_keys($model);
            $key = next($keys);
            $allModels[$index] = $model;
            $allModels[$index][$key] = ($model[$key]/$total) * 100;
        }

        return $allModels;
    }

    protected function getData()
    {
        // adapter as title as optional
        $data = array($this->title? ['label_0', $this->title]: ['label_0']);
        
        foreach($this->estimateData as $key => $model){
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
