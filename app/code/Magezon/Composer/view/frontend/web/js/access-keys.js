
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/modal/confirm',
    'jquery-ui-modules/widget',
    'mage/translate'
], function ($, confirm) {
    'use strict';

    $.widget('magezon.accesskeys', {
        /**
         * Options common to all instances of this widget.
         * @type {Object}
         */
        options: {
            deleteConfirmMessage: $.mage.__('Are you sure you want to delete this key?'),
            enableConfirmMessage: $.mage.__('Are you sure you want to enable this key?'),
            disableConfirmMessage: $.mage.__('Are you sure you want to disable this key?')
        },

        /**
         * Bind event handlers for adding and deleting addresses.
         * @private
         */
        _create: function () {
            var options         = this.options,
                addAccessKey      = options.addAccessKey,
                deleteAccessKey   = options.deleteAccessKey,
                enableAccessKey   = options.enableAccessKey,
                disableAccessKey  = options.disableAccessKey;

            if (addAccessKey) {
                $(document).on('click', addAccessKey, this._addAccessKey.bind(this));
            }

            if (enableAccessKey) {
                $(document).on('click', enableAccessKey, this._enableAccessKey.bind(this));
            }

            if (disableAccessKey) {
                $(document).on('click', disableAccessKey, this._disableAccessKey.bind(this));
            }

            if (deleteAccessKey) {
                $(document).on('click', deleteAccessKey, this._deleteAccessKey.bind(this));
            }
            $(document).on('click', '.copy-access-key-public, .copy-access-key-private, .copy-access-key-mcm', this._copyText);
        },

        /**
         * Copy Access Keys to clipboard.
         * @private
         */
        _copyText:  function () {
            var container = null;
            if ($.inArray(this.className, ['copy-access-key-public', 'copy-access-key-private']) !== -1) {
                container = $(this).parents('.generated-keys').find('.access-key')[0];
            } else {
                container = $(this).parents('tr').find('.access-key')[0];
            }
            if (document.selection) {
                window.getSelection().removeAllRanges();
                var range = document.body.createTextRange();
                range.moveToElementText(container);
                range.select().createTextRange();
                document.execCommand("Copy");
            } else if (window.getSelection) {
                window.getSelection().removeAllRanges();
                var range = document.createRange();
                range.selectNode(container);
                window.getSelection().addRange(range);
                document.execCommand("Copy");
            }

            var data = {
                status: 'success',
                successMessage: 'Key was copied to the clipboard.'
            }
            var messagesContainer = $('.page.messages')
                , successMessage = data.successMessage || 'Changes were saved.'
                , errorMessage = data.errorMessage || 'Something went wrong.'
                , message = $('<div />', {
                'class': 'message'
            }).css('display', 'none');
            messagesContainer.find('div').remove();
            if (typeof data === 'undefined') {
                message.addClass('message-error error').append($('<div>', {
                    text: errorMessage
                }).attr('data-before', '\e61f'));
            } else {
                message = (data.status === 'success') ? message.addClass('message-success success').append($('<div>', {
                    text: successMessage
                }).attr('data-before', '\e610')) : message.addClass('message-error error').append($('<div>', {
                    text: errorMessage
                }).attr('data-before', '\e61f'));
            }
            messagesContainer.append(message);
            $('.message').fadeIn('fast');
        },

        /**
         * Add a new address.
         * @private
         */
        _addAccessKey: function () {
            window.location = this.options.addAccessKeyLocation;
        },

        /**
         * Delete the address whose id is specified in a data attribute after confirmation from the user.
         * @private
         * @param {jQuery.Event} e
         * @return {Boolean}
         */
        _deleteAccessKey: function (e) {
            var self = this;

            confirm({
                content: this.options.deleteConfirmMessage,
                actions: {

                    /** @inheritdoc */
                    confirm: function () {
                        if (typeof $(e.target).parent().data('accesskey') !== 'undefined') {
                            window.location = self.options.deleteUrlPrefix + $(e.target).parent().data('accesskey') +
                                '/form_key/' + $.mage.cookies.get('form_key');
                        } else {
                            window.location = self.options.deleteUrlPrefix + $(e.target).data('accesskey') +
                                '/form_key/' + $.mage.cookies.get('form_key');
                        }
                    }
                }
            });

            return false;
        },
        _enableAccessKey: function (e) {
            var self = this;

            confirm({
                content: this.options.enableConfirmMessage,
                actions: {

                    /** @inheritdoc */
                    confirm: function () {
                        if (typeof $(e.target).parent().data('accesskey') !== 'undefined') {
                            window.location = self.options.enableUrlPrefix + $(e.target).parent().data('accesskey') +
                                '/form_key/' + $.mage.cookies.get('form_key');
                        } else {
                            window.location = self.options.enableUrlPrefix + $(e.target).data('accesskey') +
                                '/form_key/' + $.mage.cookies.get('form_key');
                        }
                    }
                }
            });

            return false;
        },
        _disableAccessKey: function (e) {
            var self = this;

            confirm({
                content: this.options.disableConfirmMessage,
                actions: {

                    /** @inheritdoc */
                    confirm: function () {
                        if (typeof $(e.target).parent().data('accesskey') !== 'undefined') {
                            window.location = self.options.disableUrlPrefix + $(e.target).parent().data('accesskey') +
                                '/form_key/' + $.mage.cookies.get('form_key');
                        } else {
                            window.location = self.options.disableUrlPrefix + $(e.target).data('accesskey') +
                                '/form_key/' + $.mage.cookies.get('form_key');
                        }
                    }
                }
            });

            return false;
        }
    });

    return $.magezon.accesskeys;
});