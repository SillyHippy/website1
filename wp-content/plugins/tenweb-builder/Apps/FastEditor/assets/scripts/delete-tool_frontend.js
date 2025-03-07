class TWBB_DELETE_TOOL extends FE_TOOL_FRONTEND {
    constructor() {
        super();
    }

    init() {
        super.init();
    }

    registerEvents() {
        let self = this;
        jQuery(document).on('click', '.twbb-delete-tool', function () {
            self.onToolClick(jQuery(this));
            self.dataPush(jQuery(this));
        });
    }

    onToolClick(tool) {
        let container = this.container;
        window.parent.$e.run('document/elements/delete', {
            container
        });
    }
}



let delete_tool;
jQuery(document).on('ready', function () {
    delete_tool = new TWBB_DELETE_TOOL();
    window['delete_tool'] = delete_tool;
    delete_tool.init();
});