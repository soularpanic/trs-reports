document.observe('dom:loaded', function() {
    treeManager = new ProductLineController({
        newProductLineUrl: $('productLineControllerInfo').readAttribute('data-addLineUrl')
    });
});