/**
 * @api
 */
define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'jquery/ui'
], function ($, alert) {
    'use strict';

    $.widget('mage.ajaxButton', {
        options: {
            url: '',
            elementId: '',
            successText: '',
            failedText: '',
            fieldMapping: ''
        },

        /**
         * Bind handlers to events
         */
        _create: function () {
            this._on({
                'click': $.proxy(this._connect, this)
            });
        },

        /**
         * Method triggers an AJAX request to check search engine connection
         * @private
         */
        _connect: function () {
            var result = this.options.failedText;
            var element =  $('#' + this.options.elementId);
            var self = this;
            var params = {};
            var msg = '';

            element.removeClass('success').addClass('fail');

            $.ajax({
                url: this.options.url,
                showLoader: true
            }).done(function (response) {
                if (response.success) {
                    element.removeClass('fail').addClass('success');
                    if (response.successText) {
                        result = response.successText;
                    } else {
                        result = self.options.successText;
                    }
                } else {
                    msg = response.errorMessage;

                    if (msg) {
                        alert({
                            content: msg
                        });
                    }
                }
            }).always(function () {
                $('#' + self.options.elementId + '_result').text(result);
            });
        }
    });

    return $.mage.ajaxButton;
});