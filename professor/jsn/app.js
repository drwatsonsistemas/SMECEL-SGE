/* ////////////////////////  Efnify script //////////////////////////////////// */

'use strict';

//prepage loader hide, after page load with delay 400ms
$(window).on('load', function() {
    $("#prepage-loader").delay(400).fadeOut('slow');
});

// write inside all jquery function
$(function() {

    /* Global variable 
    browserBodyWidth - calculate browser width 
    appLayoutControl - get app-layout-control id selector object, its use for manupulating layout view like unfixed sidebar, unfixed header, toggle sidebar
    */
    var browserBodyWidth = $(window).width(),
        appLayoutControl = $("#app-layout-control");


    // initialize user profile dropdown in navbar
    var notificationOptions = {
        alignment: 'left',
        constrainWidth: false
    };
    $('.ui-app__wrapper__navbar__rightside--users').dropdown(notificationOptions);

    // initialize notifications dropdown in navbar

    $('.ui-app__wrapper__navbar__rightside--notifications').dropdown({ alignment: 'left', constrainWidth: false, closeOnClick: false });


    // left sidenav initialize

    var leftSidenavOptions = {
        onCloseEnd: function() {
            appLayoutControl.addClass('left-sidenav-close');
        },
        onOpenStart: function() {
            appLayoutControl.removeClass('left-sidenav-close');
        }

    };
    $('.ui-app__left-sidenav').sidenav(leftSidenavOptions);

    // default left sidenav closed on medium device
    if (browserBodyWidth < 990)
        appLayoutControl.addClass('left-sidenav-close');

    // initialize menu in left sidenav
    $('.ui-app__left-sidenav__menu').collapsible();

    // left sidenav toggle(show and hide) on click button, button placed in navbar  
    $(document).on('click', '#left-sidenav-toggle', function() {
        var el = $("#ui-app__left-sidenav-slide-out"),
            instance = M.Sidenav.getInstance(el);
        if (appLayoutControl.hasClass('left-sidenav-close')) {
            instance.open();
            appLayoutControl.removeClass('left-sidenav-close');
        } else {
            instance.close();
            appLayoutControl.addClass('left-sidenav-close');
        }
    });

    // left sidenav full on mouse hover, when left sidenav collapsed/small
    $(document).on('mouseenter', '.ui-app__left-sidenav', function(e) {
        if (appLayoutControl.hasClass('left-sidenav-small')) {
            // only for sidenav full layout
            if ($(e.target).hasClass('ui-app__left-sidenav__navbar-menu'))
                return;
            appLayoutControl.removeClass('left-sidenav-small').addClass('left-sidenav-small-toggle');

        }
    });

    // left sidenav collapsed on mouse leave, when left sidenav size trigger by event mouseenter
    $(document).on('mouseleave', '.ui-app__left-sidenav', function() {
        if (appLayoutControl.hasClass('left-sidenav-small-toggle')) {
            appLayoutControl.removeClass('left-sidenav-small-toggle').addClass('left-sidenav-small');
        }
    });

    // left sidenav toggle (small and deafult size) on click button, button placed in navbar  
    $(document).on('click', '#left-sidenav-small-toggle', function() {
        var el = $('#left-sidenav-small-toggle i');
        if (el.text() == "radio_button_checked") {
            el.text('radio_button_unchecked');
            appLayoutControl.addClass('left-sidenav-small');
        } else {
            el.text('radio_button_checked');
            appLayoutControl.removeClass('left-sidenav-small');
        }

    });


    // right sidenav initialize
    var rightSidenavOptions = {
        edge: 'right',
        onOpenStart: function() {
            $('.right-aligned').prev('.sidenav-overlay').css('z-index', '998');
        }
    };
    $('.ui-app__right-sidenav').sidenav(rightSidenavOptions);

    // change app layout, like fixed header sidenav,fixed header unfixed sidenav,unfixed header sidenav
    $(document).on('change', 'input[name="layout"]', function() {
        switch ($(this).val()) {
            case "fixed-header-sidenav":
                appLayoutControl.removeClass('left-sidenav-unfixed navbar-unfixed');
                break;
            case "fixed-header-unfixed-sidenav":
                appLayoutControl.removeClass('navbar-unfixed').addClass('left-sidenav-unfixed');
                break;
            case "unfixed-header-sidenav":
                appLayoutControl.addClass('left-sidenav-unfixed navbar-unfixed');
                break;
        }
    });

    // change color for app
    $(document).on('click', '.ui-app__right-sidenav__layout__color-body', function() {
        $('.ui-app__right-sidenav__layout__color-body').removeClass('active');
        $(this).addClass('active');
        var color = $(this).data('color'),
            src = '../assets/css/theme/' + color + '.css',
            link = document.createElement('link');
        link.rel = 'stylesheet';
        link.type = 'text/css';
        link.href = src;
        link.media = 'screen';
        document.getElementsByTagName('head')[0].appendChild(link);
    });


    // manupulate class based on resize windows
    $(window).resize(function() {
        if ($(window).width() > 990) {
            // hide overlay when change width medium device to small
            var dragTarget = $('.sidenav-overlay');
            dragTarget.each(function(index, entry) {
                if (!$(entry).next().hasClass('right-aligned') && $(entry).css('display') == 'block') {
                    $('.sidenav-overlay').css({ display: 'none', opacity: 0 });
                    $('body').removeAttr("style");
                }
            });
        }
    });

    // reload page on refresh button
    $('#app-page-refresh').on('click', function() {
        location.reload();
        return false;
    });

    /************************************ Floating Action Button ******************************/
    // basic 
    $('.fixed-action-btn').floatingActionButton();
    // horizontal FAB
    $('.fixed-action-btn.horizontal-fab').floatingActionButton({ direction: 'left' });
    // fab-click-to-toggle
    $('.fixed-action-btn.fab-click-to-toggle').floatingActionButton({ hoverEnabled: false });
    $('.fixed-action-btn.horizontal-fab.fab-click-to-toggle').floatingActionButton({ direction: 'left', hoverEnabled: false });


    /******************************** Dropdown ********************************/

    // Basic
    $('.dropdown-trigger').dropdown();
    // below origin
    $('.dropdown-trigger.dropdown-below-origin').dropdown({ coverTrigger: false });

    // fixed width basic
    $('.dropdown-trigger.dropdown-fixed-width').dropdown({ constrainWidth: false });
    // fixed width below origin
    $('.dropdown-trigger.dropdown-fixed-width.dropdown-below-origin').dropdown({ constrainWidth: false, coverTrigger: false });
    // fixed width right alignment
    $('.dropdown-trigger.dropdown-fixed-width.dropdown-right-alignment').dropdown({ constrainWidth: false, alignment: 'right' });
    // fixed width below origin right alignment
    $('.dropdown-trigger.dropdown-fixed-width.dropdown-below-origin.dropdown-right-alignment').dropdown({ constrainWidth: false, coverTrigger: false, alignment: 'right' });

    // Basic active on hover
    $('.dropdown-trigger.dropdown-hover').dropdown({ hover: true });
    // below origin active on hover
    $('.dropdown-trigger.dropdown-hover.dropdown-below-origin').dropdown({ hover: true, coverTrigger: false });

    // fixed width basic active on hover
    $('.dropdown-trigger.dropdown-hover.dropdown-fixed-width').dropdown({ hover: true, constrainWidth: false });
    // fixed width below origin active on hover
    $('.dropdown-trigger.dropdown-hover.dropdown-fixed-width.dropdown-below-origin').dropdown({ hover: true, constrainWidth: false, coverTrigger: false });
    // fixed width right alignment active on hover
    $('.dropdown-trigger.dropdown-hover.dropdown-fixed-width.dropdown-right-alignment').dropdown({ hover: true, constrainWidth: false, alignment: 'right' });
    // fixed width below origin right alignment active on hover
    $('.dropdown-trigger.dropdown-hover.dropdown-fixed-width.dropdown-below-origin.dropdown-right-alignment').dropdown({ hover: true, constrainWidth: false, coverTrigger: false, alignment: 'right' });


    /**************************************** Tabs **************************************/
    // basic
    $('.tabs').tabs();
    // Swipeable Tabs
    $('.tabs.tabs-swipeable').tabs({ swipeable: true });

    /**************************************** Collapsible **************************************/

    // Basic
    $('.collapsible').collapsible();
    // Collapsible Expandable
    $('.collapsible.expandable').collapsible({ accordion: false });


    /**************************************** Tooltips **************************************/
    $('.tooltipped').tooltip();

    /**************************************** Carousel
     **************************************/
    // basic
    $('.carousel').carousel();

    // Full Width Slider
    $('.carousel.carousel-slider').carousel({
        fullWidth: true,
        indicators: true
    });

    /**************************************** Modal
     **************************************/
    $('.modal').modal();

    /**************************************** FeatureDiscovery **************************************/
    $('.tap-target').tapTarget();

    /**************************************** Media with javascript **************************************/
    //Material Box
    $('.materialboxed').materialbox();


    /********************************** Form Element **************************************/

    // select
    $('select').formSelect();
    // date picker
    $('.datepicker').datepicker();
    // time picker
    $('.timepicker').timepicker();



}); // end of document ready