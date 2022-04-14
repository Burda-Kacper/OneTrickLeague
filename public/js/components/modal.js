class Modal {
    constructor() {
        this.wrapper = $(".modal-wrapper");
        this.container = $(".modal-container");
        this.modalTitle = $(".modal-title");
        this.modalContent = $(".modal-content");
        this.close = $(".modal-close");

        this.close.on('click', function () {
            modal.closeModal();
        });
    }

    openModal(title, content) {
        this.modalTitle.text(title);
        this.modalContent.html(content);
        this.wrapper.removeClass("hidden");
    }

    closeModal() {
        this.modalTitle.text("");
        this.modalContent.html("");
        this.wrapper.addClass("hidden");
    }
}

const modal = new Modal();