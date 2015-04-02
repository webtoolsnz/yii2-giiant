<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * @var yii\web\View $this
 * @var webtoolsnz\giiant\crud\Generator $generator
 */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\helpers\Url;
use <?= $generator->indexWidgetType === 'grid' ? "yii\\grid\\GridView" : "yii\\widgets\\ListView" ?>;

/**
* @var yii\web\View $this
* @var yii\data\ActiveDataProvider $dataProvider
* @var <?= ltrim($generator->searchModelClass, '\\') ?> $searchModel
*/

$this->title = <?=$generator->modelClass?>::label(2);
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass), '-', true) ?>-index">

    <h1><?= '<?= $searchModel->label(2) ?>' ?></h1>

    <div class="clearfix">
        <p class="pull-left">
            <?= "<?= " ?>Html::a('<span class="glyphicon glyphicon-plus"></span> ' . <?= $generator->generateString('New') ?> . ' <?= Inflector::camel2words(StringHelper::basename($generator->modelClass)) ?>', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    </div>

    <?php if ($generator->indexWidgetType === 'grid'): ?>

    <div class="table-responsive">
        <?= "<?= " ?>GridView::widget([
            'layout' => '{summary}{pager}{items}{pager}',
            'dataProvider' => $dataProvider,
            'pager' => [
                'class' => yii\widgets\LinkPager::className(),
                'firstPageLabel' => <?= $generator->generateString('First') ?>,
                'lastPageLabel' => <?= $generator->generateString('Last') ?>,
            ],
            'filterModel' => $searchModel,
            'columns' => [<?php echo "\n";
            $count = 0;
            $model = new $generator->modelClass;
            foreach ($generator->getTableSchema()->columns as $column) {
                $format = trim($generator->columnFormat($column,$model));
                if ($format == false) continue;
                if (++$count < 8) {
                    echo "                {$format},\n";
                } else {
                    echo "                /*{$format}*/\n";
                }
            }

            echo <<<PHP
                [
                    'class' => '{$generator->actionButtonClass}',
                    'urlCreator' => function(\$action, \$model, \$key, \$index) {
                        // using the column name as key, not mapping to 'id' like the standard generator
                        \$params = is_array(\$key) ? \$key : [\$model->primaryKey()[0] => (string) \$key];
                        \$params[0] = \Yii::\$app->controller->id ? \Yii::\$app->controller->id . '/' . \$action : \$action;
                        return Url::toRoute(\$params);
                    },
                    'contentOptions' => ['nowrap'=>'nowrap']
                ],
PHP;
            echo "\n";
            ?>
            ],
        ]); ?>
    </div>

    <?php else: ?>

        <?= "<?= " ?> ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
        return Html::a(Html::encode($model-><?= $nameAttribute ?>), ['view', <?= $urlParams ?>]);
        },
        ]); ?>

    <?php endif; ?>

</div>
