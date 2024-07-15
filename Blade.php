<?php
namespace SLiMS\Theme;

class Blade
{
    public static function removeXss(string|array $input)
    {
        $fromString = false;
        if (is_string($input)) {
            $input = [$input];
            $fromString = true;
        }

        foreach ($input as $key => $value) {
            if (substr($key, -1) == '!') {
                unset($input[$key]);
                $input[trim($key, '!')] = $value;
                continue;
            }

            if (is_array($value)) {
                $input[$key] = self::removeXss($value);
                continue;
            }

            if (!is_string($value)) {
                continue;
            }
            
            $input[$key] = addslashes(strip_tags($value));
        }

        return $fromString ? $input[0] : $input;
    }

    public static function toEntities(string $input)
    {
        return htmlspecialchars($input);
    }
}
