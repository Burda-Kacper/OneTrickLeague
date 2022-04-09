class HomepageSlider {
    currentSlide = null;
    maxSlides = $(".homepage-scoreboard").length;
    enabledAutomaticSlide = null;
    automaticSlideChangeDelay = 5000;
    automaticSlideChangePause = 12000;

    activateSlide = (slide) => {
        this.currentSlide = slide;
        $(".homepage-scoreboard").each(function () {
            $(this).addClass("hidden");
        });
        $(".homepage-scoreboard-navigation-dot").each(function () {
            $(this).removeClass("active");
        });
        $(".homepage-scoreboard-" + slide).removeClass("hidden");
        $(".homepage-scoreboard-navigation-dot-" + slide).addClass("active");
    };

    automaticSlide = () => {
        this.enabledAutomaticSlide = setTimeout(
            function (that) {
                that.currentSlide++;
                if (that.currentSlide > that.maxSlides) {
                    that.currentSlide = 1;
                }
                that.activateSlide(that.currentSlide);
                that.automaticSlide();
            },
            this.automaticSlideChangeDelay,
            this
        );
    };
}

const homepageSlider = new HomepageSlider();

$(".homepage-scoreboard-navigation-dot").on("click", function () {
    homepageSlider.activateSlide($(this).data("scoreboard"));
    clearTimeout(homepageSlider.enabledAutomaticSlide);
    setTimeout(function () {
        homepageSlider.automaticSlide();
    }, this.automaticSlideChangePause);
});

$(window).on("load", function () {
    homepageSlider.activateSlide(1);
    homepageSlider.automaticSlide();
    $(".homepage-scoreboard-navigation").removeClass("hidden");
});
