<?php
/**
 * @var $migrationName string the new migration class name
 *  @var array $tableList
 *  @var array $tableRelations
 *  @var webtoolsnz\giiant\migration\Generator $generator
 */

echo "<?php\n";
?>

use yii\db\Schema;
use yii\db\Migration;

class <?= $migrationName ?> extends Migration
{
    public function safeUp()
    {
        $this->createTable('<?= ($generator->usePrefix)? $tableAlias : $tableName ?>',[<?=PHP_EOL ?>
    <?php foreach($tableColumns as $name=>$data):?>
        '<?=$name?>'=> <?=$data;?>,<?=PHP_EOL;?>
    <?php endforeach;?>
    ], '<?=$generator->tableOptions?>');

<?php if(!empty($tableIndexes) && is_array($tableIndexes)):?>
    <?php foreach($tableIndexes as $name=>$data):?>
        <?php if($name!='PRIMARY'):?>
$this->createIndex('<?=$name?>', '<?=$tableAlias?>','<?=implode(",",array_values($data['cols']))?>',<?=$data['isuniq']?>);
        <?php endif;?>
    <?php endforeach;?>
<?php endif?>

<?php if(!empty($tableRelations) && is_array($tableRelations)):?>
    <?php foreach($tableRelations as $table):?>
        <?php foreach($table['fKeys'] as $i=>$rel):?>
            $this->addForeignKey('fk_<?=$table['tableName']?>_<?=$rel['pk']?>', '<?=$table['tableAlias']?>', '<?=$rel['pk']?>', '<?=$rel['ftable']?>', '<?=$rel['fk']?>');
        <?php endforeach;?>
    <?php endforeach;?>
<?php endif?>

    }

    public function safeDown()
    {

<?php if(!empty($tableRelations) && is_array($tableRelations)):?>
    <?php foreach($tableRelations as $table):?>
        <?php foreach($table['fKeys'] as $i=>$rel):?>
$this->dropForeignKey('fk_<?=$table['tableName']?>_<?=$rel['pk']?>', '<?=$table['tableAlias']?>');
        <?php endforeach;?>
    <?php endforeach;?>
<?php endif?>

        $this->dropTable('<?= ($generator->usePrefix) ? $tableAlias : $tableName?>');

    }
}
