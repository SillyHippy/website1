class TWBB_DUPLICATE_TOOL extends FE_TOOL_FRONTEND {

    constructor() {
        super();
    }
    init() {
        super.init();
    }
    registerEvents() {
        let self = this;
        jQuery(document).on('click', '.twbb-duplicate-tool', function (event) {
            self.onToolClick(jQuery(this), event);
            self.dataPush(jQuery(this));
        });
    }

    onToolClick(tool) {
        let container = this.container;
        window.parent.$e.run('document/elements/duplicate', {
            container
        });
    }
}



let duplicate_tool;
jQuery(document).on('ready', function () {
    duplicate_tool= new TWBB_DUPLICATE_TOOL();
    window['duplicate_tool'] = duplicate_tool;
    duplicate_tool.init();
});