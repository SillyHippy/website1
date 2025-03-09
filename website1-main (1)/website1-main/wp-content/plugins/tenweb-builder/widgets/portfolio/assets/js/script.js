var portfolio = posts_base.extend({
  isActive(settings) {
    return settings.$element.find('.elementor-portfolio').length;
  },

  getSkinPrefix() {
    return '';
  },

  getDefaultSettings() {
    var settings = posts_base.prototype.getDefaultSettings.apply(this, arguments);

    settings.transitionDuration = 450;
    jQuery.extend(settings.classes, {
      active: 'elementor-active',
      item: 'elementor-portfolio-item',
      ghostItem: 'elementor-portfolio-ghost-item'
    });
    return settings;
  },

  getDefaultElements() {
    var elements = posts_base.prototype.getDefaultElements.apply(this, arguments);

    elements.$filterButtons = this.$element.find('.elementor-portfolio__filter');
    return elements;
  },

  getOffset(itemIndex, itemWidth, itemHeight) {
    var settings = this.getSettings(),
      itemGap = this.elements.$postsContainer.width() / settings.colsCount - itemWidth;
    itemGap += itemGap / (settings.colsCount - 1);
    return {
      start: (itemWidth + itemGap) * (itemIndex % settings.colsCount),
      top: (itemHeight + itemGap) * Math.floor(itemIndex / settings.colsCount)
    };
  },

  getClosureMethodsNames() {
    var baseClosureMethods = posts_base.prototype.getClosureMethodsNames.apply(this, arguments);

    return baseClosureMethods.concat(['onFilterButtonClick']);
  },

  filterItems(term) {
    var $posts = this.elements.$posts,
      activeClass = this.getSettings('classes.active'),
      termSelector = '.elementor-filter-' + term;

    if ('__all' === term) {
      $posts.addClass(activeClass);
      return;
    }

    $posts.not(termSelector).removeClass(activeClass);
    $posts.filter(termSelector).addClass(activeClass);
  },

  removeExtraGhostItems() {
    var settings = this.getSettings(),
      $shownItems = this.elements.$posts.filter(':visible'),
      emptyColumns = (settings.colsCount - $shownItems.length % settings.colsCount) % settings.colsCount,
      $ghostItems = this.elements.$postsContainer.find('.' + settings.classes.ghostItem);
    $ghostItems.slice(emptyColumns).remove();
  },

  handleEmptyColumns() {
    this.removeExtraGhostItems();
    var settings = this.getSettings(),
      $shownItems = this.elements.$posts.filter(':visible'),
      $ghostItems = this.elements.$postsContainer.find('.' + settings.classes.ghostItem),
      emptyColumns = (settings.colsCount - ($shownItems.length + $ghostItems.length) % settings.colsCount) % settings.colsCount;

    for (var i = 0; i < emptyColumns; i++) {
      this.elements.$postsContainer.append(jQuery('<div>', {
        class: settings.classes.item + ' ' + settings.classes.ghostItem
      }));
    }
  },

  showItems($activeHiddenItems) {
    $activeHiddenItems.show();
    setTimeout(function () {
      $activeHiddenItems.css({
        opacity: 1
      });
    });
  },

  hideItems($inactiveShownItems) {
    $inactiveShownItems.hide();
  },

  arrangeGrid() {
    var $ = jQuery,
      self = this,
      settings = self.getSettings(),
      $activeItems = self.elements.$posts.filter('.' + settings.classes.active),
      $inactiveItems = self.elements.$posts.not('.' + settings.classes.active),
      $shownItems = self.elements.$posts.filter(':visible'),
      $activeOrShownItems = $activeItems.add($shownItems),
      $activeShownItems = $activeItems.filter(':visible'),
      $activeHiddenItems = $activeItems.filter(':hidden'),
      $inactiveShownItems = $inactiveItems.filter(':visible'),
      itemWidth = $shownItems.outerWidth(),
      itemHeight = $shownItems.outerHeight();
    self.elements.$posts.css('transition-duration', settings.transitionDuration + 'ms');
    self.showItems($activeHiddenItems);

    if (self.isEdit) {
      self.fitImages();
    }

    self.handleEmptyColumns();

    if (self.isMasonryEnabled()) {
      self.hideItems($inactiveShownItems);
      self.showItems($activeHiddenItems);
      self.handleEmptyColumns();
      self.runMasonry();
      return;
    }

    $inactiveShownItems.css({
      opacity: 0,
      transform: 'scale3d(0.2, 0.2, 1)'
    });
    $activeShownItems.each(function () {
      var $item = $(this),
        currentOffset = self.getOffset($activeOrShownItems.index($item), itemWidth, itemHeight),
        requiredOffset = self.getOffset($shownItems.index($item), itemWidth, itemHeight);

      if (currentOffset.start === requiredOffset.start && currentOffset.top === requiredOffset.top) {
        return;
      }

      requiredOffset.start -= currentOffset.start;
      requiredOffset.top -= currentOffset.top;

      if (elementorFrontend.config.is_rtl) {
        requiredOffset.start *= -1;
      }

      $item.css({
        transitionDuration: '',
        transform: 'translate3d(' + requiredOffset.start + 'px, ' + requiredOffset.top + 'px, 0)'
      });
    });
    setTimeout(function () {
      $activeItems.each(function () {
        var $item = $(this),
          currentOffset = self.getOffset($activeOrShownItems.index($item), itemWidth, itemHeight),
          requiredOffset = self.getOffset($activeItems.index($item), itemWidth, itemHeight);
        $item.css({
          transitionDuration: settings.transitionDuration + 'ms'
        });
        requiredOffset.start -= currentOffset.start;
        requiredOffset.top -= currentOffset.top;

        if (elementorFrontend.config.is_rtl) {
          requiredOffset.start *= -1;
        }

        setTimeout(function () {
          $item.css('transform', 'translate3d(' + requiredOffset.start + 'px, ' + requiredOffset.top + 'px, 0)');
        });
      });
    });
    setTimeout(function () {
      self.hideItems($inactiveShownItems);
      $activeItems.css({
        transitionDuration: '',
        transform: 'translate3d(0px, 0px, 0px)'
      });
      self.handleEmptyColumns();
    }, settings.transitionDuration);
  },

  activeFilterButton(filter) {
    var activeClass = this.getSettings('classes.active'),
      $filterButtons = this.elements.$filterButtons,
      $button = $filterButtons.filter('[data-filter="' + filter + '"]');
    $filterButtons.removeClass(activeClass);
    $button.addClass(activeClass);
  },

  setFilter(filter) {
    this.activeFilterButton(filter);
    this.filterItems(filter);
    this.arrangeGrid();
  },

  refreshGrid() {
    this.setColsCountSettings();
    this.arrangeGrid();
  },

  bindEvents() {
    posts_base.prototype.bindEvents.apply(this, arguments);

    this.elements.$filterButtons.on('click', this.onFilterButtonClick);
  },

  isMasonryEnabled() {
    return !!this.getElementSettings('masonry');
  },

  run() {
    posts_base.prototype.run.apply(this, arguments);

    this.setColsCountSettings();
    this.setFilter('__all');
    this.handleEmptyColumns();
  },

  onFilterButtonClick(event) {
    this.setFilter(jQuery(event.currentTarget).data('filter'));
  },

  onWindowResize() {
    posts_base.prototype.onWindowResize.apply(this, arguments);

    this.refreshGrid();
  },

  onElementChange(propertyName) {
    posts_base.prototype.onElementChange.apply(this, arguments);

    if ('classic_item_ratio' === propertyName) {
      this.refreshGrid();
    }
  }

});

jQuery( window ).on( 'elementor/frontend/init', function() {
  elementorFrontend.hooks.addAction( 'frontend/element_ready/twbb_portfolio.default', function ( $scope ) {
    new portfolio({ $element: $scope });
  });
});
