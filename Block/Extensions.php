<?php

/**
 * @author Mygento Team
 * @copyright 2014-2026 Mygento (https://www.mygento.com)
 * @package Mygento_Base
 */

namespace Mygento\Base\Block;

use Magento\Backend\Block\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\Helper\Js;
use Magento\Framework\View\LayoutFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Extensions extends Fieldset
{
    private ModuleListInterface $moduleList;
    private LayoutFactory $layoutFactory;
    private Reader $moduleReader;
    private Json $serializer;
    private File $filesystem;
    private ResolverInterface $locale;
    private BlockInterface $fieldRenderer;
    private ScopeConfigInterface $scopeConfig;

    public function __construct(
        ModuleListInterface $moduleList,
        Reader $moduleReader,
        File $filesystem,
        ResolverInterface $locale,
        Json $jsonDecoder,
        LayoutFactory $layoutFactory,
        Context $context,
        Session $authSession,
        Js $jsHelper,
        array $data = [],
    ) {
        parent::__construct($context, $authSession, $jsHelper, $data);

        $this->moduleList = $moduleList;
        $this->layoutFactory = $layoutFactory;
        $this->moduleReader = $moduleReader;
        $this->serializer = $jsonDecoder;
        $this->filesystem = $filesystem;
        $this->locale = $locale;
        $this->scopeConfig = $context->getScopeConfig();
    }

    /**
     * Render fieldset html
     */
    public function render(AbstractElement $element): string
    {
        $html = $this->_getHeaderHtml($element);

        $site = 'https://www.mygento.com';
        $email = 'hello@mygento.сom';

        $bugs = __('Please report all bugs and feature requests to %1.', $email);
        $hiretext = __(
            'You can hire us for any Magento extension customization and development.'
                . '<br/>Write us to %1',
            $email,
        );
        $tender = __('<a href="https://mygento.com/impressum" target="_blank">Legal information</a>');

        $html .= '<table class="mygento-info" cellspacing="0" cellpading="0">'
            . '<tr class="mygento-info-line">';
        $html .= '<tr><td>' . __('Support') . ':</td>' .
            '<td>' . $bugs . '</td></tr>';
        $html .= '<tr><td>' . __('License') . ':</td><td>' . $tender . '</td></tr>';
        $html .= '<tr class="mygento-info-line "><td>'
            . '<img src="https://www.mygento.com/media/wysiwyg/logo_base.png" width="100" height="100"/>'
            . '</td><td>' . $hiretext . '<br/><br/>' . __(
                'You can check all providable services on '
                    . '<a href="%1" target="_blank">our website</a>.',
                $site . '/services',
            ) . '</td></tr><tr class="mygento-info-line"></tr>';
        $html .= '</table>';

        $modules = $this->moduleList->getNames();

        $html .= '<h2>' . __('Installed Extensions') . '</h2>';
        $html .= '<ul class="mygento-mod-list">';
        sort($modules);
        foreach ($modules as $moduleName) {
            if (strstr($moduleName, 'Mygento_') === false) {
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
                Field::class,
            );
        }

        return $this->fieldRenderer;
    }

    /**
     * Read info about extension from composer json file
     * @param string $moduleCode
     * @throws FileSystemException
     * @return mixed
     */
    private function getModuleInfo(string $moduleCode)
    {
        $dir = $this->moduleReader->getModuleDir('', $moduleCode);
        $file = $dir . DIRECTORY_SEPARATOR . 'composer.json';

        try {
            $string = $this->filesystem->fileGetContents($file);
        } catch (FileSystemException $e) {
            return null;
        }

        return $this->serializer->unserialize($string);
    }

    /**
     * Get field HTML
     * @param AbstractElement $fieldset
     * @param string $moduleCode
     * @return string
     */
    private function getFieldHtml(AbstractElement $fieldset, string $moduleCode): string
    {
        $module = $this->getModuleInfo($moduleCode);
        if (
            !is_array($module) ||
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
