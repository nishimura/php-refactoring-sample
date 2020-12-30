<?php

namespace Bbs\Io\Infrastructure\Response;

abstract class HtmlResponse implements Response
{
    public function respond(): void
    {
        ob_start();
        try {
            $this->showHtml();
        }catch (\Exception $e){
            ob_end_clean();
            throw $e;
        }
        ob_end_flush();
    }

    abstract function showHtml(): void;
}
