<?php
/**
 * Template Name: Conference Login
 */

// Enqueue Bootstrap before getting header so it is loaded in wp_head
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 'fcm-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' );
    wp_enqueue_script( 'fcm-bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js', array(), null, true );
});

get_header(); 
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5">
                    <h2 class="card-title text-center mb-4 fw-bold"><?php the_title(); ?></h2>

                    <?php if ( is_user_logged_in() ) : ?>
                        <div class="alert alert-info text-center">
                            <?php _e( 'You are already logged in.', 'conference-manager' ); ?>
                        </div>
                    <?php else : ?>
                        
                        <?php if ( isset($_GET['verified']) && $_GET['verified'] == '1' ) : ?>
                            <div class="alert alert-success">
                                <?php _e( 'Your email has been verified. You can now log in.', 'conference-manager' ); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ( isset($_GET['pwd_changed']) && $_GET['pwd_changed'] == '1' ) : ?>
                            <div class="alert alert-success">
                                <?php _e( 'Your password has been changed. Please log in with your new password.', 'conference-manager' ); ?>
                            </div>
                        <?php endif; ?>

                        <form method="post" action="" class="fcm-login-form needs-validation" novalidate>
                            <?php wp_nonce_field( 'fcm_login_action', 'fcm_login_nonce' ); ?>
                            
                            <div class="mb-4">
                                <label for="fcm_login_email" class="form-label fw-semibold"><?php _e( 'Email Address', 'conference-manager' ); ?> <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control border-start-0" name="fcm_login_email" id="fcm_login_email" placeholder="name@example.com" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="fcm_login_password" class="form-label fw-semibold"><?php _e( 'Password', 'conference-manager' ); ?> <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control border-start-0" name="fcm_login_password" id="fcm_login_password" placeholder="••••••••" required>
                                </div>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" name="fcm_submit_login" class="btn btn-primary btn-lg rounded-pill shadow-sm">
                                    <?php _e( 'Sign In', 'conference-manager' ); ?>
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-light text-center py-3 border-0 rounded-bottom-4">
                    <span class="text-muted">Don't have an account yet?</span> <a href="<?php echo site_url('/register'); ?>" class="text-decoration-none fw-bold">Register now</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Optional: Add Bootstrap Icons for the input groups -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<?php get_footer(); ?>
