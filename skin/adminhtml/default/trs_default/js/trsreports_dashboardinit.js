var dataStore, dateParser, lineGraph, pieGraph, aggregatesChart, topSoldChart;

document.observe('dom:loaded', function() {
    dataStore = new TrsDataStore({
        //'adminhtml/admin_trsreports/fetchItemsSold'
        url: $('graph_metadata').getAttribute('data-url'),
        buildParameters: function(args) {
            var args = args || {};
            return Object.extend(args, {
                form_key: window.FORM_KEY,
                is_ajax: true,
                from: $('from_date').value,
                to: $('to_date').value,
                granularity: $$('input[name="granularity"]:checked')[0].value
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
        printers: [
            new TrsDataPrinter({
                key: "date",
                label: "Date",
                formatter: function(val) {
                    return dateParser.format(val);
                }
            }),
            new TrsDataPrinter({
                key: "sold",
                label: "Sold",
                lineIndex: 0,
                formatter: function(val) {
                    return parseInt(val);
                }
            }),
            new TrsDataPrinter({
                key: "sold",
                label: "Average",
                lineIndex: 1,
                formatter: function(val) {
                    return parseInt(val);
                }
            })
        ]
    });

    pieGraph = new TrsPieGraph({
        canvasId: 'itemsPieGraph',
        dataKey: 'items'
    });

    aggregatesChart = new TrsAggregatesChart();
    topSoldChart = new TrsTopSoldChart();


    $('fetchButton').observe('click', function() {
        dataStore.refresh();
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