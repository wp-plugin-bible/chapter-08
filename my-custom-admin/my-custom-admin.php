<?php
/*
Plugin Name: My Admin
*/

add_action( 'admin_menu', 'my_admin_menu' );

function my_admin_menu() {

    add_options_page(
        __('My Submenu', 'my-custom-admin'),
        __('My Submenu', 'my-custom-admin'),
        'manage_options',
        'my-submenu',
        'my_submenu'
    );
}

function my_submenu() {
?>
<div class="wrap">
<h2>My Submenu</h2>

<form id="my-submenu-form" method="post" action="">
    <?php wp_nonce_field( 'my-nonce-key', 'my-submenu' ); ?>

    <p><?php echo esc_html( __( 'E-mail Address', 'my-custom-admin' ) ); ?>:
        <input type="text" name="my-data"
            value="<?php echo esc_attr( get_option( 'my-data' ) ); ?>"></p>
    <p><input type="submit"
            value="<?php echo esc_attr( __( 'Save', 'my-custom-admin' ) ); ?>"
            class="button button-primary button-large"></p>
</form>

</div>
<?php
}

add_action( 'admin_init', 'my_admin_init' );

function my_admin_init()
{
    if ( isset( $_POST['my-submenu'] ) && $_POST['my-submenu'] ){
        if ( check_admin_referer( 'my-nonce-key', 'my-submenu' ) ){
            $e = new WP_Error();
            if ( isset($_POST['my-data']) && $_POST['my-data'] ) {
                if ( is_email( trim( $_POST['my-data'] ) ) ) {
                    update_option( 'my-data', trim( $_POST['my-data'] ) );
                } else {
                    $e->add(
                        'error',
                        __( 'Please enter a valid email address.',
                                'my-custom-admin' )
                    );
                    set_transient( 'my-custom-admin-errors',
                            $e->get_error_messages(), 10 );
                }
            } else {
                update_option( 'my-data', '' );
            }

            wp_safe_redirect( menu_page_url( ‘my-submenu’, false ) );
        }
    }
}

add_action( 'admin_notices', 'my_admin_notices' );

function my_admin_notices() {
?>
    <?php if ( $messages = get_transient( 'my-custom-admin-errors' ) ): ?>
    <div class="updated">
        <ul>
            <?php foreach( $messages as $message ): ?>
                <li><?php echo esc_html($message); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
<?php

}
