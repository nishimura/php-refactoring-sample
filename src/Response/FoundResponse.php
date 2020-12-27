<?php

namespace Bbs\Response;

use Bbs\Session\Session;

class FoundResponse implements Response
{
    /** @var string */
    private $url;
    /** @var ?string */
    private $message;

    public function __construct(string $url, ?string $message = null)
    {
        $this->url = $url;
        $this->message = $message;
    }

    public function respond(): void
    {
        if ($this->message !== null){
            Session::add('message', $this->message);
        }

        http_response_code(302);
        header('Location: ' . $this->url);
        echo 'Redirect... ' . $this->url;
    }
}
