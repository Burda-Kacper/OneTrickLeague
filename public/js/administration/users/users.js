$(".a-users-filters-confirm").on("click", function () {
  loadUsers(0);
});

function loadUsers(page) {
  $.ajax({
    url: getUsersPath,
    method: "POST",
    dataType: "JSON",
    data: {
      username: $(".a-users-filters-username").val(),
      amount: $(".a-users-filters-amount").find(".active").data("amount"),
      sort: {
        field: $(".a-users-filters-sort-field")
          .find("option:selected")
          .data("value"),
        order: $(".a-users-filters-sort-order")
          .find("option:selected")
          .data("value"),
      },
      page: page,
    },
  }).done(function (response) {
    if (response.success) {
      $(".a-users-table-content").html(response.template);
    } else {
      popup.openPopup("error", "Wystąpił błąd", response.data, 8);
    }
  });
}

$(window).on("load", function () {
  loadUsers(0);
});
