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
    'Magento_Ui/js/form/element/abstract',
    'uiRegistry',
    'mage/translate'
], function (Abstract, registry, $t) {
    'use strict';

    return Abstract.extend({
        defaults: {
            label: ''
        },

        initObservable: function () {
            this._super().observe(['packageLabel', 'packageLink', 'packageEditUrl']);
            return this;
        },

        changeValue: function () {
            let id = registry.get('composer_package_listing.composer_package_listing.package_columns.id').selectedRow(),
                selectedRow = this.getRowById(id);
            registry.get('index = choose_package_button').title($t('Change Package'));

            this.value(id);
            this.packageLink(this.packageEditUrl() + 'id/' + id);
            this.packageLabel(selectedRow.name);
        },

        getRowById: function (id) {
            let rows = registry.get('composer_package_listing.composer_package_listing.package_columns').rows;

            return rows.find(function (row) {
                return row['id'] === id;
            });
        },
    });
});
