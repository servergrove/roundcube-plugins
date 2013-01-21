if (window.rcmail) {
    rcmail.addEventListener('init', function(evt) {
        var tab = $(document.createElement('span'))
            .attr({id: 'settingstabpluginautoresponder'})
            .addClass('tablink')
            .append(
                $(document.createElement('a'))
                    .attr('href', rcmail.env.comm_path + '&_action=plugin.autoresponder')
                    .html("Autoresponder")
            );

        rcmail.add_element(tab, 'tabs');

        rcmail.register_command('plugin.autoresponder-save', function() {

            rcmail.gui_objects.autoresponder_form.submit();
        },true);
    });
}