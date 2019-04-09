<?php
add_action('admin_head', 'my_admin_head');
function my_admin_head() {
$options = get_option('dash_font_settings');
?>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style type="text/css">
        body.rtl,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        #wpadminbar a,
        .rtl #wpadminbar,
        #wpadminbar,
        body {
            font-family: <?php esc_attr_e($options['dashmwfcfont']); ?> !important;
        }

        .rtl #wpadminbar * {
            font-family: <?php esc_attr_e($options['dashmwfcfont']); ?>;
        }
    </style>
    <?php
}
add_action('admin_head', 'mwfc_admin_head');
function mwfc_admin_head() {
echo '<style type="text/css">
.errorwppafe {
    width: 88%;
    border: 1px #d3400d solid;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    padding: 10px 10px 10px 10px;
    text-align: center !important;
    display: block !important;
    float: none !important;
    margin-right: auto !important;
    margin-left: auto !important;
    background: yellow !important;
    margin-top: 15px !important;
}

pre,
code {
    font-family: VRCD, monospaced;
}

.okwppafe {
    width: 94%;
    border: 1px #a1cb45 solid;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    margin: 5px 10px 10px 10px;
    padding: 10px 10px 10px 10px;
    background: #eaf8cc;
    display: block;
    text-align: center;
    margin-left: auto;
    margin-right: auto;
    float: none;
}

.clear {
    clear: both
}

form {
    margin: 0px;
    padding: 0px;
}

input,
select {
    padding: 5px;
    font-size: 10pt;
    border: 1px solid #cacaca;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
}

.mwtitle {
    margin-top: 40px;
    font-size: 50px;
    text-align: center;
}

.mwtitle h2 {
    line-height: 1.5em;
}

.mwfc-responsive {
    display: block;
    max-width: 100%;
    height: auto;
    margin: auto;
    border: 1px solid black;
}

#template textarea {
    direction: ltr;
    text-align: left;
    font-family: VRCD;
}

.mwfcsteps li {
    line-height: 1.5em;
}
</style>';
}