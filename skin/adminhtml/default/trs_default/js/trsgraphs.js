var TrsDataStore = Class.create({
    initialize: function(args) {
        args = args || {};
        this.url = args.url;
        this.elt = args.elt || document;
        this.data = {};
        this.buildParameters = args.buildParameters || this._defaultBuildParameters;
    },

    getData: function() {
        return this.data;
    },

    setData: function(newData) {
        this.data = newData;
        Event.fire(this.elt, "trs:newdata", newData);
    },

    refresh: function(args) {
        var that = this;
        new Ajax.Request(this.url, {
            method: 'POST',
            parameters: that.buildParameters(args),
            onSuccess: function(resp) {
                that.setData(resp.responseText.evalJSON());
            }
        });
    },

    _defaultBuildParameters: function() {
        return { form_key: window.FORM_KEY, isAjax: true };
    }
});

var TrsGraphAxis = Class.create({
    initialize: function(args) {
        args = args || {};
        this.min = args.min || 0;
        this.max = args.max || 10;
        this.unit = args.unit || 1;
        this.label = args.label || '';
        this.dynamic = args.dynamic || false;
        this.dynamicCount = args.dynamicCount || 1;
        this.labelIncrements = args.labelIncrements || false;
        this.parser = args.parser || new TrsNoopGraphParser();
    },

    getGridIncrement: function() {
        return this.dynamic ? this.dynamicCount : this.max - this.min;
    }
});

var TrsNoopGraphParser = Class.create({
    initialize: function(args) {},
    parse: function(val) {
        return val;
    },
    format: function(val) {
        return val;
    }
});

var TrsDateGraphParser = Class.create(TrsNoopGraphParser, {
    DEFAULT_PARSE_STR: "YYYY-MM-DD",
    DEFAULT_FORMAT_STR: "YYYY-MM-DD",
    DEFAULT_PARSE_ATTR: "data-parseStr",
    DEFAULT_FORMAT_ATTR: "data-formatStr",

    initialize: function($super, args) {
        $super();
        args = args || {};
        this.parseStr = args.parseStr || this.DEFAULT_PARSE_STR;
        this.formatStr = args.formatStr || this.DEFAULT_FORMAT_STR;
        this.parseAttr = args.parseAttr || this.DEFAULT_PARSE_ATTR;
        this.formatAttr = args.formatAttr || this.DEFAULT_FORMAT_ATTR;
        this.observeElts = args.observeElts || [];

        this._initListeners(this.observeElts);
    },

    _initListeners: function(elts) {
        var parseAttr = this.parseAttr,
            formatAttr = this.formatAttr;
        elts.each(function(elt) {
            elt.observe("change", function(evt) {
                var parseStr = evt.target.getAttribute(parseAttr);
                this.setParseStr(parseStr);
                var formatStr = evt.target.getAttribute(formatAttr);
                this.setFormatStr(formatStr);
            }.bind(this));
        }.bind(this));
    },

    setParseStr: function(parseStr) {
        if (parseStr) {
            this.parseStr = parseStr;
        }
    },

    setFormatStr: function(formatStr) {
        if (formatStr) {
            this.formatStr = formatStr;
        }
    },

    parse: function(val) {
        var parsed = moment(val, this.parseStr).toDate();
        return parsed;
    },

    format: function(val) {
        var formatted = moment(val).format(this.formatStr);
        return formatted;
    }
});

var TrsGraphIndex = Class.create({
    initialize: function(args) {
        this._index = {};
        this._indexingComplete = false;
    },

    getIndex: function() {
        return this._index;
    },

    reset: function() {
        this._index = {};
        this._indexingComplete = false;
    },

    isComplete: function() {
        return this._indexingComplete;
    },

    setComplete: function() {
        this._indexingComplete = true;
    },

    add: function(key, obj, append) {
//        append = append || false;
//        var idx = append ? this._index[key] || [] : [];
//        idx.push(obj);
//        this._index[key] = idx;
        var idx = this._index[key] || [];
        idx.push(obj);
        this._index[key] = idx;
    },

    getClosest: function(loc) {
        var index = this._index,
            left = null,
            right = null;
        for (x in index) {
            if (left === null || x <= loc) {
                left = x;
            }
            if ((right === null || x < right) && x >= loc) {
                right = x;
            }
        }

        var leftDist = loc - left,
            rightDist = right - loc,
            obj = leftDist < rightDist ? index[left] : index[right];
        return obj;
    }
});

var TrsDataPrinter = Class.create({
    initialize: function(args) {
        args = args || {};
        this.key = args.key;
        this.formatter = args.formatter || null;
        this.label = args.label || null;
        this.color = args.color || null;
        this.lineIndex = args.lineIndex || 0;
        this.dataKey = args.dataKey || 'data';
    },

    getColor: function(obj) {
        if (typeof(this.color) === 'function') {
            return this.color(obj)
        }
        return this.color;
    },

    format: function(val, obj) {
        var str = '';
        if (this.label !== null) {
            if (typeof(this.label) === 'function') {
                str += this.label(val, obj);
            }
            else {
                str += this.label;
            }
            str += ': ';
        }
        str += (this.formatter === null ? val : this.formatter(val, obj));
        return str;
    },

    print: function(obj) {
//        if (obj[this.lineIndex] === undefined) {
//            return;
//        }
        return this.format(obj[this.lineIndex][this.dataKey][this.key], obj);
    }
});

var TrsGraph = Class.create({
    initialize: function(args) {
        this.args = args;
        this.args = args;
        this.canvasId = args.canvasId;
        this.canvas = $(this.canvasId);
        this.context = this.canvas.getContext('2d');
        this.newDataEventName = args.newDataEventName || "trs:newdata";
        this.textColor = args.textColor || "#FFF";
        this.font = args.font || "bold 12px sans-serif";
    },

    reset: function() {
        this.canvas.width = this.canvas.width;
    },

    drawGrid: function() {
        // console.log("I am drawing the grid.");
    },

    renderData: function() {
        // console.log("I am rendering the data.");
    },

    refreshGraph: function() {
        this.reset();
        this.drawGrid();
        this.renderData();
    },

    getContext: function() {
        return this.context;
    },

    setData: function(data) {
        this.data = data;
        //this.xIndex.reset();
        return data;
    },

    getData: function() {
        return this.data;
    }
});

var TrsLineGraph = Class.create(TrsGraph, {
    DEFAULT_LINE_COLOR_ROTATION: ['#DD4B00', '#878787', '#3859D9', '#33A300'],
    DEFAULT_HIGHTLIGHT_COLOR_ROTATION: ["#E27932"],
    initialize: function($super, args) {
        $super(args);

        this.xAxis = args.xAxis;
        this.yAxis = args.yAxis;

        this.lineKeys = args.lineKeys || ['data'];

        this.lineColors = args.lineColors || this.DEFAULT_LINE_COLOR_ROTATION;
        this.highlightColors = args.highlightColors || this.DEFAULT_HIGHTLIGHT_COLOR_ROTATION;
        this.axisColor = args.axisColor || "#EEE";

        this.popupLineHeight = args.popupLineHeight || 14;
        this.popupPadding = args.popupPadding || 2;
        this.popupColor = args.popupColor || "#131313";
        this.popupWidth = args.popupWidth || 115;

        this.printers = args.printers || [];

        this.xIndex = new TrsGraphIndex();
        this.selectedPoint = undefined;
        this._initListeners();
    },

    _initListeners: function() {
        var evtName = this.newDataEventName;
        this.canvas.observe('mousemove', this._showPointData.bind(this));
        document.observe(evtName, function(evt) {
            this.setData(this._roundRangeMinMax(evt.memo));
            this.refreshGraph();
        }.bind(this))
    },

    _showPointData: function(event) {
        var xOffset = this.canvas.viewportOffset().left,
            mouseX = event.clientX - xOffset,
            toShow = this.xIndex.getClosest(mouseX);

        if (toShow !== undefined) {
            var toShowX = toShow[0]['x'],
                colorIndex = 0;
            if (this.selectedPoint === undefined || this.selectedPoint[0]['x'] != toShowX) {
                this.selectedPoint = toShow;

                this.refreshGraph();


                toShow.each(function(point) {
                    this._highlightPoint(point.x, point.y, colorIndex);
                    colorIndex++;
                }.bind(this));

                //this._highlightPoint(toShow.x, toShow.y);
                this._drawDataPopup(toShow);
            }
        }
    },

    _drawDataPopup: function(pointData) {
        var context = this.context,
            targetX = pointData[0]['x'],
            targetY = pointData[0]['y'],
            padding = this.popupPadding,

            yIncrement = this.popupLineHeight,
            estimatedHeight = yIncrement * this.printers.length + (2 * padding),
            estimatedWidth = this.popupWidth,

            defaultColor = this.textColor,

            xRemainder = this.canvas.width - targetX - estimatedWidth - (2 * padding),
            x = (xRemainder >= 0 ? targetX : targetX - estimatedWidth),

            yRemainder = this.canvas.height - targetY - estimatedHeight - (2 * padding),
            y = (yRemainder >= 0 ? targetY : targetY + yRemainder);

        context.fillStyle = this.popupColor;
        context.fillRect(x, y, estimatedWidth, estimatedHeight);

        x += padding;
        y += padding;

        this.printers.each(function(printer) {
            var str = printer.print(pointData);
            context.fillStyle = printer.getColor(pointData) || defaultColor;
            context.fillText(str, x, y);
            y += yIncrement;
        });

    },

    _highlightPoint: function(x, y, highlightColorIndex) {
        var context = this.context,
            colorIndex = (highlightColorIndex || 0) % this.highlightColors.length;
        context.beginPath();
        context.arc(x, y, 5, 0, Math.PI * 2);
        context.closePath();
        context.strokeStyle = this.highlightColors[colorIndex];
        context.stroke();
        context.fillStyle = this.highlightColors[colorIndex];
        context.fill();
    },

    drawGrid: function($super) {
        $super();
        this._drawAxis(this.xAxis, true, this.canvas.width, this.canvas.height);
        this._drawAxis(this.yAxis, false, this.canvas.width, this.canvas.height);
    },

    _drawAxis: function(axis, isX, widthPx, heightPx) {
        var context = this.context;
        var increments = axis.getGridIncrement();
        for (var i = 0; i <= increments; i++) {
            var coords = this._calculateAxisCoords(i, axis, isX, widthPx, heightPx);

            context.moveTo(coords.x1, coords.y1);
            context.lineTo(coords.x2, coords.y2);

            if (axis.labelIncrements) {

                var labelText = this._calculateLabel(i, axis, isX);

                context.font = this.font;
                context.textBaseline = isX ? "bottom" : "top";
                context.fillText(labelText,
                    isX ? this.canvas.width - coords.x1 : coords.x1,
                    isX ? coords.y2 : coords.y1);
            }
        }
        context.strokeStyle = this.axisColor;
        context.stroke();
    },

    _calculateLabel: function(increment, axis, isX) {
        var data = this.getData();
        var totalIncrements = axis.getGridIncrement();

        var labelText = "";

        if (data && data.meta) {
            var parser = axis.parser;
            var rawMax = data.meta[isX ? "domain_max": "range_max"];
            var max = parser.parse(rawMax);

            var rawMin = data.meta[isX ? "domain_min": "range_min"];
            var min = parser.parse(rawMin);

            var offset = increment * ((max - min) / totalIncrements);
            var val = isX ? max -  offset : max - offset;
            labelText = parser.format(val);
        }

        return labelText;
    },

    _calculateAxisCoords: function(increment, axis, isX, widthPx, heightPx) {
        var totalIncrements = axis.getGridIncrement();
        var pixelIncrement = (isX ? widthPx : heightPx) / totalIncrements;
        var calculated = 0.5 + (increment * pixelIncrement);
        var coords = {
            x1: isX ? calculated : 0,
            x2: isX ? calculated : widthPx,
            y1: isX ? 0 : calculated,
            y2: isX ? heightPx : calculated
        };
        return coords;
    },

    setData: function($super, data) {
        this.xIndex.reset();
        return $super(data);
    },

    renderData: function($super) {
        $super();
        var that = this,
            context = this.getContext(),
            width = this.canvas.width,
            height = this.canvas.height,
            meta = this.data.meta,
            ids = meta.ids || [ null ],
            lineKeys = this.lineKeys,
            data = this.data,
            colors = this.lineColors,
            domainParser = this.xAxis.parser,
            domainMax = domainParser.parse(meta.domain_max),
            domainMin = domainParser.parse(meta.domain_min),
            domain = domainMax - domainMin,
            pxPerDomain = width / domain,
            rangeParser = this.yAxis.parser,
            rangeMax = rangeParser.parse(meta.range_max),
            rangeMin = rangeParser.parse(meta.range_min),
            range = rangeMax - rangeMin,
            pxPerRange = height / range,
            colorIndex = 0,
            appendIndex = false;

        // console.log("domainMax: " + domainMax + " domainMin: " + domainMin + " domain: " + domain + " pxPerDomain: " + pxPerDomain);
        ids.each(function(id) {
            lineKeys.each(function(lineKey) {
                var dataSet = id === null ? data[lineKey] : data[id][lineKey],
                    color = colors[colorIndex],
                    xPrev = null,
                    yPrev = null;
                if (dataSet.length > 0) {
                    dataSet.each(function(dataPt) {
                        var xVal = domainParser.parse(dataPt.date),
                            xOffset = xVal - domainMin,
                            x = pxPerDomain === Infinity ? width / 2 : xOffset * pxPerDomain, /* if ppd is infinite, we probably have a single data point */
                            yVal = rangeParser.parse(dataPt.sold),
                            yOffset = yVal - rangeMin,
                            yPx = yOffset * pxPerRange,
                            y = height - yPx,
                            indexData = dataPt;



                        context.beginPath();
                        if (xPrev === null && yPrev === null) {
                            xPrev = x;
                            yPrev = y;
                        }
                        context.moveTo(xPrev, yPrev);
                        context.lineTo(x, y);
                        context.closePath();
                        context.strokeStyle = color;
                        context.stroke();

                        context.beginPath();
                        context.arc(x, y, 2, 0, Math.PI * 2, false);
                        context.closePath();

                        context.stroke();
                        context.fillStyle = color;
                        context.fill();

                        if (!that.xIndex.isComplete()) {
                            indexData['color'] = color;
                            indexData['id'] = id;
                            that.xIndex.add(x, {
                                x: x,
                                y: y,
                                data: indexData
                            }, appendIndex);
                        }
                        xPrev = x;
                        yPrev = y;
                    });

                    //appendIndex = true;

                }
                else {
                    var msg = "No data available",
                        msgY = height / 2,
                        msgX = (width - (5 * msg.length)) / 2;

                    context.textBaseline = "middle";
                    context.fillText(msg, msgX, msgY);
                }
                //appendIndex = false;
                colorIndex = (colorIndex + 1) % colors.length;
            });
        });
        if (!this.xIndex.isComplete()) {
            this.xIndex.setComplete();
        }
    },

    _roundRangeMinMax: function(dataset) {
        var min = dataset.meta.range_min,
            max = dataset.meta.range_max,
            factor = 10;

        if (min > 0
            || min === undefined) {
            dataset.meta.range_min = 0;
        }

        while (max % factor != max
            && max !== undefined) {
            factor *= 10;
        }

        if ((max / factor < 0.75)
            && max !== undefined) {
            factor /= 10;
            factor = (Math.floor(max / factor) + 1) * factor;
        }

        dataset.meta.range_max = factor;
        return dataset;
    }
});

var TrsPieGraph = Class.create(TrsGraph, {
    DEFAULT_COLOR_ROTATION: ['#DD4B00', '#878787', '#3859D9', '#33A300'],

    initialize: function($super, args) {
        $super(args);
        this.dataKey = args.dataKey || 'items';
        this.colorRotation = args.colorRotation || this.DEFAULT_COLOR_ROTATION;
        this.delimitColor = args.delimitColor || '#FFF';
        this._initListeners();
    },

    _initListeners: function() {
        var evtName = this.newDataEventName;
        //this.canvas.observe('mousemove', this._showPointData.bind(this));
        document.observe(evtName, function(evt) {
            this.setData(evt.memo);
            this.refreshGraph();
        }.bind(this))
    },

    renderData: function($super) {
        $super();
        var radians = Math.PI * 2,
            context = this.context,
            pieX = this.canvas.width * 0.50,
            pieY = this.canvas.height * 0.66,
            radius = 75,
            piePadding = 10,
            legendX = piePadding, //pieX + radius + piePadding,
            legendY = 25,
            legendColorKeySize = 10,
            legendPadding = 10,
            stores = this.getData()['meta']['stores'],
            total = this.getData()['meta']['totals'][this.dataKey],
            percentComplete = 0.0,
            colorIndex = 0,
            delimitColor = this.delimitColor;

        stores.each(function(store) {
            var val = store[this.dataKey],
                percent = val / total,
                prevPercent = percentComplete,
                color = this.colorRotation[colorIndex],
                label = "(" + (percent * 100).toFixed(2) + "%) " + store.store_name;

            percentComplete = prevPercent + percent;

            context.beginPath();
            context.moveTo(pieX, pieY);
            context.arc(pieX, pieY, radius, prevPercent * radians, percentComplete * radians);
            context.lineTo(pieX, pieY);
            context.closePath();
            context.strokeStyle = delimitColor;
            context.stroke();
            context.fillStyle = color;
            context.fill();

            context.font = this.font;
            context.textBaseline = "top";
            context.fillText(label, legendX, legendY);

            colorIndex = (colorIndex + 1) % this.colorRotation.length;
            legendY = legendY + legendColorKeySize + legendPadding;
        }.bind(this));
    }
});
