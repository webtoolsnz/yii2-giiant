<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * @var yii\web\View $this
 * @var yii\gii\generators\crud\Generator $generator
 */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use yii\helpers\Html;
use <?=$generator->modelClass;?>;
use yii\bootstrap\Tabs;

/**
* @var yii\web\View $this
* @var <?= ltrim($generator->modelClass, '\\') ?> $model
*/

$this->title = sprintf('Update %s: %s', <?=StringHelper::basename($generator->modelClass)?>::label(), $model->__toString());
$this->params['breadcrumbs'][] = ['label' => <?=StringHelper::basename($generator->modelClass)?>::label(2), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->__toString();
?>
<div class=<?= Inflector::camel2id(StringHelper::basename($generator->modelClass), '-', true) ?>-update">

    <h1><?= '<?=$this->title ?>' ?></h1>

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
