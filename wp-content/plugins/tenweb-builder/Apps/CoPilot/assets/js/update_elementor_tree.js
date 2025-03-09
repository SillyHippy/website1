class TWBCUpdateElementorTree {
    constructor(args, main_container, action_type) {
        this.tree = args['tree'];
        this.deleted_repeated_widgets = args['deleted_repeated_widgets'];
        this.deleted_widgets_ids = args['deleted_widgets_ids'];
        this.main_container = main_container;
        this.action_type = action_type;
    }

    run() {
        this.walk(this.tree, null);
        try {
            this.deleteRepeatedWidgets();
            this.deleteWidgets();
        } catch (e) {
            console.log("TWBCUpdateElementorTree.run.error", e);
        }
    }

    walk(nodes, parent) {

        if(parent){
            this.reorderWidgets(parent);
        }

        for (let node of nodes) {
            if (node['is_new_widget'] === true) {
                if (node['repeated_data']) {
                    this.addNewRepeatedWidget(node, parent);
                } else {
                    this.addNewWidget(node, parent);
                }

            } else if (Object.values(node['settings']).length || Object.values(node['globals_to_unset']).length) {
                this.updateWidgetSettings(node, parent);
            }

            this.walk(node['children'], node);
        }
    }

    addNewWidget(node, parent_node) {
        let at = 0;
        if (parent_node === null && Object.keys(this.main_container).length === 0 ) {
            this.createEmptyContainer();
            parent_node = this.main_container;
            at = parent_node["children"].length;
        } else if (parent_node === null && this.main_container["type"] === "container") {
            parent_node = this.main_container;
            at = parent_node["children"].length;
        } else {
            parent_node = this.main_container.parent;
            for (let n of parent_node["children"]) {
                if (n["id"] === this.main_container["id"]) {
                    break;
                }
                at++;
            }
            at++;
        }


        let model = {
            "id": node["id"],
            "elType": 'widget',
            "widgetType": node["type"],
            "settings": node["settings"],
            "children": []
        }
        if ( typeof coPilot !== 'undefined' && coPilot.newAddedWidgetModelId !== 'undefined' ) {
            coPilot.newAddedWidgetModelId = node["id"];
        }
        let widget_container = window.parent.$e.commands.run('document/elements/create', {
            "container": TWBCUtils.findContainerById(parent_node["id"]),
            "model": model,
            "options": {
                "at": at
            }
        });

        console.log("Adding new widget: ", node, parent_node["id"], at);
        if(node["children"].length === 0 || !widget_container["repeaters"]){
            return;
        }

        let child_control_name = node["children"][0]["repeated_data"]["control_name"];
        if(!widget_container["repeaters"][child_control_name] || !widget_container["repeaters"][child_control_name]["children"]){
            return;
        }

        let child_count = widget_container["repeaters"][child_control_name]["children"].length;
        for (let i = 0; i <= child_count; i++) {
            window.parent.$e.commands.run('document/repeater/remove', {
                "container": widget_container,
                "index": 0,
                "name": child_control_name
            });
        }

    }

    createEmptyContainer() {
        let newContainerData = {
            elType: 'container', // Specify container type
            settings: {
                flex_direction: "column",
            },
        };

        let result = window.parent.$e.commands.run('document/elements/create', {
            container: elementor.settings.page.getEditedView().getContainer(),
            model: newContainerData,
            options: {
                at: -1, // Position at the end
            }
        });
        this.main_container = result;
    }

    addNewRepeatedWidget(node, parent_node) {
        let model = node['settings'];
        model["_id"] = node["id"];

        window.parent.$e.commands.run('document/repeater/insert', {
            "container": TWBCUtils.findContainerById(parent_node["id"]),
            "model": model,
            "name": node["repeated_data"]["control_name"]
        });

        console.log("Adding new repeated widget: ", node, parent_node["id"]);
    }

    updateWidgetSettings(node, parent_node) {

        let container
        if (node["repeated_data"]) {
            let parent_container = window.$e.components.get('document').utils.findContainerById(parent_node["id"]);

            for(let child of TWBCUtils.getChildrenContainers(parent_container)){
                if(child["id"] === node["id"]){
                    container = child;
                    break;
                }
            }

        } else {
            container = window.$e.components.get('document').utils.findContainerById(node["id"]);
        }


        if( this.action_type !== 'image_generation' ) {
            TWBCUtils.disableGlobals(container, node["globals_to_unset"]).then(function () {
                TWBCUtils.setSetting(container, node["settings"], {
                    external: true,
                    render: true,
                });
            });
        } else {
            TWBCUtils.setSetting(container, node["settings"], {
                external: true,
                render: true,
            });
        }

    }

    deleteRepeatedWidgets() {
        for (let deleted_widget of this.deleted_repeated_widgets) {
            let parent_container = TWBCUtils.findContainerById(deleted_widget["parent_widget_id"]);
            let index = this.getChildIndex(parent_container, deleted_widget["repeated_widget_id"]);

            window.parent.$e.commands.run('document/repeater/remove', {
                "container": parent_container,
                "index": index,
                "name": deleted_widget["repeater_name"]
            });
        }
    }

    deleteWidgets(){
        let containers = [];
        for (let widget_id of this.deleted_widgets_ids) {
            containers.push(TWBCUtils.findContainerById(widget_id));
        }

        window.parent.$e.commands.run('document/elements/delete', {
            "containers": containers
        });
    }

    reorderWidgets(parent_node) {
        if (parent_node["type"] !== "container" || parent_node["children"].length === 0) {
            return;
        }

        let container = TWBCUtils.findContainerById(parent_node["id"]);
        let containerModel = container.model;

        if (!containerModel) {
            console.error(`Container with ID ${parent_node["id"]} not found.`);
            return;
        }

        if(container.children.length !== parent_node["children"].length){
            return;
        }

        let widgetOrder = parent_node["children"].map(child => child["id"]);

        // Get the widgets collection of the container
        const widgetCollection = containerModel.get('elements');

        // Validate that all widgets exist in the container
        const allWidgetsExist = widgetOrder.every(widgetID =>
            widgetCollection.some(widget => widget.id === widgetID)
        );

        if (!allWidgetsExist) {
            console.error('One or more widget IDs in the order array do not exist in the container.');
            return;
        }

        // Create a new collection in the desired order
        const reorderedWidgets = widgetOrder.map(widgetID =>
            widgetCollection.find(widget => widget.id === widgetID)
        );

        // Clear the container's current widgets
        widgetCollection.reset();

        // Add the widgets in the new order
        reorderedWidgets.forEach(widgetModel => widgetCollection.add(widgetModel, {"silent": true}, true));

        container.view.render();
        $e.internal( 'document/save/set-is-modified', { status: true } )
    }

    getChildIndex(parent_container, child_id) {
        let index = 0;

        for (let child of parent_container.children) {
            if (child["id"] === child_id) {
                break;
            }
            index++;
        }

        return index;
    }
}