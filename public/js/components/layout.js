$(".layout-footer-mobile-icon.down").on("click", function () {
  $(".layout-footer").animate({ scrollTop: 45 }, 500);
});
$(".layout-footer-mobile-icon.up").on("click", function () {
  $(".layout-footer").animate({ scrollTop: 0 }, 500);
});
