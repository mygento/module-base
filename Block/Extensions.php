<?php

/**
 * @author Mygento Team
 * @copyright 2014-2019 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Block;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Extensions extends \Magento\Config\Block\System\Config\Form\Fieldset
{
    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    private $moduleList;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    private $moduleReader;

    /**
     * @var \Magento\Framework\Json\DecoderInterface
     */
    private $jsonDecoder;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    private $filesystem;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    private $locale;

    /**
     * @var \Magento\Framework\View\Element\BlockInterface
     */
    private $fieldRenderer;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param \Magento\Framework\Module\Dir\Reader $moduleReader
     * @param \Magento\Framework\Filesystem\Driver\File $filesystem
     * @param \Magento\Framework\Locale\ResolverInterface $locale
     * @param \Magento\Framework\Json\DecoderInterface $jsonDecoder
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\View\Helper\Js $jsHelper
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        \Magento\Framework\Filesystem\Driver\File $filesystem,
        \Magento\Framework\Locale\ResolverInterface $locale,
        \Magento\Framework\Json\DecoderInterface $jsonDecoder,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Backend\Block\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\View\Helper\Js $jsHelper,
        array $data = []
    ) {
        parent::__construct($context, $authSession, $jsHelper, $data);

        $this->moduleList = $moduleList;
        $this->layoutFactory = $layoutFactory;
        $this->moduleReader = $moduleReader;
        $this->jsonDecoder = $jsonDecoder;
        $this->filesystem = $filesystem;
        $this->locale = $locale;
        $this->scopeConfig = $context->getScopeConfig();
    }

    /**
     * Render fieldset html
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $html = $this->_getHeaderHtml($element);

        $site = 'https://www.mygento.net';
        $email = 'connect@mygento.net';

        if ($this->locale->getLocale() === 'ru_RU') {
            $site = 'https://www.mygento.ru';
            $email = 'connect@mygento.ru';
        }

        $ticketUrl = 'mailto:support@mygento.ru';
        $url = __(
            'Purchased extensions support is available through '
            . '<a href="%1" target="_blank">ticket tracking system</a>',
            $ticketUrl
        );
        $bugs = __('Please report all bugs and feature requests.');
        $emailtext = __(
            'If for some reasons you can not submit ticket '
            . 'to our system, you can write us an email %1.',
            $email
        );
        $hiretext = __(
            'You can hire us for any Magento extension customization and development.'
            . '<br/>Write us to %1',
            $email
        );
        $tender = __('Tender offer can be checked '
            . '<a href="https://www.mygento.ru/oferta" target="_blank">here</a>');

        $html .= '<table class="mygento-info" cellspacing="0" cellpading="0">'
            . '<tr class="mygento-info-line">';
        $html .= '<tr><td>' . __('Support') . ':</td>' .
            '<td>' . $url . '.<br/><br/>' . $bugs .
            '<br/><br/>' . $emailtext . '</td></tr>';
        $html .= '<tr><td>' . __('License') . ':</td><td>' . $tender . '</td></tr>';
        $html .= '<tr class="mygento-info-line "><td>'
            . '<img src="//www.mygento.ru/media/wysiwyg/logo_base.png" width="100" height="100"/>'
            . '</td><td>' . $hiretext . '<br/><br/>' . __(
                'You can check all providable services on '
                . '<a href="%1" target="_blank">our website</a>.',
                $site . '/services'
            ) . '</td></tr><tr class="mygento-info-line"></tr>';
        $html .= '</table>';

        $modules = $this->moduleList->getNames();

        $dispatchResult = new \Magento\Framework\DataObject($modules);
        $modules = $dispatchResult->toArray();

        $html .= '<h2>' . __('Installed Extensions') . '</h2>';
        $html .= '<ul class="mygento-mod-list">';
        sort($modules);
        foreach ($modules as $moduleName) {
            if (strstr($moduleName, 'Mygento_') === false
                || $moduleName === 'Mygento_Base'
            ) {
                continue;
            }

            $html .= $this->getFieldHtml($element, $moduleName);
        }
        $html .= '</ul>';

        $html .= $this->_getFooterHtml($element);

        return $html;
    }

    /**
     * Get Field Renderer
     */
    private function getFieldRenderer()
    {
        if (empty($this->fieldRenderer)) {
            $layout = $this->layoutFactory->create();

            $this->fieldRenderer = $layout->createBlock(
                \Magento\Config\Block\System\Config\Form\Field::class
            );
        }

        return $this->fieldRenderer;
    }

    /**
     * Read info about extension from composer json file
     * @param string $moduleCode
     * @throws \Magento\Framework\Exception\FileSystemException
     * @return mixed
     */
    private function getModuleInfo($moduleCode)
    {
        $dir = $this->moduleReader->getModuleDir('', $moduleCode);
        $file = $dir . DIRECTORY_SEPARATOR . 'composer.json';

        try {
            $string = $this->filesystem->fileGetContents($file);
        } catch (\Magento\Framework\Exception\FileSystemException $e) {
            return null;
        }

        return $this->jsonDecoder->decode($string);
    }

    /**
     * Get field HTML
     * @param AbstractElement $fieldset
     * @param string $moduleCode
     * @return string
     */
    private function getFieldHtml(AbstractElement $fieldset, $moduleCode)
    {
        $module = $this->getModuleInfo($moduleCode);
        if (!is_array($module) ||
            !array_key_exists('version', $module) ||
            !array_key_exists('description', $module)
        ) {
            return '';
        }

        $currentVer = $module['version'];
        $moduleName = $module['description'];
        $status = '<span class="mygento-icon-success"></span>';

        // in case if module output disabled
        if ($this->scopeConfig->getValue('advanced/modules_disable_output/' . $moduleCode)) {
            $status = __('Output disabled');
        }

        $field = $fieldset->addField($moduleCode, 'label', [
            'name' => 'dummy',
            'label' => $moduleName,
            'value' => $currentVer,
        ])->setRenderer($this->getFieldRenderer());

        return '<li>' . $status . $field->toHtml() . '</li>';
    }
}
