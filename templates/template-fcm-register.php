<?php
/**
 * Template Name: Conference Registration
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
        <div class="col-lg-8 col-md-10">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5">
                    <h2 class="card-title text-center mb-4 fw-bold"><?php the_title(); ?></h2>
                    
                    <?php if ( is_user_logged_in() ) : ?>
                        <div class="alert alert-info text-center">
                            <?php _e( 'You are already registered and logged in.', 'conference-manager' ); ?>
                        </div>
                    <?php else : ?>
                        
                        <?php if ( isset($_GET['registered']) && $_GET['registered'] == '1' ) : ?>
                            <div class="alert alert-success">
                                <?php _e( 'Registration successful! Please check your email to verify your account.', 'conference-manager' ); ?>
                            </div>
                        <?php endif; ?>

                        <form method="post" action="" class="fcm-register-form needs-validation" novalidate>
                            <?php wp_nonce_field( 'fcm_register_action', 'fcm_register_nonce' ); ?>
                            
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="fcm_title" class="form-label fw-semibold"><?php _e( 'Title', 'conference-manager' ); ?> <span class="text-danger">*</span></label>
                                    <select class="form-select" name="fcm_title" id="fcm_title" required>
                                        <option value="" selected disabled>Select...</option>
                                        <option value="Mr.">Mr.</option>
                                        <option value="Ms.">Ms.</option>
                                        <option value="Mrs.">Mrs.</option>
                                        <option value="Dr.">Dr.</option>
                                        <option value="Prof.">Prof.</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="fcm_first_name" class="form-label fw-semibold"><?php _e( 'First Name', 'conference-manager' ); ?> <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="fcm_first_name" id="fcm_first_name" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="fcm_last_name" class="form-label fw-semibold"><?php _e( 'Last Name', 'conference-manager' ); ?> <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="fcm_last_name" id="fcm_last_name" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="fcm_email" class="form-label fw-semibold"><?php _e( 'Email Address', 'conference-manager' ); ?> <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="fcm_email" id="fcm_email" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="fcm_password" class="form-label fw-semibold"><?php _e( 'Password', 'conference-manager' ); ?> <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="fcm_password" id="fcm_password" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="fcm_phone" class="form-label fw-semibold"><?php _e( 'Phone Number', 'conference-manager' ); ?> <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="fcm_phone" id="fcm_phone" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="fcm_gender" class="form-label fw-semibold"><?php _e( 'Gender', 'conference-manager' ); ?></label>
                                    <select class="form-select" name="fcm_gender" id="fcm_gender">
                                        <option value="" selected disabled>Select...</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="fcm_occupation" class="form-label fw-semibold"><?php _e( 'Occupation', 'conference-manager' ); ?></label>
                                    <input type="text" class="form-control" name="fcm_occupation" id="fcm_occupation">
                                </div>
                                <div class="col-md-6">
                                    <label for="fcm_institution" class="form-label fw-semibold"><?php _e( 'Institution/Organization', 'conference-manager' ); ?></label>
                                    <input type="text" class="form-control" name="fcm_institution" id="fcm_institution">
                                </div>

                                <div class="col-md-12">
                                    <label for="fcm_country" class="form-label fw-semibold"><?php _e( 'Country', 'conference-manager' ); ?></label>
                                    <input type="text" class="form-control" name="fcm_country" id="fcm_country">
                                </div>

                                <div class="col-12 mt-4 text-center">
                                    <button type="submit" name="fcm_submit_registration" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">
                                        <?php _e( 'Create Account', 'conference-manager' ); ?>
                                    </button>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-light text-center py-3 border-0 rounded-bottom-4">
                    <span class="text-muted">Already have an account?</span> <a href="<?php echo site_url('/login'); ?>" class="text-decoration-none fw-bold">Login here</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
