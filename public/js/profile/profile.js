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
    },
  }).done(function (response) {
    $(".profile-data-container").html(response.data);
  });
}

$(document).ready(function () {
  loadProfileSection("profile");
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
      }
    });
  }
);
