<?php
/**
 * Created by PhpStorm.
 * User: tobias
 * Date: 19.03.14
 * Time: 01:02
 */

namespace webtoolsnz\giiant\base;


use yii\base\Object;

class Provider extends Object
{
    /**
     * @var \webtoolsnz\giiant\crud\Generator
     */
    public $generator;
    public $columnNames = [''];
} 