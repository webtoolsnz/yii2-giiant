<?php

use yii\helpers\StringHelper;

/**
 * @var yii\web\View $this
 * @var yii\gii\generators\crud\Generator $generator
 */

/** @var \yii\db\ActiveRecord $model */
$model = new $generator->modelClass;
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->getTableSchema()->columnNames;
}

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/**
* @var yii\web\View $this
* @var <?= ltrim($generator->modelClass, '\\') ?> $model
* @var yii\widgets\ActiveForm $form
*/

?>

<div class="<?= \yii\helpers\Inflector::camel2id(StringHelper::basename($generator->modelClass), '-', true) ?>-form">

    <?= "<?php " ?>$form = ActiveForm::begin([
        'id' => '<?= $model->formName() ?>',
        'layout' => '<?= $generator->formLayout ?>',
        'enableClientValidation' => false,
    ]); ?>

    <?= "<?php " ?>echo $form->errorSummary($model); ?>

    <?php foreach ($safeAttributes as $attribute) {
        if (!isset($generator->getTableSchema()->columns[$attribute])) {
            continue;
        }

        $column   = $generator->getTableSchema()->columns[$attribute];
        $prepend = $generator->prependActiveField($column, $model);
        $field = $generator->activeField($column, $model);
        $append = $generator->appendActiveField($column, $model);

        if ($prepend) {
            echo "\n    <?php " . $prepend . " ?>";
        }
        if ($field) {
            echo "\n    <?= " . $field . " ?>";
        }
        if ($append) {
            echo "\n    <?php " . $append . " ?>";
        }
    } ?>

    <hr/>

    <?= "<?= " ?>Html::a(<?= $generator->generateString('Cancel') ?>, ['index'], ['class' => 'btn btn-default']) ?>

    <p class="pull-right">
        <?= "<?= " ?>Html::submitButton('<span class="glyphicon glyphicon-check"></span> ' . ($model->isNewRecord ? <?= $generator->generateString('Create') ?> : <?= $generator->generateString('Save') ?>), [
        'id'    => 'save-' . $model->formName(),
        'class' => 'btn btn-primary'
        ]); ?>
    </p>


    <?= "<?php " ?>ActiveForm::end(); ?>

</div>
