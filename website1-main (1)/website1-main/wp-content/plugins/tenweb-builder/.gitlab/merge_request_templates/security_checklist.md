### Security Checklist


- [ ] Nonce is used wherever it is necessary
- [ ] Data is properly validated/sanitized
- [ ] Data is properly escaped
- [ ] All SQL queries are properly prepared
- [ ] Appropriate hooks and filters are used
- [ ] There is no duplicated code
- [ ] Unique prefixes are used for everything defined in the public namespace (ex. options, functions, global variables, constants, post meta, etc.)
- [ ] Code is PHP 5.6 compatible (arrays are initialized as array() not [], no anonymous functions are used)
- [ ] No hard coding of scripts or styles; wp_enqueue_* is used
- [ ] Js and css files are enqueued only where needed
- [ ] WP Core-bundled scripts are used (example: jQuery)
- [ ] Code is clearly commented
- [ ] Jira task is clearly described for QA
- [ ] Functionality is tested
