<?php

require_once(__DIR__.str_replace('/', DIRECTORY_SEPARATOR, '/../../SGControlApi.php'));


class rcube_sgcontrol_password
{
    function save($curpass, $passwd)
    {
        try {
            $api = new SGControlApi();
            $rcmail = rcmail::get_instance();
            $api->setPassword(
                $rcmail->user->get_username('domain'),
                $rcmail->user->get_username('local'),
                $curpass,
                $passwd
            );

            return PASSWORD_SUCCESS;
        } catch (Exception $e) {
            return array(
                'message' => $e->getMessage(),
                'code'    => PASSWORD_ERROR
            );
        }
    }
}


/**
 * @param string $curpass
 * @param string $passwd
 *
 * @return int
 */
function password_save($curpass, $passwd)
{
    try {
        $api = new SGControlApi();
        $rcmail = rcmail::get_instance();
        $api->setPassword(
            $rcmail->user->get_username('domain'),
            $rcmail->user->get_username('local'),
            $curpass,
            $passwd
        );

        return PASSWORD_SUCCESS;
    } catch (Exception $e) {
        return array(
            'message' => $e->getMessage(),
            'code'    => PASSWORD_ERROR
        );
    }
}