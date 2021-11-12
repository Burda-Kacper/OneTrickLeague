$(".quiz-start-button").on("click", function () {
  const quizIntroContainer = $(".quiz-intro-container");
  quizIntroContainer.removeClass("anim-slide-in-up");
  quizIntroContainer.addClass("anim-slide-out-up");
  $.ajax({
    url: quizStartPath,
    method: "POST",
    dataType: "JSON",
  }).done(function (response) {
    $(".quiz-container").html(response.data);
  });
});
