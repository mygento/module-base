<?php

/**
 * @author Mygento Team
 * @copyright 2014-2019 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Block\Adminhtml\System\Config;

class AjaxButton extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $template = 'Mygento_Base::system/config/button.phtml';

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

    public function getWidgetName()
    {
        return 'ajaxButton';
    }

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
        $this->addData(
            [
                'button_label' => __($originalData['button_label']),
                'html_id' => $element->getHtmlId(),
                'ajax_url' => $this->_urlBuilder->getUrl($this->url, ['form_key' => $this->getFormKey()]),
            ]
        );

        return $this->_toHtml();
    }
}
