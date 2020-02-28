<?php

/**
 * @author Mygento Team
 * @copyright 2014-2020 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Block\Adminhtml\System\Config;

class CronButton extends AjaxButton
{
    /**
     * @var string
     */
    protected $url = 'base/cron/schedule';

    /**
     * @var \Mygento\Base\Helper\Cron
     */
    private $cronHelper;

    /**
     * @param \Mygento\Base\Helper\Cron $cronHelper
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Mygento\Base\Helper\Cron $cronHelper,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->cronHelper = $cronHelper;
    }

    /**
     * @return string|null
     */
    public function getNote()
    {
        $data = $this->getData('original_data');
        if (!$data || !isset($data['job_code']) || !$data['job_code']) {
            return null;
        }
        $run = $this->cronHelper->isRunning($data['job_code']) ? __('Yes') : __('No');
        $result = [
            '<p>' . __('Last Update') . ': <b>' . $this->cronHelper->getLastUpdate($data['job_code']) . '</b>' . '</p>',
            '<p>' . __('Job Status') . ': <b>' . $run . '</b>' . '</p>',
        ];

        return implode(PHP_EOL, $result);
    }

    /**
     * @param mixed $fieldConfig
     * @return string
     */
    protected function getActionUrl($fieldConfig)
    {
        if (!isset($fieldConfig['job_code']) || !$fieldConfig['job_code']) {
            return '#';
        }

        return $this->_urlBuilder->getUrl(
            $this->url,
            [
                'form_key' => $this->getFormKey(),
                'job' => $fieldConfig['job_code'],
            ]
        );
    }
}
