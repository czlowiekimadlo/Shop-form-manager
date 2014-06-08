var tabs = {

    init : function () {
        this.bindTabs();
        var currentTab = this.getCurrentTab();
        if (currentTab) {
            this.switchToTab(currentTab);
        } else {
            this.switchToTab('#shops');
        }
    },

    hideAllTabs : function () {
        $('.tabcontent').hide();
    },

    showTab : function (tabName) {
        $(tabName + '.tabcontent').show();
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