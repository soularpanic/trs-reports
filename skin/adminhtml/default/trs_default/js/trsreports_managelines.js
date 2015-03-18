var ProductLineController = Class.create({

    DEFAULT_NEW_PRODUCT_LINE_BUTTON_SELECTOR: '.add',

    initialize: function(args) {
        console.log("ProductLineController initializing...");
        this.newProductLineButton = args.newProductLineButton || $$(this.DEFAULT_NEW_PRODUCT_LINE_BUTTON_SELECTOR);
        this.newProductLineUrl = args.newProductLineUrl || '#';

        var context = this,
            handler = this.openNewProductLineDialog.bind(context);
        this.newProductLineButton.each(function(elt) {
            elt.observe('click', handler);
        });
    },

    openNewProductLineDialog: function() {
        var url = this.newProductLineUrl;
        Dialog.info(null, {
            closable:true,
            resizable:true,
            draggable:true,
            className:'magento',
            windowClassName:'popup-window',
            title:'Create New Product Line',
            top:50,
            width:300,
            height:150,
            zIndex:1000,
            recenterAuto:false,
            hideEffect:Element.hide,
            showEffect:Element.show,
            id:'browser_window',
            url:url,
            onClose:function (param, el) {
                window.location = window.location;
            }
        });
    }
});