<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * @var yii\web\View $this
 * @var webtoolsnz\giiant\crud\Generator $generator
 */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use dmstr\bootstrap\Tabs;

/**
* @var yii\web\View $this
* @var <?= ltrim($generator->modelClass, '\\') ?> $model
*/

$this->title = $model->__toString();
$this->params['breadcrumbs'][] = ['label' => <?=StringHelper::basename($generator->modelClass)?>::label(2), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->__toString()];
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass), '-', true) ?>-view">

    <!-- flash message -->
    <?= "<?php if (\\Yii::\$app->session->getFlash('deleteError') !== null) : ?>
        <span class=\"alert alert-info alert-dismissible\" role=\"alert\">
            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
            <span aria-hidden=\"true\">&times;</span></button>
            <?= \\Yii::\$app->session->getFlash('deleteError') ?>
        </span>
    <?php endif; ?>" ?>



    <?php $label = StringHelper::basename($generator->modelClass); ?>

    <h3><?= '<?= $model->__toString() ?>' ?></h3>

    <?= "<?= " ?>DetailView::widget([
    'model' => $model,
    'attributes' => [
    <?php
    foreach ($generator->getTableSchema()->columns as $column) {
        $format = $generator->attributeFormat($column);
        if ($format === false) {
            continue;
        } else {
            echo $format . ",\n";
        }
    }
    ?>
    ],
    ]); ?>

    <hr/>

    <div class="clearfix">
        <?= "<?= " ?>Html::a('<span class="glyphicon glyphicon-pencil"></span> ' . <?= $generator->generateString('Edit') ?>, ['update', <?= $urlParams ?>],['class' => 'btn btn-primary']) ?>
        <div class="pull-right">
            <?= "<?= " ?>Html::a('<span class="glyphicon glyphicon-trash"></span> ' . <?= $generator->generateString('Delete') ?>, ['delete', <?= $urlParams ?>],
            [
            'class' => 'btn btn-danger',
            'data-confirm' => '' . <?= $generator->generateString('Are you sure to delete this item?') ?> . '',
            'data-method' => 'post',
            ]); ?>
        </div>
    </div>

</div>
