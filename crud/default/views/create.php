<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * @var yii\web\View $this
 * @var yii\gii\generators\crud\Generator $generator
 */

echo "<?php\n";
?>

use yii\helpers\Html;
use <?=$generator->modelClass;?>;
use yii\bootstrap\Tabs;

/**
* @var yii\web\View $this
* @var <?= ltrim($generator->modelClass, '\\') ?> $model
*/

$this->title = <?= $generator->generateString('Create') ?>;
$this->params['breadcrumbs'][] = ['label' => <?=StringHelper::basename($generator->modelClass)?>::label(2), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass), '-', true) ?>-create">

    <?= "<?= Tabs::widget([
        'encodeLabels' => false,
        'items' => [
            [
                'label'   => ".StringHelper::basename($generator->modelClass)."::label(1),
                'content' => \$this->render('_form', ['model' => \$model]),
                'active'  => true,
            ],
        ]
    ]);?>"; ?>

</div>
