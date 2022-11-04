<?php
/**
 * Helper function to display dates in different formats.
 * Note: This function only works in English. It should be adapted to work with *any* language.
 * @param {string} $str The date.
 * @param {string} $str Format of the given date.
 * @return {string}
 */
function pretty_date($str, $source_fmt = 'j M Y') {
    $d = date_parse_from_format($source_fmt, $str);
    $t = mktime(0, 0, 0, $d['month'], $d['day'], $d['year']);
    return date('F n, Y', $t);
}

/**
 * Shortand function to print a translatable message.
 * @param {string} $str The message.
 */
function _e($str) {
    echo _($str);
}

/**
 * Set translation domain path, relative to current app's dir.
 * @param {string} $domain Translation domain; e.g. "messages".
 * @return {bool}
 */
function gettext_load_domain($domain = 'messages', $encoding = 'UTF-8') {
    // Load translations from the `locale` dir.
    $lc_dir = __DIR__ . '/locale';

    $res = bindtextdomain($domain, $lc_dir);
    if (!$res) {
        trigger_error(sprintf('Could not load "%s" domain codeset from %s', $domain, $lc_dir));
        return FALSE;
    }

    // It is important to specify the encoding. Must match that of the PO file.
    $res = bind_textdomain_codeset($domain, $encoding);
    if (!$res) {
        trigger_error('Could not bind "%s" domain codeset. Is it installed?');
        return FALSE;
    }

    return TRUE;
}

/**
 * Apply user interface translations based on a given locale.
 * @param {string} $lc_iso Language locale.
 * @return {bool}
 */
function gettext_apply_translations($lc_iso, $domain = 'messages') {
    if (!$lc_iso) {
        trigger_error('Cannot translate from an empty locale.');
        return FALSE;
    }

    // NB: Apparently Windows servers need *both* `setlocale()` and `putenv()`,
    // but our application requires a Unix-based OS so we don't need to call `putenv()`.
    $res = setlocale(LC_MESSAGES, $lc_iso);
    if (!$res) {
        trigger_error(sprintf('Could not set the "%s" locale. Is it installed?', $lc_iso));
        return FALSE;
    }

    // Also localize dates. No need to trigger another error if the previous one was thrown.
    setlocale(LC_TIME, $lc_iso);

    // Indicate the name of the MO file, without extension.
    gettext_load_domain($domain);

    // Set default domain for the gettext function.
    $res = textdomain($domain);
    if (!$res) {
        trigger_error(sprintf('Could not set "%s" as default domain. Does it exist?', $domain));
        return FALSE;
    }

    return TRUE;
}

// Point to an already installed locale in our OS. Hint: Run `locale -a`.
// For testing purposes, it doesn't matter the actual language of the messages.
// What matters is that every string has been internationalized properly.
gettext_apply_translations('es_ES.utf8');
