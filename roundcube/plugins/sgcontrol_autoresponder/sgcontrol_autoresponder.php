<?php

require_once(__DIR__.str_replace('/', DIRECTORY_SEPARATOR, '/../SGControlApi.php'));

/**
 * Class sgcontrol_autoresponder
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class sgcontrol_autoresponder extends rcube_plugin
{

    /** @var SGControlApi */
    private $sgcontrolApi;

    public function init()
    {
        $rcmail = rcmail::get_instance();
        // add Tab label
        $rcmail->output->add_label('autoresponder');

        $this->register_action('plugin.autoresponder', array($this, 'autoresponder_init'));
        $this->register_action('plugin.autoresponder-save', array($this, 'autoresponder_save'));
        $this->include_script('sgcontrol_autoresponder.js');

        $this->sgcontrolApi = new SGControlApi();
    }

    public function autoresponder_init()
    {
        $this->register_handler('plugin.body', array($this, 'autoresponder_form'));

        $rcmail = rcmail::get_instance();
        $rcmail->output->set_pagetitle('Configure Autoresponder');
        # Displays the plugin
        $rcmail->output->send('plugin');
    }

    public function autoresponder_save()
    {
        $this->register_handler('plugin.body', array($this, 'autoresponder_form'));

        $rcmail = rcmail::get_instance();
        $rcmail->output->set_pagetitle('Configure Autoresponder');

        if (isset($_POST['autoresponder_response'])) {
            $enabled = isset($_POST['autoresponder_enabler']);
            $content = $_POST['autoresponder_response'];

            try {
                $out = $this->sgcontrolApi->setAutoResponder(
                    $rcmail->user->get_username('domain'),
                    $rcmail->user->get_username('local'),
                    $rcmail->decrypt($_SESSION['password']),
                    $enabled,
                    $content
                );

                $rcmail->output->command('display_message', $out->msg, 'confirmation');
            } catch (Exception $e) {
                $rcmail->output->command('display_message', $e->getMessage(), 'error');
            }
        }

        rcmail_overwrite_action('plugin.autoresponder');
        # Displays the plugin
        $rcmail->output->send('plugin');
    }

    public function autoresponder_form()
    {
        $rcmail = rcmail::get_instance();
        try {
            $callResponse = $this->sgcontrolApi->getAccount($rcmail->user->get_username('domain'), $rcmail->user->get_username('local'), $rcmail->decrypt($_SESSION['password']));

            $autoresponderContent = $callResponse->rsp->autoresponder;
            $autoresponderEnabled = $callResponse->rsp->autoresponderEnabled;
        } catch (Exception $e) {
            $autoresponderContent = '';
            $autoresponderEnabled = false;
            $rcmail->output->command('display_message', $e->getMessage(), 'error');
        }

        $enabler = new html_checkbox(array(
            'name'  => 'autoresponder_enabler',
            'id'    => 'autoresponder_enabler',
            'value' => 1,
        ));

        $response = new html_textarea(array(
            'name'  => 'autoresponder_response',
            'id'    => 'autoresponder_response',
            'style' => 'width: 360px; height: 100px;'
        ));

        $content = html::div(array(),
            html::p(array(), $enabler->show($autoresponderEnabled == 'true' ? 1 : 0).' Enable autoresponder').
                html::p(array(), html::div(array(), 'Auto-responder content:').
                    html::div(array(), $response->show($autoresponderContent)))
        );

        $boxTitle = html::div(array(
            'id'    => 'prefs-title',
            'class' => 'boxtitle'
        ), 'Configure Autoresponder');

        $button = $rcmail->output->button(array(
            'command' => 'plugin.autoresponder-save',
            'type'    => 'input',
            'class'   => 'button mainaction',
            'label'   => 'save'
        ));
        $boxContent = html::div(array('class' => 'boxcontent'), $content.html::p(null, $button));

        $out = html::div(array('class' => 'box'), $boxTitle.$boxContent);

        $rcmail->output->add_gui_object('autoresponder_form', 'autoresponder-form');

        return $rcmail->output->form_tag(array(
            'id'     => 'autoresponder-form',
            'name'   => 'autoresponder-form',
            'method' => 'post',
            'action' => './?_task=settings&_action=plugin.autoresponder-save',
        ), $out);
    }
}