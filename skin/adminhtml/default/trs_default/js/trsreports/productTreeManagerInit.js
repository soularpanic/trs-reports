document.observe('dom:loaded', function() {
    treeManager = new ProductTreeManager({
        newProductTreeUrl: $$('.add')[0].readAttribute('value')
        //newProductLineUrl: $('productLineControllerInfo').readAttribute('data-addLineUrl')
    });
});