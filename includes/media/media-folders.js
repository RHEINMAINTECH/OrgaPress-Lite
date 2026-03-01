jQuery(function ($) {
if (!wp || !wp.media) {
return;
}

const TaxonomyFilter = wp.media.View.extend({
    tagName: 'select',
    className: 'attachment-filters',
    initialize: function () {
        this.listenTo(this.collection, 'reset', this.render);
    },
    render: function () {
        const self = this;
        this.$el.empty();
        this.$el.append('<option value="">' + OrgaPressMedia.labels.all_folders + '</option>');

        wp.ajax.post('get_terms', {
            taxonomy: OrgaPressMedia.taxonomy,
            hide_empty: false
        }).done(function (terms) {
            terms.forEach(function (term) {
                self.$el.append('<option value="' + term.term_id + '">' + term.name + '</option>');
            });
        });

        return this;
    },
    events: {
        change: 'change'
    },
    change: function () {
        this.model.set(OrgaPressMedia.taxonomy, this.$el.val());
    }
});

const AttachmentsBrowser = wp.media.view.AttachmentsBrowser;
wp.media.view.AttachmentsBrowser = AttachmentsBrowser.extend({
    createToolbar: function () {
        AttachmentsBrowser.prototype.createToolbar.apply(this, arguments);
        this.toolbar.set(
            'OrgaPressMediaFolders',
            new TaxonomyFilter({
                controller: this.controller,
                collection: this.collection,
                model: this.collection.props
            }).render()
        );
    }
});

});
