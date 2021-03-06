<?php
/**
 * Created by PhpStorm.
 * User: tobias
 * Date: 14.03.14
 * Time: 10:21
 */

namespace webtoolsnz\giiant\crud\providers;

use yii\db\ColumnSchema;

class EditorProvider extends \webtoolsnz\giiant\base\Provider
{
    public function activeField(ColumnSchema $attribute)
    {
        if (!isset($this->generator->getTableSchema()->columns[$attribute->name])) {
            return null;
        }
        $column = $this->generator->getTableSchema()->columns[$attribute->name];
        switch (true) {
            case (in_array($column->name, $this->columnNames)):
                $this->generator->requires[] = '2amigos/yii2-ckeditor-widget';
                return <<<EOS
\$form->field(\$model, '{$attribute->name}')->widget(
    \dosamigos\ckeditor\CKEditor::class,
    [
        'options' => ['rows' => 6],
        'preset' => 'basic'
    ]
)
EOS;
            default:
                return null;
        }
    }
} 