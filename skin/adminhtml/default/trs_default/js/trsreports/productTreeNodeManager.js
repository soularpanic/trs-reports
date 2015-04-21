var ProductTreeNodeManager = Class.create({

    initialize: function(args) {
        console.log("initializing ProductTreeNodeManager");

        var _args = args || {};
        this.dataUrl = _args.dataUrl;
        this.dropHandlers = _args.dropHandlers || {};

        if (!this.dropHandlers['treePanelContainer']) {
            this.dropHandlers['treePanelContainer'] = new TreePanelContainerDropHandler({});
        }

        this.tree = new Ext.tree.TreePanel('treePanelContainer', {
            animate: true,
            loader: new Ext.tree.TreeLoader({
                dataUrl: this.dataUrl
            }),
            enableDD: true,
            containerScroll: true
        });

        this.root = new Ext.tree.AsyncTreeNode({
            text: 'Product Trees',
            draggable:false,
            id: 'source'
        });
        this.tree.setRootNode(this.root);

        this.tree.render();
        this.root.expand();


        this.tree.dropZone.notifyDrop = function(src, evt, data) {
            console.log("We are being dropped all up ons.");
            console.log(src);
            console.log(evt);
            console.log(data);

            var srcId = src.handleElId;
            if (this.dropHandlers[srcId]) {
                this.dropHandlers[srcId].handleDrop(src, evt, data);
            }

            var handles = data.handles,
                handle = (typeof handles != "string" ? handles[0] : handles);
        }.bind(this);

        $$("#productGrid tbody tr td").each(function(elt) {
            var id = elt.up().select('td:first')[0].innerHTML;
            id = id.trim();
            Ext.dd.Registry.register(elt, {handles: id, isHandle: true});
        });

        this.dz = new Ext.dd.DragZone($('productGrid'));
        this.dz.addToGroup("TreeDD");
        this.dz.beforeDragDrop = function(trgt, evt, id) {
            console.log("hey carl");
        };
        this.dz.initFrame();
    }
});

var TRSDropHandler = Class.create({
    initialize: function(args) {
        console.log("Initializing abstract TRSDropHandler");
    },
    handleDrop: function(src, evt, data) {
        console.log("Abstract handleDrop impl message");
        console.log(src);
        console.log(evt);
        console.log(data);
    }
});

var TreePanelContainerDropHandler = Class.create(TRSDropHandler, {

    initialize: function($super, args) {
        $super(args);
        console.log("Initializing Tree Panel Container Drop Handler");
        var _args = args || {};
        this.addUrl = _args.addUrl;
    },

    handleDrop: function($super, src, evt, data) {
        $super(src, evt, data);
        console.log("Tree Panel Container Drop Handler handling drop!");

        var url = this.addUrl,
            sourceId = data.node.id,
            targetElt = Ext.dd.Registry.getTargetFromEvent(evt),
            targetId = targetElt.node.id;

        console.log("Moving node |"+sourceId+"| to |"+targetId+"|");
        new Ajax.Request(url, {
           parameters: {
               sourceId: sourceId,
               targetId: targetId
           },
            onComplete: function(resp) {
                console.log('done');
                console.log(resp);
            }
        });
    }
});

var MagentoProductGridDropHandler = Class.create(TRSDropHandler, {

    initialize: function($super, args) {
        $super(args);
        console.log("Initializing Magento Product Grid Drop Handler");
        var _args = args || {};
        this.addUrl = _args.addUrl;
    },

    handleDrop: function($super, src, evt, data) {
        $super(src, evt, data);
        console.log("Magento Product Grid Drop Handler handling drop!");

        var productId = data.handles,
            targetElt = Ext.dd.Registry.getTargetFromEvent(evt),
            targetId = targetElt.node.id;

        console.log("We need to add product ID |"+productId+"| to |"+targetId+"|");

        this.addProduct(productId, targetId);
    },

    addProduct: function(productId, targetId) {
        console.log("Add me!");
        var url = this.addUrl;
        new Ajax.Request(url, {
            parameters: {
                sourceId: productId,
                sourceType: 'product',
                targetId: targetId
            },
            onComplete: function(resp) {
                console.log("And we're done. Resp:");
                console.log(resp);
            }
        });
    }
});