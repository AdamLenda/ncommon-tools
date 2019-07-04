<?php

namespace NCommon\Tools;

class StringTools
{
    /**
     * This function helps indent text
     *
     * @param string|object $string
     * @param $number_of_tabs
     * @return string
     */
    public static function tabPadNewLines($string, $number_of_tabs)
    {
        if (is_object($string)) {
            if (method_exists($string, '__toString')) {
                $string = $string->__toString();
            } else {
                $string = get_class($string).' does not implement __toString()';
            }
        }
        return preg_replace("/\n/", "\n".str_repeat("\t", $number_of_tabs), $string);
    }

    /**
     * @param $string
     * @param $repetitions
     * @param $characters
     *
     * @return null|string|string[]
     */
    public static function padNewLines($string, $repetitions, $characters = ' ')
    {
        if (is_object($string)) {
            if (method_exists($string, '__toString')) {
                $string = $string->__toString();
            } else {
                $string = get_class($string).' does not implement __toString()';
            }
        }
        return preg_replace("/\n/", "\n".str_repeat($characters, $repetitions), $string);
    }

    /**
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    public static function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    /**
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    public static function endsWith($haystack, $needle)
    {
        $length = strlen($needle);

        return $length === 0 ||
            (substr($haystack, -$length) === $needle);
    }

    /**
     * Removes as all $characterToRemove from the end of a string.
     * @param string $characterToRemove
     * @param string $stringToModify
     * @return string
     */
    public static function removeTrailing($characterToRemove, $stringToModify)
    {
        while (!empty($stringToModify) && substr($stringToModify, -1) == $characterToRemove) {
            $stringToModify = rtrim($stringToModify, $characterToRemove);
        }
        return $stringToModify;
    }

    /**
     * @param string $haystack
     * @param string $needle
     * @return string Return's $needle.$haystack unless $haystack already starts with $needle
     */
    public static function ensureBeginsWith($haystack, $needle)
    {
        if (empty($haystack)) {
            return $needle;
        }

        if ($haystack[0] == $needle) {
            return $haystack;
        }

        return $needle.$haystack;
    }

    /**
     * @param string $haystack
     * @param string $needle
     * @return string Return's $haystack.$needle unless $haystack already ends with $needle
     */
    public static function ensureEndsWith($haystack, $needle)
    {
        if (empty($haystack)) {
            return $needle;
        }

        if ($haystack[strlen($haystack)-1] == $needle) {
            return $haystack;
        }

        return $haystack.$needle;
    }

    /**
     * Note that this function is not suitable for security / cryptographic purposes. It is just a centralized place to
     * keep logic that has been copy and pasted throughout JVZoo when creating transaction identifiers.
     *
     * Sections that look like this:
     *
     * <code>
     * $x = strtolower(substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',10)),0,10))
     * </code>
     *
     * which, more readably written shows it's quite a bit of stuff logic...
     *
     * <code>
     * $x = strtolower(
     *        substr(
     *            str_shuffle(
     *                str_repeat(
     *                    'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
     *                    10
     *                )
     *            ),
     *            0,
     *            10
     *        )
     *    )
     * </code>
     *
     * can now be replaced with
     *
     * <code>
     * $x = StringTools::randomString(10, CharacterSetConstants::ALPHA_LOWER_AND_DIGITS)
     * </code>
     *
     * Which is more readable, more maintainable, AND requires fewer CPU instructions to execute
     *
     * @param $length
     * @param string $characterSet
     * @return string a randomized string of characters
     */
    public static function randomString($length, $characterSet = CharacterSetConstants::ALPHA_UPPER_AND_DIGITS)
    {
        $characterSet = str_shuffle($characterSet);
        $result = [];
        for ($i = 0; $i < $length; $i++) {
            $result[] = $characterSet[rand(0, strlen($characterSet) - 1)];
        }
        return implode($result);
    }

    public static function hexToBase36($hexString)
    {
        if (empty($hexString)) {
            return null;
        }

        $parts = explode(
            '|',
            trim(
                chunk_split($hexString, 4, '|'),
                '|'
            )
        );

        $result = '';
        foreach ($parts as $part) {
            if (empty($part) || strlen($part) !== 4) {
                continue;
            }
            $result .= base_convert($part, 16, 36);
        }
        return $result;
    }

    /**
     * Attempts to SAFELY convert a value of any type into a string
     *
     * @param mixed $value
     * @param integer|null $maxLength
     *
     * @return string
     */
    public static function asString($value, $maxLength = null)
    {
        if (empty($value)) {
            return '';
        }
        if (!is_string($value)) {
            if (is_scalar($value)) {
                $value = (string)$value;
            } elseif (is_array($value)) {
                $value = print_r($value, true);
            } elseif (is_object($value)) {
                if (method_exists($value, '__toString')) {
                    $value = $value->__toString();
                } elseif (method_exists($value, 'ToString')) {
                    $value = $value->ToString();
                } else {
                    $value = get_class($value);
                }
            } else {
                $value = gettype($value);
            }
        }

        if ($maxLength !== null && is_int($maxLength)) {
            return substr($value, 0, $maxLength);
        }
        return $value;
    }

    /**
     * Converts "my_field_name" or "my.field.name" or "my_field.name" to "MyFieldName"
     *
     * @param $string
     * @param array $splitOnCharacters
     *
     * @return null|string
     */
    public static function toCapitalCamelCase($string, $splitOnCharacters = ['.', '_'])
    {
        if (empty($string)) {
            return null;
        }

        if (empty($splitOnCharacters)) {
            return ucfirst($string);
        }

        $capitalCamelCaseString = $string;
        foreach ($splitOnCharacters as $splitOnCharacter) {
            if (empty($capitalCamelCaseString)) {
                // shouldn't happen, but just in case...
                return null;
            }
            if (strpos($capitalCamelCaseString, $splitOnCharacter) === false) {
                //not found, skip
                continue;
            }

            $stringParts = explode($splitOnCharacter, $capitalCamelCaseString);
            if (empty($stringParts)) {
                // shouldn't happen, but just in case...
                continue;
            }

            $processedStringParts = [];
            foreach ($stringParts as $keyPart) {
                if (empty($keyPart)) {
                    continue;
                }
                if (strlen($keyPart) > 1) {
                    $keyPart = ucfirst($keyPart);
                }
                $processedStringParts[] = $keyPart;
            }
            $capitalCamelCaseString = implode('', $processedStringParts);
        }
        return ucfirst($capitalCamelCaseString);
    }

    /**
     * Use this method for tracking the longest string in a series. Pass the current value as the first parameter and
     * the integer length of longest string seen thus far as the second. Compares the strlen($value) against
     * $minimumLength and returns the highest of the two values.
     *
     * @param $value
     * @param $minimumLength
     *
     * @return int
     */
    public static function getLengthIfGreaterThan($value, $minimumLength)
    {
        $fieldWidth = strlen($value);
        if ($fieldWidth > $minimumLength) {
            return $fieldWidth;
        }
        return $minimumLength;
    }
}