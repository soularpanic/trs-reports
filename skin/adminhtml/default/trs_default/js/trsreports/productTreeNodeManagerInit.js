Ext.onReady(function() {
    var metadata = $('productTreeNodeManagerMetadata'),
        addUrl = metadata.readAttribute('data-addUrl'),
        gridDropHandler = new MagentoProductGridDropHandler({ addUrl: addUrl }),
        treeDropHandler = new TreePanelContainerDropHandler({ addUrl: addUrl });

    trsTreeManager = new ProductTreeNodeManager({
        dataUrl: metadata.readAttribute('data-readUrl'),
        dropHandlers: {
            productGrid: gridDropHandler,
            treePanelContainer: treeDropHandler
        }
    });
});