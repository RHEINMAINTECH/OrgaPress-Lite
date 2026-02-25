jQuery(function ($) {
    if (!wp || !wp.media) {
        return;
    }

const frameProto = wp.media.view.MediaFrame.Post.prototype;

const originalBind = frameProto.bindHandlers;
frameProto.bindHandlers = function () {
    originalBind.apply(this, arguments);
    this.on('content:render', this.enableDragAndDrop, this);
};

frameProto.enableDragAndDrop = function () {
    const view = this.content.get();
    if (!view || !view.collection) {
        return;
    }

    view.$el.find('.attachments-browser .attachment').attr('draggable', true);

    view.$el.on('dragstart', '.attachment', function (e) {
        e.originalEvent.dataTransfer.setData('attachment_id', $(this).data('id'));
    });

    $('.orgapress-folder-tree li').on('dragover', function (e) {
        e.preventDefault();
        $(this).addClass('drag-over');
    });

    $('.orgapress-folder-tree li').on('dragleave', function () {
        $(this).removeClass('drag-over');
    });

    $('.orgapress-folder-tree li').on('drop', function (e) {
        e.preventDefault();
        $(this).removeClass('drag-over');

        const attachmentId = e.originalEvent.dataTransfer.getData('attachment_id');
        const termId = $(this).data('id');

        if (!attachmentId || !termId) {
            return;
        }

        wp.ajax.post('save-attachment', {
            id: attachmentId,
            orgapress_media_folder: termId
        });
    });
};

});
