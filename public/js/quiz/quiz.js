$(".quiz-start-button").on("click", function () {
  if ($(this).data("disabled") === "disabled") {
    return;
  }
  $(this).data("disabled", "disabled");
  const that = $(this);
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
    if (response.success) {
      loadQuestionInterface(response);
    } else {
      that.data("disabled", "");
      that.data("quiz-saved-token", "");
      quizIntroContainer.removeClass("anim-slide-out-up-absolute");
      quizIntroContainer.addClass("anim-slide-in-up");
      popup.openPopup("error", "Nie można zacząć Quizu", response.data);
    }
  });
});
$(".quiz-container").on("click", ".quiz-question-answer", function () {
  if ($(".quiz-container").data("disabled") === "disabled") {
    return;
  }
  $(".quiz-container").data("disabled", "disabled");
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
  if (response.success) {
    $(".quiz-container").html(response.data);
    if ($(".quiz-question-container").length > 0) {
      $(".quiz-container").data("disabled", "");
      autoResponseTimeout = setTimeout(function () {
        submitAnswer(null);
      }, 15000);
    }
  } else {
    popup.openPopup("error", "Wystąpił błąd", response.data);
  }
}
$(".quiz-container").on(
  "click",
  ".quiz-question-finish-link-copy",
  function () {
    navigator.clipboard.writeText($(".quiz-question-finish-link-link").text());
    popup.openPopup(
      "message",
      "Kopiowanie linku",
      "Zlecono skopiowanie linku do schowka. W przypadku, gdyby kopiowanie się nie powiodło możesz skopiować link ręcznie.",
      8
    );
  }
);
