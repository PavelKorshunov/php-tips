<?php

class Stream
{
    /**
     * @var array
     */
    private array $threads;

    /**
     * @var int
     */
    private int $timeout = 10;

    public function __construct(array $stream)
    {
        $this->threads = $stream;
    }

    /**
     * @param Closure $callback
     */
    public function handle(\Closure $callback): void
    {
        while (count($this->threads)) {
            $w = null;
            $e = null;
            $streams = $this->threads;

            // stream_select изменяет исходный массив $streams и возвращает первый готовый к чтению ресурс
            if(stream_select($streams, $w, $e, $this->timeout) !== false) {
                $this->process($streams, $callback);
            } else {
                throw new RuntimeException('stream_select failed');
            }
        }
    }

    /**
     * @param array $streams
     * @param Closure $callback
     */
    private function process(array $streams, \Closure $callback): void
    {
        foreach ($streams as $key => $stream) {
            $text = '';
            while(!feof($stream)) {
                $text .= fread($stream, 100);
            }

            $callback($text);
            fclose($stream);
            unset($this->threads[$key]);
        }
    }
}