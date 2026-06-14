<?php
/**
 * Этот файл является частью расширения модуля веб-приложения RosGear.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

namespace Rg\Backend\Config\License\Model;

use Ge;
use Rg\Backend\Config\Model\ServiceForm;

/**
 * Модель данных конфигурации службы "Лицензия".
 * 
 * Cлужба {@see \Ge\License\License}.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Config\License\Model
 * @since 1.0
 */
class Form extends ServiceForm
{
    /**
     * {@inheritdoc}
     */
    public function maskedAttributes(): array
    {
        return [
            'key' => 'key'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function saveConfig(?array $parameters): static
    {
        Ge::$app->license
            ->setKey($parameters['key'])
            ->save();
        return $this;
    }
}
