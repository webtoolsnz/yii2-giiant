<?php
/**
 * Created by PhpStorm.
 * User: tobias
 * Date: 19.03.14
 * Time: 01:02
 */

namespace badams\giiant\base;


use yii\base\Object;

class Provider extends Object
{
    /**
     * @var \badams\giiant\crud\Generator
     */
    public $generator;
    public $columnNames = [''];
} 