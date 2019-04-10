<?php
/**
 * Added Languages - Messenger .. 
 * 
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'HTCC_Lang' ) ) :

class HTCC_Lang {

    static $fb_lang = array(
        'af_ZA' => 'Afrikaans',
		'sq_AL' => 'Albanian',
		'hy_AM' => 'Armenian',
		'ar_AR' => 'Arabic',
		'az_AZ' => 'Azerbaijani',
		'be_BY' => 'Belarusian',
		'bg_BG' => 'Bulgarian *',  // tried - not worked
		'bn_IN' => 'Bengali',
		'bs_BA' => 'Bosnian',
		'eu_ES' => 'Basque',
		'ca_ES' => 'Catalan',
		'cs_CZ' => 'Czech',
		'hr_HR' => 'Croatian',
		'da_DK' => 'Danish',
		'nl_NL' => 'Dutch',
		'en_US' => 'English',
		'eo_EO' => 'Esperanto',
		'tl_PH' => 'Filipino',
		'fi_FI' => 'Finnish',
		'et_EE' => 'Estonian',
		'fo_FO' => 'Faroese',
		'fr_FR' => 'French (France)',
		'fr_CA' => 'French (Canada)',
		'fy_NL' => 'Frisian',
		'de_DE' => 'German',
		'el_GR' => 'Greek',
		'ka_GE' => 'Georgian *',  // tried - not worked
		'gl_ES' => 'Galician',
		'he_IL' => 'Hebrew',
		'hi_IN' => 'Hindi',
		'hu_HU' => 'Hungarian',
		'id_ID' => 'Indonesian',
		'it_IT' => 'Italian',
		'is_IS' => 'Icelandic',
		'ga_IE' => 'Irish',
		'ja_JP' => 'Japanese',
		'ko_KR' => 'Korean',
		'ku_TR' => 'Kurdish',
		'km_KH' => 'Khmer',
		'la_VA' => 'Latin',
		'lt_LT' => 'Lithuanian *',  // tried - not worked
        'lv_LV' => 'Latvian *',  // tried - not worked
        'mr_IN' => 'Marathi',
		'mk_MK' => 'Macedonian',
		'ml_IN' => 'Malayalam',
		'ms_MY' => 'Malay',
		'nb_NO' => 'Norwegian (bokmal)',
		'ne_NP' => 'Nepali',
		'nn_NO' => 'Norwegian (nynorsk)',
		'fa_IR' => 'Persian',
		'pa_IN' => 'Punjabi',
		'pl_PL' => 'Polish',
		'ps_AF' => 'Pashto',
		'pt_BR' => 'Portuguese (Brazil)',
		'pt_PT' => 'Portuguese (Portugal)',
		'ro_RO' => 'Romanian',
		'ru_RU' => 'Russian',
		'es_LA' => 'Spanish',
		'es_ES' => 'Spanish (Spain)',
		'sk_SK' => 'Slovak *',   // tried - not worked
		'sl_SI' => 'Slovenian',
		'sr_RS' => 'Serbian',
		'sv_SE' => 'Swedish',
		'sw_KE' => 'Swahili',
		'ta_IN' => 'Tamil',
		'te_IN' => 'Telugu',
		'th_TH' => 'Thai',
		'tr_TR' => 'Turkish',
		'uk_UA' => 'Ukrainian',
		'vi_VN' => 'Vietnamese',
		'cy_GB' => 'Welsh',
		'zh_CN' => 'zh_CN Simplified Chinese (China)',
		'zh_HK' => 'zh_HK Traditional Chinese (Hong Kong)',
		'zh_TW' => 'zh_TW Traditional Chinese (Taiwan)',
    );
    

    // http://www.loc.gov/standards/iso639-2/php/code_list.php
    // https://developers.facebook.com/docs/internationalization/
    // https://developers.facebook.com/apps/156589268405824/app-details/localize/
    // https://developers.facebook.com/docs/messenger-platform/messenger-profile/supported-locales  ( but not all as expected )

}

endif; // END class_exists check