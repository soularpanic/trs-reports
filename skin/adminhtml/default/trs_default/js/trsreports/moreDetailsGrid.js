var MoreDetailsGrid = Class.create({

    CONTAINER_PREFIX: "detailsContainer-",

    initialize: function(args) {
        console.log('starting');
        var _args = args || {},
            context = this,
            toggleHandler = this.clickMoreDetailsHandler.bind(context);
        this.moreDetailsSelector = _args.moreDetailsSelector || '.moreDetails';
        this.tableColumns = _args.tableColumns || 1;

        $$(this.moreDetailsSelector).each(function(elt) {
            elt.observe('click', toggleHandler);
        });
    },

    toggleMoreDetails: function(parentRow, containerId, url) {
        var container = $(this.CONTAINER_PREFIX + containerId);
        if (container) {
            this.closeMoreDetails(containerId);
        }
        else {
            this.openMoreDetails(parentRow, containerId, url);
        }
    },

    openMoreDetails: function(parentRow, containerId, url) {
        var tableColumns = this.tableColumns;
        var context = this;
        new Ajax.Request(url, {
            onSuccess: function(response) {
                parentRow.insert({
                    after: "<tr id=\"" + context.CONTAINER_PREFIX + containerId + "\" style=\"background:#ffffff;\"><td colspan=\"" + tableColumns + "\">" + response.responseText + "</td></tr>"
                });
            }
        })

    },

    closeMoreDetails: function(containerId) {
        var container = $(this.CONTAINER_PREFIX + containerId);
        container.remove();
    },

    clickMoreDetailsHandler: function(evt) {
        this.doToggleDetails(evt.target);
    },

    doToggleDetails: function(elt) {
        var _elt = $(elt);
        var target = _elt.up('tr');
        var url = _elt.readAttribute('data-url');
        var containerId = _elt.readAttribute('data-containerId');
        this.toggleMoreDetails(target, containerId, url);
    }
});