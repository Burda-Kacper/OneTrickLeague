$(".profile-menu-section").on("click", function () {
    loadProfileSection($(this).data("section"));
});

function loadProfileSection(section) {
    $(".profile-menu-section").each(function () {
        $(this).removeClass("active");
        if ($(this).data("section") === section) {
            $(this).addClass("active");
        }
    });
    $.ajax({
        url: profileSectionPath,
        method: "POST",
        dataType: "JSON",
        data: {
            section: section,
            userUrl: $(".profile-container").data('user-url')
        },
    }).done(function (response) {
        if (response.success) {
            $(".profile-data-container").html(response.data);
        } else {
            popup.openPopup("error", "Wystąpił błąd", response.data, 8);
        }
    });
}

$(document).ready(function () {
    loadProfileSection("quiz");
});

$(".profile-data-container").on(
    "click",
    ".profile-image-select-entry",
    function () {
        const picture = $(this).data("picture");
        $.ajax({
            url: profilePicturePath,
            method: "POST",
            dataType: "JSON",
            data: {
                pictureId: picture,
            },
        }).done(function (response) {
            if (response.success) {
                $(".profile-menu-image, .layout-header-login-image").attr(
                    "src",
                    "/img/profilePicture/" + response.data
                );
            } else {
                popup.openPopup("error", "Wystąpił błąd", response.data, 8);
            }
        });
    }
);
// ETODO: Figure out a better way to code this...
$(".profile-data-container").on(
    "click",
    "#profile-quiz-add-submit",
    function () {
        const question = $("#profile-quiz-add-question").val();
        const answerCorrect = $("#profile-quiz-add-answer-1").val();
        const answerWrong1 = $("#profile-quiz-add-answer-2").val();
        const answerWrong2 = $("#profile-quiz-add-answer-3").val();
        const answerWrong3 = $("#profile-quiz-add-answer-4").val();

        if (
            !question ||
            !answerCorrect ||
            !answerWrong1 ||
            !answerWrong2 ||
            !answerWrong3
        ) {
            popup.openPopup(
                "error",
                "Wystąpił błąd",
                "Uzupełnij wszystkie pola przed wysłaniem pytania.",
                8
            );
            return;
        }

        $.ajax({
            url: quizAddQuestionPath,
            method: "POST",
            dataType: "JSON",
            data: {
                question: question,
                answers: {
                    correct: answerCorrect,
                    wrong1: answerWrong1,
                    wrong2: answerWrong2,
                    wrong3: answerWrong3,
                },
            },
        }).done(function (response) {
            if (response.success) {
                popup.openPopup("success", "Przesłano nowe pytanie", response.data, 16);
                $(".profile-quiz-add-input").each(function () {
                    $(this).val("");
                });
            } else {
                popup.openPopup("error", "Wystąpił błąd", response.data, 16);
            }
        });
    }
);

$(".profile-data-container").on("click", "#submit-password", function () {
    const passwords = {
        old: $("#old-password").val(),
        new: $("#new-password").val(),
        repeat: $("#repeat-password").val(),
    };

    $.ajax({
        url: profileChangePasswordPath,
        method: "POST",
        dataType: "JSON",
        data: {
            passwords: passwords,
        },
    }).done(function (response) {
        if (response.success) {
            popup.openPopup("success", "Sukces", response.data, 8);
        } else {
            popup.openPopup("error", "Wystąpił błąd", response.data, 8);
        }
    });
});
