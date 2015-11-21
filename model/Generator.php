<?php

namespace webtoolsnz\giiant\model;

use yii\gii\CodeFile;
use yii\helpers\Inflector;
use yii\db\Schema;
use Yii;

/**
 * This generator will generate one or multiple ActiveRecord classes for the specified database table.
 *
 * @author Tobias Munk <schmunk@usrbin.de>
 * @since 0.0.1
 */
class Generator extends \yii\gii\generators\model\Generator
{
    /**
     * @var bool whether to overwrite (extended) model classes, will be always created, if file does not exist
     */
    public $generateModelClass = false;

    /**
     * @var null string for the table prefix, which is ignored in generated class name
     */
    public $tablePrefix = null;

    /**
     * @var array key-value pairs for mapping a table-name to class-name, eg. 'prefix_FOObar' => 'FooBar'
     */
    public $tableNameMap = [];

    /**
     * @var null string for the description attribute, if not provided defaults to primary key.
     */
    public  $descriptiveAttribute = null;

    /**
     * @var string
     */
    public $ignoreTables = 'migration';

    protected $classNames2;

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Giiant Model';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator generates an ActiveRecord class and base class for the specified database table.';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [['generateModelClass'], 'boolean'],
                [['tablePrefix', 'descriptiveAttribute', 'ignoreTables'], 'safe'],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function stickyAttributes()
    {
        return array_merge(parent::stickyAttributes(), ['ignoreTables']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            [
                'generateModelClass' => 'Generate Model Class',
                'descriptiveAttribute' => 'Descriptive Attribute',
                'ignoreTables' => 'Tables to ignore',
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function hints()
    {
        return array_merge(
            parent::hints(),
            [
                'generateModelClass' => 'This indicates whether the generator should generate the model class, this should usually be done only once. The model-base class is always generated.',
                'tablePrefix'        => 'Custom table prefix, eg <code>app_</code>.<br/><b>Note!</b> overrides <code>yii\db\Connection</code> prefix!',
                'descriptiveAttribute' => 'An attribute of the model that can be used to represent it in text form. eg. <b>User</b> model might use the <code>username</code> attribute to describe a user.',
                'ignoreTables' => 'A Comma delimited list of tables to exclude.',
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        return ['model.php', 'model-extended.php'];
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $files     = [];
        $relations = $this->generateRelations();
        $db        = $this->getDbConnection();
        foreach ($this->getTableNames() as $tableName) {

            $className = $this->generateClassName($tableName);
            $tableSchema = $db->getTableSchema($tableName);

            $params      = [
                'tableName'        => $tableName,
                'className'        => $className,
                'tableSchema'      => $tableSchema,
                'labels'           => $this->generateLabels($tableSchema),
                'rules'            => $this->generateRules($tableSchema),
                'relations'        => isset($relations[$tableName]) ? $relations[$tableName] : [],
                'ns'               => $this->ns,
                'searchConditions' => $this->generateSearchConditions($className, $tableSchema),
                'label'            => $this->generateLabel($className),
                'toString'         => $this->generateToString($tableSchema),
            ];

            $files[] = new CodeFile(
                Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/base/' . $className . '.php',
                $this->render('model.php', $params)
            );

            $modelClassFile = Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/' . $className . '.php';
            if ($this->generateModelClass || !is_file($modelClassFile)) {
                $files[] = new CodeFile(
                    $modelClassFile,
                    $this->render('model-extended.php', $params)
                );
            }
        }
        return $files;
    }

    /**
     * Generates a class name from the specified table name.
     *
     * @param string $tableName the table name (which may contain schema prefix)
     * @param null $useSchemaName
     * @return string the generated class name
     */
    protected function generateClassName($tableName, $useSchemaName = NULL)
    {

        #Yii::trace("Generating class name for '{$tableName}'...", __METHOD__);
        if (isset($this->classNames2[$tableName])) {
            #Yii::trace("Using '{$this->classNames2[$tableName]}' for '{$tableName}' from classNames2.", __METHOD__);
            return $this->classNames2[$tableName];
        }

        if (isset($this->tableNameMap[$tableName])) {
            Yii::trace("Converted '{$tableName}' from tableNameMap.", __METHOD__);
            return $this->classNames2[$tableName] = $this->tableNameMap[$tableName];
        }

        if (($pos = strrpos($tableName, '.')) !== false) {
            $tableName = substr($tableName, $pos + 1);
        }

        $db         = $this->getDbConnection();
        $patterns   = [];
        $patterns[] = "/^{$this->tablePrefix}(.*?)$/";
        $patterns[] = "/^(.*?){$this->tablePrefix}$/";
        $patterns[] = "/^{$db->tablePrefix}(.*?)$/";
        $patterns[] = "/^(.*?){$db->tablePrefix}$/";

        if (strpos($this->tableName, '*') !== false) {
            $pattern = $this->tableName;
            if (($pos = strrpos($pattern, '.')) !== false) {
                $pattern = substr($pattern, $pos + 1);
            }
            $patterns[] = '/^' . str_replace('*', '(\w+)', $pattern) . '$/';
        }

        $className = $tableName;
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $tableName, $matches)) {
                $className = $matches[1];
                Yii::trace("Mapping '{$tableName}' to '{$className}' from pattern '{$pattern}'.", __METHOD__);
                break;
            }
        }

        $returnName = Inflector::id2camel($className, '_');
        Yii::trace("Converted '{$tableName}' to '{$returnName}'.", __METHOD__);
        return $this->classNames2[$tableName] = $returnName;
    }

    protected function generateRelations()
    {
        $relations = parent::generateRelations();

        // inject namespace
        $ns = "\\{$this->ns}\\";
        foreach ($relations AS $model => $relInfo) {
            foreach ($relInfo AS $relName => $relData) {

                $relations[$model][$relName][0] = preg_replace(
                    '/(has[A-Za-z0-9]+\()([a-zA-Z0-9]+::)/',
                    '$1__NS__$2',
                    $relations[$model][$relName][0]
                );
                $relations[$model][$relName][0] = str_replace('__NS__', $ns, $relations[$model][$relName][0]);
            }
        }
        return $relations;
    }

    public function getTableNames()
    {
        $ignoreTables = array_filter(array_map('trim', explode(',', $this->ignoreTables)));
        $tableNames =  parent::getTableNames();

        return array_filter($tableNames, function ($v) use ($ignoreTables) {
            return !in_array($v, $ignoreTables);
        });
    }

    /**
     * Generates search conditions
     * @return array
     */
    public function generateSearchConditions($class, $tableSchema)
    {
        $columns = [];
        if (($table = $tableSchema) === false) {
            /* @var $model \yii\base\Model */
            $model = new $class();
            foreach ($model->attributes() as $attribute) {
                $columns[$attribute] = 'unknown';
            }
        } else {
            foreach ($table->columns as $column) {
                $columns[$column->name] = $column->type;
            }
        }

        $likeConditions = [];
        $hashConditions = [];
        foreach ($columns as $column => $type) {
            switch ($type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                case Schema::TYPE_BOOLEAN:
                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                    $hashConditions[] = "'{$table->name}.{$column}' => \$this->{$column},";
                    break;
                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                default:
                    $likeConditions[] = "->andFilterWhere(['like', '{$table->name}.{$column}', \$this->{$column}])";
                    break;
            }
        }

        $conditions = [];
        if (!empty($hashConditions)) {
            $conditions[] = str_repeat(' ', 8)."\$query->andFilterWhere([\n"
                . str_repeat(' ', 12) . implode("\n" . str_repeat(' ', 12), $hashConditions)
                . "\n" . str_repeat(' ', 8) . "]);\n";
        }

        if (!empty($likeConditions)) {
            $conditions[] = str_repeat(' ', 7)."\$query" . implode("\n" . str_repeat(' ', 12), $likeConditions) . ";\n";
        }

        return $conditions;
    }

    public function generateLabel($className)
    {
        $name = Inflector::camel2words($className);
        $plural = Inflector::pluralize($name);
        $singular = Inflector::singularize($name);

        return '{n, plural, =1{'.$singular.'} other{'.$plural.'}}';
    }

    public function generateToString($tableSchema)
    {
        return $this->descriptiveAttribute ? $this->descriptiveAttribute : reset($tableSchema->primaryKey);
    }

}
