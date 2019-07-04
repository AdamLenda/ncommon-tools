<?php
namespace NCommon\Tools;
class CharacterSetConstants
{
    const ALPHA = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    const ALPHA_AND_DIGITS = self::ALPHA.self::DIGITS;
    const ALPHA_LOWER = 'abcdefghijklmnopqrstuvwxyz';
    const ALPHA_LOWER_AND_DIGITS = self::ALPHA_LOWER.self::DIGITS;
    const ALPHA_UPPER = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const ALPHA_UPPER_AND_DIGITS = self::ALPHA_UPPER.self::DIGITS;
    const DIGITS = '0123456789';
    const HEX_LOWER = '0123456789abcdef';
}