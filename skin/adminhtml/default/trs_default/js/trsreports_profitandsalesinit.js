var dataStore, dateParser, lineGraph, salesChart;

document.observe('dom:loaded', function() {
    dataStore = new TrsDataStore({
        //'adminhtml/admin_trsreports/fetchItemsSold'
        url: $('graph_metadata').getAttribute('data-url'),
        buildParameters: function(args) {
            var args = args || {},
                storeIds = '';

            $$('.trsReportStoreSelectCheckbox::checked').each(function(elt) {
                if(storeIds.length > 0) {
                    storeIds += ',';
                }
                storeIds += elt.value;
            });

            return Object.extend(args, {
                form_key: window.FORM_KEY,
                is_ajax: true,
                from: $('from_date').value,
                to: $('to_date').value,
                granularity: $$('input[name="granularity"]:checked')[0].value,
                productId: productGrid_massactionJsObject.checkedString,
                storeId: storeIds,
                ignoreSession: true
            });
        }
    });

    dateParser = new TrsDateGraphParser({
        parseStr: $$("input[name='granularity']:checked")[0].getAttribute('data-parseStr'),
        formatStr: $$("input[name='granularity']:checked")[0].getAttribute('data-formatStr'),
        observeElts: $$("input[name='granularity']")
    });
    lineGraph = new TrsLineGraph({
        canvasId: 'itemsLineGraph',
        xAxis: new TrsGraphAxis({
            dynamic: true,
            dynamicCount: 5,
            labelIncrements: true,
            parser: dateParser
        }),
        yAxis: new TrsGraphAxis({ dynamic: true, dynamicCount: 2, labelIncrements: true }),
        lineKeys: ['actual_sold', 'avg_sold'],
        popupWidth: 280

    });

    salesChart = new TrsGridChart({
        containerId: 'salesGrid'
    });

    $('fetchButton').observe('click', function() {
        dataStore.refresh();
    });

    $('productToggle').observe('click', function() {
        var elt = $('productGrid');
        elt.visible() ? Effect.SlideUp(elt) : Effect.SlideDown(elt);
    });

    $(document).observe('trs:newdata', function() {
        var elt = $('productGrid');
        if (elt.visible()) {
            Effect.SlideUp(elt);
        }

        var printers = [];
        printers.push(new TrsDataPrinter({
            key: "date",
            label: "Date",
            formatter: function(val) {
                return dateParser.format(dateParser.parse(val));
            }
        }));

        var data = dataStore.getData(),
            count = data.meta.ids.length;

        for(var i = 0; i < count; i++) {
            printers.push(
                new TrsDataPrinter({
                    key: "sold",
                    lineIndex: i * 2,
                    label: function(val, obj) {
                        var data = dataStore.getData(),
                            id = obj[this.lineIndex][this.dataKey]['id'];
                        return data['meta'][id]['label'] + ' sold';
                    },
                    formatter: function(val) {
                        return parseInt(val);
                    },
                    color: function(obj) {
                        if (obj[this.lineIndex] === undefined) {
                            return null;
                        }
                        return obj[this.lineIndex][this.dataKey]['color'];
                    }
                }),
                new TrsDataPrinter({
                    key: "sold",
                    lineIndex: i * 2 + 1,
                    label: function(val, obj) {
                        var data = dataStore.getData(),
                            id = obj[this.lineIndex][this.dataKey]['id'];
                        return data['meta'][id]['label'] + ' average';
                    },
                    formatter: function(val) {
                        return parseInt(val);
                    },
                    color: function(obj) {
                        return obj[this.lineIndex][this.dataKey]['color'];
                    }
                })
            );
        }

        lineGraph.printers = printers;


    });

    Calendar.setup({
        inputField: 'from_date',
        button: 'from_date_btn',
        ifFormat: '%Y-%m-%e'
    });

    Calendar.setup({
        inputField: 'to_date',
        button: 'to_date_btn',
        ifFormat: '%Y-%m-%e'
    });
});

var testData = {
    meta: {
        domain_max: '2014-05-14',
        domain_min: '2014-05-14',
        range_max: 4,
        range_min: 0,
        total_sold: 3,
        total_time: "56"
    },
    actual_sold: [{ date: '2014-05-14', sold: "3.0000" }],
    avg_sold: [{ date: "2014-05-14", sold: 0.4285714 }]
};