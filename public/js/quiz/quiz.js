$(".quiz-start-button").on("click", function () {
  if ($(this).data("disabled") === "disabled") {
    return;
  }
  $(this).data("disabled", "disabled");
  const quizIntroContainer = $(".quiz-intro-container");
  quizIntroContainer.removeClass("anim-slide-in-up");
  quizIntroContainer.addClass("anim-slide-out-up-absolute");
  $.ajax({
    url: quizStartPath,
    method: "POST",
    dataType: "JSON",
    data: {
      quizSavedToken: $(".quiz-start-button").data("quiz-saved-token"),
    },
  }).done(function (response) {
    loadQuestionInterface(response);
  });
});
$(".quiz-container").on("click", ".quiz-question-answer", function () {
  clearTimeout(autoResponseTimeout);
  submitAnswer($(this).data("answer-token"));
});
function submitAnswer(answerToken) {
  $(".quiz-question-container").removeClass("anim-slide-in-up");
  $(".quiz-question-container").addClass("anim-slide-out-up-absolute");
  $.ajax({
    url: quizAnswerPath,
    method: "POST",
    dataType: "JSON",
    data: {
      answerToken: answerToken,
      quaId: $(".quiz-question-meta").data("quaid"),
      token: $(".quiz-question-meta").data("token"),
    },
  }).done(function (response) {
    loadQuestionInterface(response);
  });
}
var autoResponseTimeout = null;
function loadQuestionInterface(response) {
  //ETODO: Handle errors
  if (response.success) {
    $(".quiz-container").html(response.data);
    if ($(".quiz-question-container").length > 0) {
      autoResponseTimeout = setTimeout(function () {
        submitAnswer(null);
      }, 15000);
    }
  }
}
// ETODO: Add copying successful popup
$(".quiz-container").on(
  "click",
  ".quiz-question-finish-link-copy",
  function () {
    navigator.clipboard.writeText($(".quiz-question-finish-link-link").text());
  }
);
