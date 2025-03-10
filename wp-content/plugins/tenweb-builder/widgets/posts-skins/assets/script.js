jQuery( window ).on( 'elementor/frontend/init', function () {

    class LoadMore extends elementorModules.frontend.handlers.Base {
        getDefaultSettings() {
            return {
                selectors: {
                    postsContainer: '.elementor-posts-container',
                    postWrapperTag: 'article',
                    loadMoreButton: '.elementor-button',
                    loadMoreSpinnerWrapper: '.e-load-more-spinner',
                    loadMoreSpinner: '.e-load-more-spinner i, .e-load-more-spinner svg',
                    loadMoreAnchor: '.e-load-more-anchor'
                },
                classes: {
                    loadMoreSpin: 'eicon-animation-spin',
                    loadMoreIsLoading: 'e-load-more-pagination-loading',
                    loadMorePaginationEnd: 'e-load-more-pagination-end',
                    loadMoreNoSpinner: 'e-load-more-no-spinner'
                }
            };
        }
        getDefaultElements() {
            const selectors = this.getSettings('selectors');
            return {
                postsWidgetWrapper: this.$element[0],
                postsContainer: this.$element[0].querySelector(selectors.postsContainer),
                loadMoreButton: this.$element[0].querySelector(selectors.loadMoreButton),
                loadMoreSpinnerWrapper: this.$element[0].querySelector(selectors.loadMoreSpinnerWrapper),
                loadMoreSpinner: this.$element[0].querySelector(selectors.loadMoreSpinner),
                loadMoreAnchor: this.$element[0].querySelector(selectors.loadMoreAnchor)
            };
        }
        bindEvents() {
            super.bindEvents();

            // Handle load more functionality for on-click type.
            if (!this.elements.loadMoreButton) {
                return;
            }
            this.elements.loadMoreButton.addEventListener('click', event => {
                if (this.isLoading) {
                    return;
                }
                event.preventDefault();
                this.handlePostsQuery();
            });
        }
        onInit() {
            super.onInit();
            this.classes = this.getSettings('classes');
            this.isLoading = false;
            const paginationType = this.getElementSettings('pagination_type');
            if ('load_more_on_click' !== paginationType && 'load_more_infinite_scroll' !== paginationType) {
                return;
            }
            this.isInfinteScroll = 'load_more_infinite_scroll' === paginationType;

            // When spinner is not available, the button's text should not be hidden.
            this.isSpinnerAvailable = this.getElementSettings('load_more_spinner').value;
            if (!this.isSpinnerAvailable) {
                this.elements.postsWidgetWrapper.classList.add(this.classes.loadMoreNoSpinner);
            }
            if (this.isInfinteScroll) {
                this.handleInfiniteScroll();
            } else if (this.elements.loadMoreSpinnerWrapper && this.elements.loadMoreButton) {
                // Instead of creating 2 spinners for on-click and infinity-scroll, one spinner will be used so it should be appended to the button in on-click mode.
                this.elements.loadMoreButton.insertAdjacentElement('beforeEnd', this.elements.loadMoreSpinnerWrapper);
            }

            // Set the post id and element id for the ajax request.
            this.elementId = this.getID();
            this.postId = elementorFrontendConfig.post.id;

            // Set the current page and last page for handling the load more post and when no more posts to show.
            if (this.elements.loadMoreAnchor) {
                this.currentPage = parseInt(this.elements.loadMoreAnchor.getAttribute('data-page'));
                this.maxPage = parseInt(this.elements.loadMoreAnchor.getAttribute('data-max-page'));
                if (this.currentPage === this.maxPage || !this.currentPage) {
                    this.handleUiWhenNoPosts();
                }
            }
        }

        // Handle load more functionality for infinity-scroll type.
        handleInfiniteScroll() {
            if (this.isEdit) {
                return;
            }
            this.observer = elementorModules.utils.Scroll.scrollObserver({
                callback: event => {
                    if (!event.isInViewport || this.isLoading) {
                        return;
                    }

                    // When the observer is triggered it won't be triggered without scrolling, but sometimes there will be no scrollbar to trigger it again.
                    this.observer.unobserve(this.elements.loadMoreAnchor);
                    this.handlePostsQuery().then(() => {
                        if (this.currentPage !== this.maxPage) {
                            this.observer.observe(this.elements.loadMoreAnchor);
                        }
                    });
                }
            });
            this.observer.observe(this.elements.loadMoreAnchor);
        }
        handleUiBeforeLoading() {
            this.isLoading = true;
            if (this.elements.loadMoreSpinner) {
                this.elements.loadMoreSpinner.classList.add(this.classes.loadMoreSpin);
            }
            this.elements.postsWidgetWrapper.classList.add(this.classes.loadMoreIsLoading);
        }
        handleUiAfterLoading() {
            this.isLoading = false;
            if (this.elements.loadMoreSpinner) {
                this.elements.loadMoreSpinner.classList.remove(this.classes.loadMoreSpin);
            }
            if (this.isInfinteScroll && this.elements.loadMoreSpinnerWrapper && this.elements.loadMoreAnchor) {
                // Since the spinner has to be shown after the new content (posts), it should be appended after the anchor element.
                this.elements.loadMoreAnchor.insertAdjacentElement('afterend', this.elements.loadMoreSpinnerWrapper);
            }
            this.elements.postsWidgetWrapper.classList.remove(this.classes.loadMoreIsLoading);
        }
        handleUiWhenNoPosts() {
            this.elements.postsWidgetWrapper.classList.add(this.classes.loadMorePaginationEnd);
        }
        afterInsertPosts() {}
        handleSuccessFetch(result) {
            this.handleUiAfterLoading();
            const selectors = this.getSettings('selectors');

            // Grabbing only the new articles from the response without the existing ones (prevent posts duplication).
            const postsElements = result.querySelectorAll(`[data-id="${this.elementId}"] ${selectors.postsContainer} > ${selectors.postWrapperTag}`);
            const nextPageUrl = result.querySelector(`[data-id="${this.elementId}"] .e-load-more-anchor`).getAttribute('data-next-page');
            postsElements.forEach(element => this.elements.postsContainer.append(element));
            this.elements.loadMoreAnchor.setAttribute('data-page', this.currentPage);
            this.elements.loadMoreAnchor.setAttribute('data-next-page', nextPageUrl);
            if (this.currentPage === this.maxPage) {
                this.handleUiWhenNoPosts();
            }
            this.afterInsertPosts(postsElements, result);
        }
        handlePostsQuery() {
            this.handleUiBeforeLoading();
            this.currentPage++;
            const nextPageUrl = this.elements.loadMoreAnchor.getAttribute('data-next-page');
            return fetch(nextPageUrl).then(response => response.text()).then(html => {
                // Convert the HTML string into a document object
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                this.handleSuccessFetch(doc);
            });
        }
    }

    elementorFrontend.hooks.addAction('frontend/element_ready/tenweb-posts.cards', function ($scope) {
        let ob = new LoadMore({$element: $scope});
        ob.bindEvents();
    });

    elementorFrontend.hooks.addAction('frontend/element_ready/tenweb-posts.classic', function ($scope) {
        let ob = new LoadMore({$element: $scope});
        ob.bindEvents();
    });
    elementorFrontend.hooks.addAction('frontend/element_ready/tenweb-posts.full_content', function ($scope) {
        let ob = new LoadMore({$element: $scope});
        ob.bindEvents();
    });

    var TenwebPosts = elementorModules.frontend.handlers.Base.extend({
        getSkinPrefix() {

            let skinName = this.elements.$postsContainer.attr('skin');
            return skinName + '_';
        },
        bindEvents() {
            elementorFrontend.addListenerOnce(this.getModelCID(), 'resize', this.onWindowResize);
        },
        unbindEvents() {
            elementorFrontend.removeListeners(this.getModelCID(), 'resize', this.onWindowResize);
        },
        getClosureMethodsNames() {
            return elementorModules.frontend.handlers.Base.prototype.getClosureMethodsNames.apply(this, arguments).concat(['fitImages', 'onWindowResize']);
        },
        getDefaultSettings() {
            return {
                classes: {
                    fitHeight: 'elementor-fit-height',
                    hasItemRatio: 'elementor-has-item-ratio'
                },
                selectors: {
                    postsContainer: '.elementor-posts-container',
                    post: '.elementor-post',
                    postThumbnail: '.elementor-post__thumbnail',
                    postThumbnailImage: '.elementor-post__thumbnail img'
                }
            };
        },
        getDefaultElements() {
            var selectors = this.getSettings('selectors');
            return {
                $postsContainer: this.$element.find(selectors.postsContainer),
                $posts: this.$element.find(selectors.post)
            };
        },
        fitImage($post) {
            var settings = this.getSettings(),
                $imageParent = $post.find(settings.selectors.postThumbnail),
                $image = $imageParent.find('img'),
                image = $image[0];
            if (!image) {
                return;
            }
            var imageParentRatio = $imageParent.outerHeight() / $imageParent.outerWidth(),
                imageRatio = image.naturalHeight / image.naturalWidth;
            $imageParent.toggleClass(settings.classes.fitHeight, imageRatio < imageParentRatio);
        },
        fitImages() {
            var $ = jQuery,
                self = this,
                itemRatio = getComputedStyle(this.$element[0], ':after').content,
                settings = this.getSettings();
/*
            if (self.isMasonryEnabled()) {
                this.elements.$postsContainer.removeClass(settings.classes.hasItemRatio);
                return;
            }
*/
            this.elements.$postsContainer.toggleClass(settings.classes.hasItemRatio, !!itemRatio.match(/\d/));
            this.elements.$posts.each(function () {
                var $post = $(this),
                    $image = $post.find(settings.selectors.postThumbnailImage);
                self.fitImage($post);
                $image.on('load', function () {
                    self.fitImage($post);
                });
            });
        },
        setColsCountSettings() {
            const settings = this.getElementSettings(),
                skinPrefix = this.getSkinPrefix(),
                colsCount = elementorFrontend.utils.controls.getResponsiveControlValue(settings, `${skinPrefix}columns`);
            this.setSettings('colsCount', colsCount);
        },
        isMasonryEnabled() {
            return !!this.getElementSettings(this.getSkinPrefix() + 'masonry');
        },
        initMasonry() {
            imagesLoaded(this.elements.$posts, this.runMasonry);
        },
        getVerticalSpaceBetween() {
            /* The `verticalSpaceBetween` variable is set up in a way that supports older versions of the portfolio widget */
            let verticalSpaceBetween = elementorFrontend.utils.controls.getResponsiveControlValue(this.getElementSettings(), `${this.getSkinPrefix()}row_gap`, 'size');
            if ('' === this.getSkinPrefix() && '' === verticalSpaceBetween) {
                verticalSpaceBetween = this.getElementSettings('item_gap.size');
            }
            return verticalSpaceBetween;
        },
        runMasonry() {
            var elements = this.elements;
            elements.$posts.css({
                marginTop: '',
                transitionDuration: ''
            });
            this.setColsCountSettings();
            var colsCount = this.getSettings('colsCount'),
                hasMasonry = this.isMasonryEnabled() && colsCount >= 2;
            elements.$postsContainer.toggleClass('elementor-posts-masonry', hasMasonry);
            if (!hasMasonry) {
                elements.$postsContainer.height('');
                return;
            }
            const verticalSpaceBetween = this.getVerticalSpaceBetween();
            var masonry = new elementorModules.utils.Masonry({
                container: elements.$postsContainer,
                items: elements.$posts.filter(':visible'),
                columnsCount: this.getSettings('colsCount'),
                verticalSpaceBetween: verticalSpaceBetween || 0
            });
            masonry.run();
        },
        run() {
            // For slow browsers
            setTimeout(this.fitImages, 0);
            this.initMasonry();
        },
        onInit() {
            elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);
            this.bindEvents();
            this.run();
        },
        onWindowResize() {
            this.fitImages();
            this.runMasonry();
        },
        onElementChange() {
            this.fitImages();
            setTimeout(this.runMasonry);
        }
    });

    class PostsSlider {
        onInint() {
            var self = this;
            jQuery('.tenweb-posts-slider').each(async function(i,elem) {
                var id = jQuery(elem).parents('.elementor-widget-tenweb-posts').attr('data-id');
                jQuery(elem).attr('id', 'tenweb-posts-slider-swiper-' + id);
                var settings = jQuery(elem).data('settings');

                if ( ! jQuery.isEmptyObject(settings) ) {

                    settings.slidesPerView = {
                        desktop: 3,
                        tablet: 2,
                        mobile: 1
                    };
                    var swiperOptions = {
                        grabCursor: true,
                        effect: 'slide',
                        //initialSlide: self.getInitialSlide( settings ),
                        initialSlide: 0,
                        slidesPerView: self.getDeviceSlidesPerView( 'desktop', settings ),
                        loop: 'yes' === settings.loop,
                    }
                    var breakpointsSettings = {},
                        breakpoints = elementorFrontend.config.breakpoints;

                    breakpointsSettings[breakpoints.lg - 1] = {
                        slidesPerView: self.getDeviceSlidesPerView( 'desktop', settings ),
                        slidesPerGroup: self.getSlidesToScroll( settings ),
                        spaceBetween: self.getSpaceBetween( 'desktop', settings )
                    }

                    breakpointsSettings[breakpoints.md - 1] = {
                        slidesPerView: self.getDeviceSlidesPerView( 'tablet', settings ),
                        slidesPerGroup: self.getSlidesToScroll( settings ),
                        spaceBetween: self.getSpaceBetween( 'tablet', settings )
                    };

                    breakpointsSettings[breakpoints.xs] = {
                        slidesPerView: self.getDeviceSlidesPerView( 'mobile', settings ),
                        slidesPerGroup: self.getSlidesToScroll( settings ),
                        spaceBetween: self.getSpaceBetween( 'mobile', settings )
                    };

                    swiperOptions.breakpoints = breakpointsSettings;

                    var showArrows = 'arrows' === settings.slider_navigation || 'both' === settings.slider_navigation,
                        pagination = 'dots' === settings.slider_navigation || 'both' === settings.slider_navigation;

                    if (showArrows) {
                        swiperOptions.navigation = {
                            prevEl: '.swiper-button-prev',
                            nextEl: '.swiper-button-next'
                        };
                    }

                    if (pagination) {
                        swiperOptions.pagination = {
                            el: '.swiper-pagination',
                            type: 'bullets',
                            clickable: true
                        };
                    }

                    if (true === swiperOptions.loop) {
                        swiperOptions.loopedSlides = settings.slides_count;
                    }

                    if ( settings.autoplay === 'yes' ) {
                        swiperOptions.autoplay = {
                            delay: settings.autoplay_speed,
                            disableOnInteraction: settings.disable_on_interaction === 'yes',
                            pauseOnMouseEnter: settings.pause_on_mouseover === 'yes',
                        }
                    }

                    const Swiper = elementorFrontend.utils.swiper;
                    await new Swiper( jQuery('#tenweb-posts-slider-swiper-' + id), swiperOptions );
                }
            });
        }
        getInitialSlide ( settings ) {
            return Math.floor( ( settings.slides_count - 1 ) / 2 );
        }

        getSlidesToScroll ( settings ) {
            return Math.min( settings.slides_count, +settings.slides_to_scroll || 1 );
        }

        getDeviceSlidesPerView( view , settings ) {
            var str = "slides_per_view" + ("desktop" === view ? "" : "_" + view);
            var num =	Math.min( settings.slides_count, +settings[str] || settings['slidesPerView'][view] );
            return num;
        }

        getSpaceBetween( view, settings ) {
            var str = "space_between";
            return view && "desktop" !== view && (str += "_" + view), settings.breakpoints[str].size || 0;
        }
    }

    elementorFrontend.hooks.addAction('frontend/element_ready/tenweb-posts.classic', function ($scope) {
        new TenwebPosts({$element: $scope});
        let postsSlider = new PostsSlider();
        postsSlider.onInint();
    });
    elementorFrontend.hooks.addAction('frontend/element_ready/tenweb-posts.cards', function ($scope) {
        new TenwebPosts({$element: $scope});
        let postsSlider = new PostsSlider();
        postsSlider.onInint();
    });
    elementorFrontend.hooks.addAction('frontend/element_ready/tenweb-posts.image_left', function ($scope) {
        new TenwebPosts({$element: $scope});
        let postsSlider = new PostsSlider();
        postsSlider.onInint();
    });
    elementorFrontend.hooks.addAction('frontend/element_ready/tenweb-posts.on_image', function ($scope) {
        new TenwebPosts({$element: $scope});
        let postsSlider = new PostsSlider();
        postsSlider.onInint();
    });
    elementorFrontend.hooks.addAction('frontend/element_ready/tenweb-posts.full_content', function ($scope) {
        new TenwebPosts({$element: $scope});
        let postsSlider = new PostsSlider();
        postsSlider.onInint();
    });
})
// jQuery( window ).on( 'elementor:init', function() {
//     elementor.hooks.addAction('panel/open_editor/widget/tenweb-posts', function (panel, model, view) {
//         /*
// * change is done in 1.27.X version
// * this is for insuring backward compatibility with the old version of the widget,
// * we remove old control and replace it's values to new one's
//   */
//         const controlIds = {
//             'tenweb-posts_include_term_ids': [
//                 'tenweb-posts_include_taxonomy_post_category',
//                 'tenweb-posts_include_taxonomy_post_post_tag'
//             ],
//             'tenweb-posts_exclude_term_ids': [
//                 'tenweb-posts_exclude_taxonomy_post_category',
//                 'tenweb-posts_exclude_taxonomy_post_post_tag'
//             ],
//         }
//
//         for (const oldControlId in controlIds) {
//             if (controlIds.hasOwnProperty(oldControlId)) {
//                 const newControlIds = controlIds[oldControlId];
//                 var termIdsControl = model.get('settings').get(oldControlId);
//                 if (termIdsControl.length > 0) {
//                     var termIds = termIdsControl;
//
//                     termIds.forEach(function (termId) {
//                         newControlIds.forEach(function (newControlId) {
//                             replaceOldControlWithNew({
//                                 'model': model,
//                                 'container': view.container,
//                                 'oldControlValue': termId,
//                                 'newControlId': newControlId
//                             });
//                         });
//                     });
//
//                     window.parent.$e.commands.run('document/elements/settings', {
//                         "container": view.container,
//                         settings: {
//                             [oldControlId]: ""
//                         }
//                     });
//                     window.parent.$e.run('document/save/default');
//                 }
//
//             }
//         }
//     });
// });
function replaceOldControlWithNew(args) {
    const newControlOptions = Object.keys(args['model'].attributes.settings.options.controls[args['newControlId']].options);
    if ( jQuery.inArray(args['oldControlValue'], newControlOptions) !== -1 ) {
        let appliedValues = args['model'].getSetting(args['newControlId']);
        if( appliedValues.length === 0 ) {
            appliedValues = [];
        }
        appliedValues.push(args['oldControlValue']);
        let settings = {
            [args['newControlId']]: appliedValues,
        }
        window.parent.$e.commands.run('document/elements/settings', {
            "container": args['container'],
            settings: settings
        });
    }
}
