class Popup {
    constructor() {
        this.container = $(".popup-container");
        this.wrapper = $(".popup-wrapper");
        this.container.on("click", ".popup-entry-close", function () {
            popup.closePopup($(this));
        });
    }

    closePopup(closeButton) {
        closeButton
            .closest(".popup-entry")
            .removeClass("anim-slide-in-up")
            .addClass("anim-slide-out-up");
        setTimeout(
            function (that) {
                that.closest(".popup-entry").remove();
            },
            1000,
            closeButton
        );
    }

    openPopup(type, title, message, duration = null) {
        let iconClass = "fas fa-info-circle";
        switch (type) {
            case "success":
                iconClass = "fas fa-check-circle";
                break;
            case "message":
                iconClass = "fas fa-info-circle";
                break;
            case "error":
                iconClass = "fas fa-exclamation-circle";
                break;
        }
        const template = $(`
      <div class="popup-entry anim-slide-in-up otl-box-shadow ${type}">
    <div class="popup-entry-bar"></div>
    <div class="popup-entry-content">
      <p class="popup-entry-title">
        <i class="${iconClass}"></i>
        ${title}
      </p>
      <p class="popup-entry-text">${message}</p>
    </div>
    <div class="popup-entry-close-container">
      <button class="popup-entry-close">
        <i class="fas fa-times"></i>
      </button>
    </div>
  </div>
  `);
        this.container.append(template);

        if (duration) {
            const popupEntryBar = template.find(".popup-entry-bar");
            popupEntryBar.addClass("popup-entry-bar-shrink");
            popupEntryBar.css("animation-duration", duration + "s");
            setTimeout(
                function (closeButton) {
                    popup.closePopup(closeButton);
                },
                duration * 1000,
                template.find(".popup-entry-close")
            );
        }
    }
}

const popup = new Popup();
