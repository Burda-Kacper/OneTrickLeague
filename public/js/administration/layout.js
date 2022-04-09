$(".a-action-button").on("click", function () {
    $(this)
        .parent()
        .find(".a-action-button")
        .each(function () {
            $(this).removeClass("active");
        });
    $(this).addClass("active");
});
