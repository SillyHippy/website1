class TWBCElementorTree {
    constructor(container) {
        this.container = container; // container is an Elementor object, which contains info about widgets
        this.tree = null;
    }

    buildTree(element, parent = null) {
        let widgetSettings = new TWBCWidgetSettings(element);

        let node = {
            "settings": widgetSettings.getModifiedSettings(),
            "globals": widgetSettings.getGlobals(),
            "type": widgetSettings.getType(),
            "id": widgetSettings.getID(),
            "children": [],
            "meta" : {
                "dimensions" : widgetSettings.getContainerDimensions()
            }
        };

        if (parent === null) {
            this.tree = node;
        } else {
            parent.children.push(node);
        }

        for (let child of TWBCUtils.getChildrenContainers(element)) {
            this.buildTree(child, node)
        }
    }

    getTree() {
        if (this.tree === null) {
            this.buildTree(this.container);
        }

        return this.tree;
    }


}