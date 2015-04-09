var ProductTreeManager = Class.create({
    DEFAULT_NEW_PRODUCT_LINE_BUTTON_SELECTOR: '.add',

    initialize: function(args) {
        console.log("ProductTreeManager initializing...");
        this.newProductTreeButton = args.newProductTreeButton || $$(this.DEFAULT_NEW_PRODUCT_LINE_BUTTON_SELECTOR);
        this.newProductTreeUrl = args.newProductTreeUrl || '#';

        var context = this,
            handler = this.openNewProductLineDialog.bind(context);
        this.newProductTreeButton.each(function(elt) {
            elt.observe('click', handler);
        });
    },

    openNewProductLineDialog: function() {
        var url = this.newProductTreeUrl;
        Dialog.info(null, {
            closable:true,
            resizable:true,
            draggable:true,
            className:'magento',
            windowClassName:'popup-window',
            title:'Create New Product Tree',
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