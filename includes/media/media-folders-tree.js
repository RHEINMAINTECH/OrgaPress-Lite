jQuery(function ($) {
    if (!wp || !wp.media || !wp.media.view) {
        return;
    }

const FolderTree = wp.media.View.extend({
    className: 'orgapress-media-folder-tree',
    initialize: function () {
        this.terms = [];
        this.fetchTerms();
    },
    fetchTerms: function () {
        const self = this;
        wp.ajax.post('get_terms', {
            taxonomy: OrgaPressMedia.taxonomy,
            hide_empty: false
        }).done(function (terms) {
            self.terms = terms;
            self.render();
        });
    },
    render: function () {
        const self = this;
        this.$el.empty();
        this.$el.append('<h3>' + OrgaPressMedia.labels.media_folders + '</h3>');
        const ul = $('<ul class="orgapress-folder-tree"></ul>');

        this.terms.forEach(function (term) {
            const li = $('<li data-id="' + term.term_id + '">' + term.name + '</li>');
            li.on('click', function () {
                self.controller.content.get().collection.props.set(
                    OrgaPressMedia.taxonomy,
                    term.term_id
                );
            });
            ul.append(li);
        });

        this.$el.append(ul);
        return this;
    }
});

const Library = wp.media.view.MediaFrame.Post;
wp.media.view.MediaFrame.Post = Library.extend({
    bindHandlers: function () {
        Library.prototype.bindHandlers.apply(this, arguments);
        this.on('content:render', this.renderFolderTree, this);
    },
    renderFolderTree: function () {
        if (this.folderTree) {
            return;
        }
        this.folderTree = new FolderTree({
            controller: this
        });
        this.$el.find('.media-frame-menu').append(this.folderTree.el);
    }
});

});
