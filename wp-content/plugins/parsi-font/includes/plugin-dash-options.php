<?php
function mw_dash_settings_page() { 
?>
    <div class="container">
		<div class="mweb">
			<?php echo'<a target="_blank" href="https://my.mandegarweb.com/aff.php?aff=62"><img class="mwfc-responsive" src="' . plugins_url( 'assets/images/mandegarweb.gif', dirname(__FILE__) ) . '"></a>'; ?>
        </div>
		<div class="mwtitle">
            <h2><span class="dashicons dashicons-dashboard"></span>
                <?php _e('MW Font Changer', 'mwfc'); ?>
            </h2>
        </div>
        <div id="tabs" class="tabs">
            <nav>
                <ul>
                    <li><a href="#section-1"><i class="fa fa-tachometer" aria-hidden="true"></i> <span><?php _e('Dashboard Font', 'mwfc'); ?></span></a></li>
                    <li><a href="#section-4"><i class="fa fa-question" aria-hidden="true"></i> <span><?php _e('Help', 'mwfc'); ?></span></a></li>
                    <li><a href="#section-5"><i class="fa fa-comments" aria-hidden="true"></i> <span><?php _e('Feedback', 'mwfc'); ?></span></a></li>
                </ul>
            </nav>
            <div class="content">
                <section id="section-1">
                    <?php include_once('plugin-dashboard-options.php'); ?>
                </section>
                <section id="section-4">
                    <?php include_once('help.php'); ?>
                    <section id="section-5">
                        <?php include_once('feedback.php'); ?>
                    </section>
            </div>
            <!-- /content -->
        </div>
        <!-- /tabs -->
    </div>
    <?php echo '<script src="' . plugins_url( 'assets/js/cbpFWTabs.js', dirname(__FILE__) ) . '"></script> '; ?>
    <script>
        new CBPFWTabs(document.getElementById('tabs'));
    </script>
    <?php
}