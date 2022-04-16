$(".profile-data-container").on('click', '.profile-quiz-history-points', function () {
    $.ajax({
        url: profileQuizDetailsPath,
        method: "POST",
        dataType: "JSON",
        data: {
            quizToken: $(this).data('quiz'),
        },
    }).done(function (response) {
        if (response.success) {
            modal.openModal("Szczegóły Quizu", response.data);
        } else {
            popup.openPopup("error", "Wystąpił błąd", response.data, 8);
        }
    });
});