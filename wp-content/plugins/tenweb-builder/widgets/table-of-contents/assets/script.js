jQuery( window ).on( 'elementor/frontend/init', function() {
  var TOCHandler = elementorModules.frontend.handlers.Base.extend({
      getDefaultSettings: function getDefaultSettings() {
      var elementSettings = this.getElementSettings(),
          listWrapperTag = 'numbers' === elementSettings.marker_view ? 'ol' : 'ul';
      return {
        selectors: {
          widgetContainer: '.elementor-widget-container',
          postContentContainer: '.elementor:not([data-elementor-type="header"]):not([data-elementor-type="footer"]):not([data-elementor-type="popup"])',
          expandButton: '.elementor-toc__toggle-button--expand',
          collapseButton: '.elementor-toc__toggle-button--collapse',
          body: '.elementor-toc__body',
          headerTitle: '.elementor-toc__header-title'
        },
        classes: {
          anchor: 'elementor-menu-anchor',
          listWrapper: 'elementor-toc__list-wrapper',
          listItem: 'elementor-toc__list-item',
          listTextWrapper: 'elementor-toc__list-item-text-wrapper',
          firstLevelListItem: 'elementor-toc__top-level',
          listItemText: 'elementor-toc__list-item-text',
          activeItem: 'elementor-item-active',
          headingAnchor: 'elementor-toc__heading-anchor',
          collapsed: 'elementor-toc--collapsed'
        },
        listWrapperTag: listWrapperTag
      };
    },
    getDefaultElements: function getDefaultElements() {
      var settings = this.getSettings();
      return {
        $pageContainer: this.getContainer(),
        $widgetContainer: this.$element.find(settings.selectors.widgetContainer),
        $expandButton: this.$element.find(settings.selectors.expandButton),
        $collapseButton: this.$element.find(settings.selectors.collapseButton),
        $tocBody: this.$element.find(settings.selectors.body),
        $listItems: this.$element.find('.' + settings.classes.listItem)
      };
    },
    getContainer: function getContainer() {
      var settings = this.getSettings(),
          elementSettings = this.getElementSettings(); // If there is a custom container defined by the user, use it as the headings-scan container

      if (elementSettings.container) {
        return jQuery(elementSettings.container);
      } // Get the document wrapper element in which the TOC is located


      var $documentWrapper = this.$element.parents('.elementor'); // If the TOC container is a popup, only scan the popup for headings

      if ('popup' === $documentWrapper.attr('data-elementor-type')) {
        return $documentWrapper;
      } // If the TOC container is anything other than a popup, scan only the post/page content for headings


      return jQuery(settings.selectors.postContentContainer);
    },
    bindEvents: function bindEvents() {
      var _this = this;

      var elementSettings = this.getElementSettings();

      if (elementSettings.minimize_box) {
        this.elements.$expandButton.on('click', function () {
          return _this.expandBox();
        });
        this.elements.$collapseButton.on('click', function () {
          return _this.collapseBox();
        });
      }

      if (elementSettings.collapse_subitems) {
        this.elements.$listItems.on('hover', function (event) {
          return jQuery(event.target).slideToggle();
        });
      }
    },
    getHeadings: function getHeadings() {
    // Get all headings from document by user-selected tags
    var elementSettings = this.getElementSettings(),
        tags = elementSettings.headings_by_tags.join(','),
        selectors = this.getSettings('selectors'),
        excludedSelectors = elementSettings.exclude_headings_by_selector;
    return this.elements.$pageContainer.find(tags).not(selectors.headerTitle).filter(function (index, heading) {
      return !jQuery(heading).closest(excludedSelectors).length; // Handle excluded selectors if there are any
    });
  },
    addAnchorsBeforeHeadings: function addAnchorsBeforeHeadings() {
    var _this2 = this;

    var classes = this.getSettings('classes'); // Add an anchor element right before each TOC heading to create anchors for TOC links

    this.elements.$headings.before(function (index) {
      // Check if the heading element itself has an ID, or if it is a widget which includes a main heading element, whether the widget wrapper has an ID
      if (jQuery(_this2.elements.$headings[index]).data('hasOwnID')) {
        return;
      }

      return "<span id=\"".concat(classes.headingAnchor, "-").concat(index, "\" class=\"").concat(classes.anchor, " \"></span>");
    });
  },
    activateItem: function activateItem($listItem) {
    var classes = this.getSettings('classes');
    this.deactivateActiveItem($listItem);
    $listItem.addClass(classes.activeItem);
    this.$activeItem = $listItem;

    if (!this.getElementSettings('collapse_subitems')) {
      return;
    }

    var $activeList;

    if ($listItem.hasClass(classes.firstLevelListItem)) {
      $activeList = $listItem.parent().next();
    } else {
      $activeList = $listItem.parents('.' + classes.listWrapper).eq(-2);
    }

    if (!$activeList.length) {
      delete this.$activeList;
      return;
    }

    this.$activeList = $activeList;
    this.$activeList.stop().slideDown();
  },
    deactivateActiveItem: function deactivateActiveItem($activeToBe) {
    if (!this.$activeItem || this.$activeItem.is($activeToBe)) {
      return;
    }

    var _this$getSettings = this.getSettings(),
        classes = _this$getSettings.classes;

    this.$activeItem.removeClass(classes.activeItem);

    if (this.$activeList && (!$activeToBe || !this.$activeList[0].contains($activeToBe[0]))) {
      this.$activeList.slideUp();
    }
  },
    followAnchor: function followAnchor($element, index) {
    var _this3 = this;

    var anchorSelector = $element[0].hash;
    var $anchor;

    try {
      // `decodeURIComponent` for UTF8 characters in the hash.
      $anchor = jQuery(decodeURIComponent(anchorSelector));
    } catch (e) {
      return;
    }

    elementorFrontend.waypoint($anchor, function (direction) {
      if (_this3.itemClicked) {
        return;
      }

      var id = $anchor.attr('id');

      if ('down' === direction) {
        _this3.viewportItems[id] = true;

        _this3.activateItem($element);
      } else {
        delete _this3.viewportItems[id];

        _this3.activateItem(_this3.$listItemTexts.eq(index - 1));
      }
    }, {
      offset: 'bottom-in-view',
      triggerOnce: false
    });
    elementorFrontend.waypoint($anchor, function (direction) {
      if (_this3.itemClicked) {
        return;
      }

      var id = $anchor.attr('id');

      if ('down' === direction) {
        delete _this3.viewportItems[id];

        if ((_this3.viewportItems).length) {
          _this3.activateItem(_this3.$listItemTexts.eq(index + 1));
        }
      } else {
        _this3.viewportItems[id] = true;

        _this3.activateItem($element);
      }
    }, {
      offset: 0,
      triggerOnce: false
    });
  },
    followAnchors: function followAnchors() {
    var _this4 = this;

    this.$listItemTexts.each(function (index, element) {
      return _this4.followAnchor(jQuery(element), index);
    });
  },
    populateTOC: function populateTOC() {
    this.listItemPointer = 0;
    var elementSettings = this.getElementSettings();

    if (elementSettings.hierarchical_view) {
      this.createNestedList();
    } else {
      this.createFlatList();
    }

    this.$listItemTexts = this.$element.find('.elementor-toc__list-item-text');
    this.$listItemTexts.on('click', this.onListItemClick.bind(this));

    if (!elementorFrontend.isEditMode()) {
      this.followAnchors();
    }
  },
    createNestedList: function createNestedList() {
    var _this5 = this;

    this.headingsData.forEach(function (heading, index) {
      heading.level = 0;

      for (var i = index - 1; i >= 0; i--) {
        var currentOrderedItem = _this5.headingsData[i];

        if (currentOrderedItem.tag <= heading.tag) {
          heading.level = currentOrderedItem.level;

          if (currentOrderedItem.tag < heading.tag) {
            heading.level++;
          }

          break;
        }
      }
    });
    this.elements.$tocBody.html(this.getNestedLevel(0));
  },
    createFlatList: function createFlatList() {
    this.elements.$tocBody.html(this.getNestedLevel());
  },
    getNestedLevel: function getNestedLevel(level) {
    var settings = this.getSettings(),
        elementSettings = this.getElementSettings(),
        icon = this.getElementSettings('icon'); // Open new list/nested list

    var html = "<".concat(settings.listWrapperTag, " class=\"").concat(settings.classes.listWrapper, "\">"); // for each list item, build its markup.

    while (this.listItemPointer < this.headingsData.length) {
      var currentItem = this.headingsData[this.listItemPointer];
      var listItemTextClasses = settings.classes.listItemText;

      if (0 === currentItem.level) {
        // If the current list item is a top level item, give it the first level class
        listItemTextClasses += ' ' + settings.classes.firstLevelListItem;
      }

      if (level > currentItem.level) {
        break;
      }

      if (level === currentItem.level) {
        html += "<li class=\"".concat(settings.classes.listItem, "\">");
        html += "<div class=\"".concat(settings.classes.listTextWrapper, "\">");
        var liContent = "<a href=\"#".concat(currentItem.anchorLink, "\" class=\"").concat(listItemTextClasses, "\">").concat(currentItem.text, "</a>"); // If list type is bullets, add the bullet icon as an <i> tag

        if ('bullets' === elementSettings.marker_view && icon) {
          liContent = "<i class=\"".concat(icon.value, "\"></i>").concat(liContent);
        }

        html += liContent;
        html += '</div>';
        this.listItemPointer++;
        var nextItem = this.headingsData[this.listItemPointer];

        if (nextItem && level < nextItem.level) {
          // If a new nested list has to be created under the current item,
          // this entire method is called recursively (outside the while loop, a list wrapper is created)
          html += this.getNestedLevel(nextItem.level);
        }

        html += '</li>';
      }
    }

    html += "</".concat(settings.listWrapperTag, ">");
    return html;
  },
    handleNoHeadingsFound: function handleNoHeadingsFound() {
      var noHeadingsText = 'No headings were found on this page.';

    // var noHeadingsText = elementorProFrontend.config.i18n['toc_no_headings_found'];
    //
    // if (elementorFrontend.isEditMode()) {
    //   noHeadingsText = elementorPro.translate('toc_no_headings_found');
    // }

    return this.elements.$tocBody.html(noHeadingsText);
  },
    collapseOnInit: function collapseOnInit() {
    var minimizedOn = this.getElementSettings('minimized_on'),
        currentDeviceMode = elementorFrontend.getCurrentDeviceMode();

    if ('tablet' === minimizedOn && 'desktop' !== currentDeviceMode || 'mobile' === minimizedOn && 'mobile' === currentDeviceMode) {
      this.collapseBox();
    }
  },
    getHeadingAnchorLink: function getHeadingAnchorLink(index, classes) {
    var headingID = this.elements.$headings[index].id,
        wrapperID = this.elements.$headings[index].closest('.elementor-widget').id;
    var anchorLink = '';

    if (headingID) {
      anchorLink = headingID;
    } else if (wrapperID) {
      // If the heading itself has an ID, we don't want to overwrite it
      anchorLink = wrapperID;
    } // If there is no existing ID, use the heading text to create a semantic ID


    if (headingID || wrapperID) {
      jQuery(this.elements.$headings[index]).data('hasOwnID', true);
    } else {
      anchorLink = "".concat(classes.headingAnchor, "-").concat(index);
    }

    return anchorLink;
  },
    setHeadingsData: function setHeadingsData() {
    var _this6 = this;

    this.headingsData = [];
    var classes = this.getSettings('classes'); // Create an array for simplifying TOC list creation

    this.elements.$headings.each(function (index, element) {
      var anchorLink = _this6.getHeadingAnchorLink(index, classes);

      _this6.headingsData.push({
        tag: +element.nodeName.slice(1),
        text: element.textContent,
        anchorLink: anchorLink
      });
    });
  },
    run: function run() {
    this.elements.$headings = this.getHeadings();

    if (!this.elements.$headings.length) {
      return this.handleNoHeadingsFound();
    }

    this.setHeadingsData();

    if (!elementorFrontend.isEditMode()) {
      this.addAnchorsBeforeHeadings();
    }

    this.populateTOC();

    if (this.getElementSettings('minimize_box')) {
      this.collapseOnInit();
    }
  },
    expandBox: function expandBox() {
    var boxHeight = this.getCurrentDeviceSetting('min_height');
    this.$element.removeClass(this.getSettings('classes.collapsed'));
    this.elements.$tocBody.slideDown(); // return container to the full height in case a min-height is defined by the user

    this.elements.$widgetContainer.css('min-height', boxHeight.size + boxHeight.unit);
  },
    collapseBox: function collapseBox() {
    this.$element.addClass(this.getSettings('classes.collapsed'));
    this.elements.$tocBody.slideUp(); // close container in case a min-height is defined by the user

    this.elements.$widgetContainer.css('min-height', '0px');
  },
    onInit: function onInit() {
    var _get2,
        _this7 = this;

    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }

      _get2 = elementorModules.frontend.handlers.Base.prototype.onInit.apply(_this7, [this].concat(args));

    this.viewportItems = [];
      return _this7.run();
  },
    onListItemClick: function onListItemClick(event) {
    var _this8 = this;

    this.itemClicked = true;
    setTimeout(function () {
      return _this8.itemClicked = false;
    }, 2000);
    var $clickedItem = jQuery(event.target),
        $list = $clickedItem.parent().next(),
        collapseNestedList = this.getElementSettings('collapse_subitems');
    var listIsActive;

    if (collapseNestedList && $clickedItem.hasClass(this.getSettings('classes.firstLevelListItem'))) {
      if ($list.is(':visible')) {
        listIsActive = true;
      }
    }

    this.activateItem($clickedItem);

    if (collapseNestedList && listIsActive) {
      $list.slideUp();
    }
  },
  });

  elementorFrontend.hooks.addAction( 'frontend/element_ready/twbb_table-of-contents.default', function ( $scope ) {
    new TOCHandler({ $element: $scope });
  });

});
