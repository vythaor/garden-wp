<?php

/**
 *  View of Facebook button area when connected.
 * 
 * @uses at class-htcc-admin.php 
 */

if (!defined('ABSPATH')) exit;

?>

<?php

$logout_button = [
    'logout_path' => $logout_path
];

HT_CC::view('ht-cc-admin-logout-button', $logout_button);

?>

<h5>Connected Facebook page</h5>
<div class="connected-page">    
    <div class="active-page-info">
        <div class="connected-page-title">
            <?php echo $connected_page['name']; ?> <span>(Connected)</span>
        </div>
        <div class="connected-page-settings">
            <a class="button-lazy-load" href="<?php echo $connected_page['path']; ?>">Disconnect</a>
            <div class="lazyload"></div>           
        </div>
        <input type="hidden" name="htcc_options[fb_page_id]" value="<?php echo $connected_page['remote_id'] ?>">
    </div>
</div>



