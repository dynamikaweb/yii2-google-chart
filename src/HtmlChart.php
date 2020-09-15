<?php

namespace dynamikaweb\googlechart;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;


class HtmlChart extends \yii\base\Widget
{
    public $containerId;

    public $visualization;

    public $packages = 'corechart'; 

    public $loadVersion = "1.1";

    public $data = array();

    public $options = array();
    
    public $scriptAfterArrayToDataTable = '';

    public $htmlOptions = array();

    public $tag = "div";

    public function run()
    {
        $id = ArrayHelper::getValue($this->options, 'id', $this->getId());
        // if no container is set, it will create one
        if ($this->containerId == null) {
            $this->htmlOptions['id'] = 'div-chart' . $id;
            $this->containerId = $this->htmlOptions['id'];
            echo Html::tag($this->tag, null, $this->htmlOptions);
        }
        $this->registerClientScript($id);
    }

    /**
     * Registers required scripts
     */
    public function registerClientScript($id)
    {
        $jsData = Json::encode($this->data);
        $jsOptions = Json::encode($this->options);
        
        $script = '
			google.setOnLoadCallback(drawChart' . $id . ');
			function drawChart' . $id . '() {
				var data = google.visualization.arrayToDataTable(' . $jsData . ');
				' . $this->scriptAfterArrayToDataTable . '
				var options = ' . $jsOptions . ';
				' . $id . ' = new google.visualization.' . $this->visualization . '(document.getElementById("' . $this->containerId . '"));
				' . $id . '.draw(data, options);
			}';

        $view = $this->getView();
        $view->registerJsFile('https://www.google.com/jsapi',['position' => View::POS_HEAD]);
        $view->registerJs('google.load("visualization", "' . $this->loadVersion . '", {packages:["' . $this->packages . '"]});', View::POS_HEAD, __CLASS__ . '#' . $id);
        $view->registerJs($script, View::POS_HEAD, $id);
    }
}
