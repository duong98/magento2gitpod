/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_Composer
 * @copyright Copyright (C) 2023 Magezon (https://www.magezon.com)
 */

define([
    'Magento_Ui/js/grid/columns/column',
    'uiRegistry'
], function (Column, registry) {
    'use strict';

    let data = registry.get('composer_version_form.composer_version_form_data_source').data;
    return Column.extend({
        defaults: {
            selectedRow: data['package_id'],
            selectedVariableType: null
        },

        /**
         * Calls 'initObservable' of parent
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            this._super().observe(['selectedRow']);
            return this;
        },
    });
});
