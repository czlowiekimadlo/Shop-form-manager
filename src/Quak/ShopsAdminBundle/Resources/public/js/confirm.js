var popup = {

    init : function () {
        this.bindRemovalPopups();
    },

    bindRemovalPopups : function () {
        $('.confirmRemoval').on('click', function () {
            return confirm("Are you sure you want to remove this element?");
        });
    }

}