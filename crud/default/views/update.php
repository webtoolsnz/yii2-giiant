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

/**
* @var yii\web\View $this
* @var <?= ltrim($generator->modelClass, '\\') ?> $model
*/

$this->title = sprintf('Update %s: %s', $model->label(), $model->__toString());
$this->params['breadcrumbs'][] = ['label' => $model->label(2), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->__toString(), 'url' => ['view', <?= $urlParams ?>]];
$this->params['breadcrumbs'][] = <?= $generator->generateString('Edit') ?>;
?>
<div class="<?= '<?=$model->label()?>' ?>-update">

    <h1><?= '<?=$this->title ?>' ?></h1>

    <?= "<?php " ?>echo $this->render('_form', [
    'model' => $model,
    ]); ?>

</div>
