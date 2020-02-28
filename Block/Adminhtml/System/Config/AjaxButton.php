<?php

/**
 * @author Mygento Team
 * @copyright 2014-2020 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Block\Adminhtml\System\Config;

class AjaxButton extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var string
     */
    protected $template = 'Mygento_Base::system/config/button.phtml';

    /**
     * @var string
     */
    protected $url = '';

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $newElement = clone $element;
        $newElement->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();

        return parent::render($newElement);
    }

    /**
     * @return string
     */
    public function getWidgetName()
    {
        return 'ajaxButton';
    }

    /**
     * @return string|null
     */
    public function getNote()
    {
        return null;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setTemplate($this->template);

        return $this;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $label = $originalData['button_label'] ?? 'Button Label';
        $this->addData(
            [
                'button_label' => __($label),
                'html_id' => $element->getHtmlId(),
                'ajax_url' => $this->getActionUrl($originalData),
                'original_data' => $originalData,
            ]
        );

        return $this->_toHtml();
    }

    /**
     * @param array $originalData
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function getActionUrl($originalData)
    {
        return $this->_urlBuilder->getUrl($this->url, ['form_key' => $this->getFormKey()]);
    }
}
