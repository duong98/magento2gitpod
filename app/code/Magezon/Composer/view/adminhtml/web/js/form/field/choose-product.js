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
            this._super().observe(['productLabel', 'productLink', 'productEditUrl']);
            return this;
        },

        changeValue: function () {
            const sku = registry.get('composer_product_listing.composer_product_listing.product_columns.entity_id').selectedRow(),
                selectedRow = this.getRowById(sku);
            registry.get('index = choose_product_button').title($t('Change Product'));
            this.value(selectedRow.sku);
            this.productLink(this.productEditUrl() + 'id/' + selectedRow.entity_id);
            this.productLabel(selectedRow.name + '(' + selectedRow.sku  + ')');
        },

        getRowById: function (sku) {
            const rows = registry.get('composer_product_listing.composer_product_listing.product_columns').rows;
            return rows.find(function (row) {
                return row['sku'] === sku;
            });
        },
    });
});
