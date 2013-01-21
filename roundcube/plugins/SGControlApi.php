<?php


/**
 * Class SGControlApi
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class SGControlApi
{

    /**
     * @param string $domainName The domain name of the email address
     * @param string $username The username of the email address
     * @param string $password The current password the email account
     *
     * @return mixed
     */
    public function getAccount($domainName, $username, $password)
    {
        return $this->call('email/getAccount', array(
            'domainName' => $domainName,
            'username'   => $username,
            'passwd'     => $password,
        ));
    }

    /**
     * @param string $domainName The domain name of the email address
     * @param string $username The username of the email address
     * @param string $password The current password the email account
     * @param string $newPassword The new password to be set
     *
     * @return mixed
     */
    public function setPassword($domainName, $username, $password, $newPassword)
    {
        return $this->call('email/putAccount', array(
            'domainName' => $domainName,
            'username'   => $username,
            'passwd'     => $password,
            'npassword'  => $newPassword,
            'cpassword'  => $newPassword
        ));
    }

    /**
     * @param string $domainName The domain name of the email address
     * @param string $username The username of the email address
     * @param string $password The current password the email account
     * @param bool   $enabled Whether or not enable the autoresponder
     * @param string $content The autoresponder content
     *
     * @return mixed
     */
    public function setAutoResponder($domainName, $username, $password, $enabled, $content)
    {
        return $this->call('email/setAutoresponder', array(
            'domainName'           => $domainName,
            'username'             => $username,
            'passwd'               => $password,
            'autoresponderEnabled' => (int)$enabled,
            'autoresponder'        => $content
        ));
    }

    /**
     * @param string $callName
     * @param array  $params
     *
     * @return mixed
     * @throws Exception
     */
    private function call($callName, array $params = array())
    {
        //error_log(sprintf('API call "%s": Starting', $callName));
        $result = $this->getUrlContents(sprintf('https://control.servergrove.com/api/%s.json', $callName), $params);

        if (false === $result) {
            throw new Exception('Wrong type for result');
        }

        $result = json_decode($result);

        if (true !== $result->result) {
            throw new Exception($result->msg);
        }

        //error_log(sprintf('API call "%s": Completed', $callName));

        return $result;
    }

    /**
     * @param string $url
     * @param array  $params
     * @param string $method
     *
     * @return mixed
     * @throws Exception
     */
    private function getUrlContents($url, array $params = array(), $method = 'post')
    {
        if (!function_exists('curl_init')) {
            throw new Exception('Missing curl library');
        }

        $curl = curl_init();

        //error_log(sprintf('URL: %s?%s', $url, http_build_query($params)));
        if ('post' == strtolower($method)) {
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
        } else {
            curl_setopt($curl, CURLOPT_URL, sprintf('%s?%s', $url, http_build_query($params)));
        }
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        //error_log(sprintf('URL result: %s', var_export($response, true)));
        curl_close($curl);

        return $response;
    }
}
