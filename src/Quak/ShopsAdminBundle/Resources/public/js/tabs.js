var tabs = {

    init : function () {
        this.bindTabs();
        var currentTab = this.getCurrentTab();
        if (currentTab) {
            this.switchToTab(currentTab);
        } else {
            this.switchToTab('#reporting');
        }
    },

    hideAllTabs : function () {
        $('.tabcontent').hide();
        $('#tabs li a').removeClass('selectedTab');
    },

    showTab : function (tabName) {
        $(tabName + '.tabcontent').show();
        $("#tabs li a").each(function(index, element) {
            var tab = $(element);
            var target = tab.attr('href');

            if (target === tabName) {
                tab.addClass('selectedTab');
            }
        });
    },

    switchToTab : function (tabName) {
        this.hideAllTabs();
        this.showTab(tabName);
    },

    bindTabs : function () {
        $('#tabs').on('click', 'li a', function () {
            var header = $(this);
            var target = header.attr('href');

            tabs.switchToTab(target);
        });
    },

    getCurrentTab : function () {
        return window.location.hash;
    }
}