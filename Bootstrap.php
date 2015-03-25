<?php
/**
 * @link http://www.diemeisterei.de/
 * @copyright Copyright (c) 2014 diemeisterei GmbH, Stuttgart
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace webtoolsnz\giiant;

use yii\base\Application;
use yii\base\BootstrapInterface;


/**
 * Class Bootstrap
 * @package webtoolsnz\giiant
 * @author Tobias Munk <tobias@diemeisterei.de>
 */
class Bootstrap implements BootstrapInterface
{

    /**
     * Bootstrap method to be called during application bootstrap stage.
     *
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        if ($app->hasModule('gii')) {

            if (!isset($app->getModule('gii')->generators['giiant-model'])) {
                $app->getModule('gii')->generators['giiant-model'] = 'webtoolsnz\giiant\model\Generator';
            }
            if (!isset($app->getModule('gii')->generators['giiant-crud'])) {
                $app->getModule('gii')->generators['giiant-crud'] = 'webtoolsnz\giiant\crud\Generator';
            }
            if ($app instanceof \yii\console\Application) {
                $app->controllerMap['giiant-batch'] = 'webtoolsnz\giiant\commands\BatchController';
            }
        }
    }
}