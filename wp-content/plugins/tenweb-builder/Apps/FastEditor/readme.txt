Things to remember for creating new tool for widgets
1. each tool main content should have 'twbb-fe-tool' class and data attributes
            data-tool='font_family' data-analytics='Font Family'

2. data-tool attribute should be the same + '_tool' in js files when creating instance from tool Class,
            because it is used for text-editor widget after remote-rendering click triggering
