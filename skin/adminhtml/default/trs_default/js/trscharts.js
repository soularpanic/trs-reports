var TrsChart = Class.create({
    initialize: function(args) {
        console.log("aggregate panel starting");
        args = args || {};
        this.data = {};
        this.newDataEventName = args.newDataEventName || "trs:newdata";

        this._initListeners();
    },

    _initListeners: function() {
        var evtName = this.newDataEventName;
        document.observe(evtName, function(evt) {
            this.setData(evt.memo);
            this.refresh();
        }.bind(this));
    },

    setData: function(data) {
        this.data = data;
    },

    getData: function() {
        return this.data;
    },

    refresh: function() {
        console.log("Refreshing chart");

    }
});

var TrsGridChart = Class.create(TrsChart, {
    _STANDARD_FORMATTERS: {
        text: function(val) {
            return val;
        },
        int: function(val) {
            return Math.round(val);
        },
        decimal: function(val) {
            return new Number(val).toFixed(2);
        },
        currency: function(val) {
            return '$' + new Number(val).toFixed(2);
        }
    },
    initialize: function($super, args) {
        $super(args);
        this.containerId = args.containerId || 'jsonGrid';
        this.container = $(this.containerId);
        this.columnIds = this._readColumnIds();
        this.tbody = this.container.select('.grid tbody')[0];
        this.formatters = args.formatters || this._STANDARD_FORMATTERS;
    },

    _readColumnIds: function() {
        ids = [];
        this.container.select('.headings th').each(function(th) {
            var index = th.readAttribute('data-index'),
                path = th.readAttribute('data-path') || 'data',
                format = th.readAttribute('data-format') || 'text';

            ids.push({index: index, path: path, format: format});
        });
        return ids;
    },

    _clearTbody: function() {
        this.tbody.childElements().each(function (elt) { elt.remove(); })
    },

    refresh: function($super) {
        $super();
        this._clearTbody();
        var data = this.getData(),
            setIds = data.meta.ids,
            columnIds = this.columnIds,
            tbody = this.tbody,
            formatters = this.formatters;

        if (this.columnIds.length > 0) {
            var rowPath = columnIds[0].path,
                even = true;

            setIds.each(function(setId) {
                var j = 0;

                while (data[setId][rowPath][j]) {
                    var rowHtml = even ? '<tr class="even">' : '<tr>';
                    columnIds.each(function(keyPair) {
                        var val,
                            formatter,
                            formattedVal;
                        if (keyPair.path === 'meta') {
                            val = data['meta'][setId][keyPair.index];
                        }
                        else {
                            val = data[setId][keyPair.path][j][keyPair.index]
                        }
                        formatter = formatters[keyPair.format];
                        formattedVal = formatter(val);
                        rowHtml += "<td>" + formattedVal + "</td>";
                    });
                    rowHtml += '</tr>';
                    tbody.insert(rowHtml);
                    j++;
                    even = !even;
                }
            });
        }
    }
});

var TrsAggregatesChart = Class.create(TrsChart, {
    refresh: function($super) {
        $super();
        var data = this.getData();
        $("revenueCell").update(data.meta.totals.value);
        $("soldCell").update(data.meta.totals.items);
        $("ordersCell").update(data.meta.totals.orders);
    }
});

var TrsTopSoldChart = Class.create(TrsChart, {
    initialize: function($super, args) {
        args = args || {};
        $super(args);
        this.tableId = args.tableId || "topSoldChart";
    },

    refresh: function($super) {
        $super();
        var data = this.getData(),
            total = data.meta.totals.items,
            topSellers = data.meta.top_sellers,
            tableBody = $(this.tableId).select('tbody')[0];

        tableBody.update();

        topSellers.each(function(topSeller) {
            var percent = topSeller.qty / total,
                rowHtml = "<tr><td>" + topSeller.name + "</td><td>"
                    + parseFloat(topSeller.qty).toFixed(0) + "</td><td>" + (percent * 100).toFixed(2) + "</td></tr>";
            tableBody.insert({ bottom: rowHtml });
        }.bind(this));
    }
});