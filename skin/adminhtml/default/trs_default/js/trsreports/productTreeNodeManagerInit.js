Ext.onReady(function() {
    var metadata = $('productTreeNodeManagerMetadata'),
        gridDropHandler = new MagentoProductGridDropHandler({
            addUrl: metadata.readAttribute('data-addUrl')
        });

    trsTreeManager = new ProductTreeNodeManager({
        dataUrl: metadata.readAttribute('data-readUrl'),
        dropHandlers: {
            productGrid: gridDropHandler
        }
    });
});