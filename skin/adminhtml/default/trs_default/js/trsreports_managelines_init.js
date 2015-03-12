document.observe('dom:loaded', function() {
    trsLineController = new ProductLineController({
        newProductLineUrl: $('productLineControllerInfo').readAttribute('data-addLineUrl')
    });
});