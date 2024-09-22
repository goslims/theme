<?php
namespace SLiMS\Theme;

class Loader
{
    private string $buffer = '';
    private string $base = '';

    public function __construct(string $pathToTemplate)
    {
        $this->base = $pathToTemplate . DS;
    }

    public function loadFile(string $filename, array $dataToExtract = [])
    {
        $fileInfo = pathinfo(Loader::pathConverter($filename));

        extract($dataToExtract);
        if (file_exists($filePath = $this->base . str_replace($this->base, '', $fileInfo['dirname']) . DS . ($fileInfo['filename']??'uknown') . '.php')) {
            if (ob_get_contents()) ob_end_clean();
            ob_start();
            include $filePath;
            $this->buffer .= ob_get_clean();
        } else {
            throw new \Exception("File $filePath not found!");
        }

        return $this;
    }

    public static function pathConverter(string $input)
    {
        return str_replace(['.','/','\\'], DS, trim($input, '.'));
    }

    public function truncateBuffer()
    {
        $this->buffer = '';
    }

    public function getBuffer()
    {
        $content = $this->buffer;
        $this->truncateBuffer();
        return $content;
    }

    public function __toString()
    {
        return $this->buffer;
    }
}
