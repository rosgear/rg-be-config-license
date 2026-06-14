<?php
/**
 * Этот файл является частью расширения модуля веб-приложения RosGear.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

namespace Rg\Backend\Config\License\Controller;

use Ge;
use Ge\Panel\Widget\EditWindow;
use Rg\Backend\Config\Controller\ServiceForm;

/**
 * Контроллер конфигурации службы "Лицензия".
 * 
 * Cлужба {@see \Ge\License\License}.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Config\License\Controller
 * @since 1.0
 */
class Form extends ServiceForm
{
    /**
     * Возвращает элементы панели формы (Ge.view.form.Panel GeJS).
     * 
     * @return array
     */
    protected function getFormItems(): array
    {
        /** @var \Ge\I18n\Formatter $formatter */
        $formatter = Ge::$app->formatter;
        /** @var \Ge\License\License $license */
        $license = Ge::$app->license;
        /** @var string|null Лицензионный ключ */
        $key = $license->getKey();
        // предупреждение
        $notice      = '';
        $createdDate = '';
        $period      = '';
        $ipAddress   = '';
        $domain      = '';
        $edition     = '';

        /** @var false|array $info */
        $info = $license->getInfo($key);
        // невозможно получить информацию
        if ($info === false) {
            $notice = $this->t('Error getting key') 
                    . ': <span style="margin-left:5px;color:#555">' . $license->getApi()->getLocalizedError() . '</span>';
        } else {
            // колчество дней до окончания лицензии
            if ($info['activeLeftDays']) {
                $notice = $this->t(
                    'Your license key is active, but there are {0} days left until your license expires',
                    [$info['activeLeftDays']]
                );
            } else
            // колчество дней до активации лицензии
            if ($info['activeAfterDays']) {
                $notice = $this->t(
                    'Your license key is not active, there are {0} days left until the license starts',
                    [$info['activeAfterDays']]
                );
            } else
            // если лицензии не активна
            if ($info['active'] === false)
                $notice = $this->t('Your license key is not active');
            // если лицензии активна
            else
                $notice = $this->t('Your license key is active');

            // дата создания лицензии
            $createdDate = $info['created'] ? $formatter->toDate($info['created']) : '';

            // период действия лицензии
            if (empty($licenseInfo['periodFrom']) && empty($info['periodTo']))
                $period = $this->t('no limits');
            else {
                $period   = '';
                $dateFrom = $info['periodFrom'];
                $dateTo   = $info['periodTo'];
                if ($dateFrom) {
                    $period .= $this->t('from {0}', [$formatter->toDate($dateFrom)]);
                }
                if ($dateTo) {
                    $period .= ' ' . $this->t('to {0}', [$formatter->toDate($dateTo)]);
                }
            }

            // редакция
            if (empty($info['edition']))
                $edition = $this->t('no limits');
            else
                $edition = $info['edition'];

            // IP-адрес
            if (empty($info['ipAddress']))
                $ipAddress = $this->t('no limits');
            else
                $ipAddress = $info['ipAddress'];

            // IP-адрес
            if (empty($info['domain']))
                $domain = $this->t('no limits');
            else
                $domain = $info['domain'];
        }
        return [
            [
                'xtype'      => 'textfield',
                'fieldLabel' => '#License key',
                'emptyText'  => 'XXXXXXXX-XXXXXXXX-XXXXXXXX-XXXXXXXX',
                'width'      => '100%',
                'name'       => 'key',
                'maxLength'  => 35,
                'allowBlank' => false,
                'value'      => $license->getKey()
            ],
            $notice ? [
                'xtype' => 'label',
                'ui'    => 'note',
                'html'  => $notice
            ] : [],
            [
                'xtype' => 'label',
                'ui'    => 'header-line'
            ],
            [
                'xtype'      => 'displayfield',
                'ui'         => 'parameter',
                'fieldLabel' => '#Registered in the name',
                'name'       => 'name',
                'value'      => $info['name'] ?? ''
            ],
            [
                'xtype'      => 'displayfield',
                'ui'         => 'parameter',
                'fieldLabel' => '#E-mail',
                'name'       => 'email',
                'value'      => $info['email'] ?? ''
            ],
            [
                'xtype'      => 'displayfield',
                'ui'         => 'parameter',
                'fieldLabel' => '#Phone',
                'name'       => 'phone',
                'value'      => $info['phone'] ?? ''
            ],
            [
                'xtype'      => 'displayfield',
                'ui'         => 'parameter',
                'fieldLabel' => '#Date of registration',
                'name'       => 'created',
                'value'      => $createdDate
            ],
            [
                'xtype'      => 'displayfield',
                'ui'         => 'parameter',
                'fieldLabel' => '#Validity',
                'name'       => 'period',
                'value'      => $period
            ],
            [
                'xtype'      => 'displayfield',
                'ui'         => 'parameter',
                'fieldLabel' => '#IP-address',
                'name'       => 'ip',
                'value'      => $ipAddress
            ],
            [
                'xtype'      => 'displayfield',
                'ui'         => 'parameter',
                'fieldLabel' => '#Domain',
                'name'       => 'domain',
                'value'      => $domain
            ],
            [
                'xtype'      => 'displayfield',
                'ui'         => 'parameter',
                'fieldLabel' => '#Application edition',
                'name'       => 'edition',
                'value'      => $edition
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function createWidget(): EditWindow
    {
        /** @var EditWindow $window */
        $window = parent::createWidget();

        // окно компонента (Ext.window.Window Sencha ExtJS)
        $window->autoHeight = true;
        $window->width = 500;

        // панель формы (Ge.view.form.Panel GeJS)
        $window->form->items = $this->getFormItems();
        $window->form->bodyPadding = 10;
        $window->form->defaults = [
            'labelAlign' => 'right',
            'labelWidth' => 180,
        ];
        $window->form->setStateButtons($window->form::STATE_UPDATE, [
            'help' => ['subject' => 'index'], 'save', 'cancel'
        ]);
        return $window;
    }
}
