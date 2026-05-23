<?php
/**
 * Template Name: Conference Dashboard
 */

// Enqueue Bootstrap before getting header so it is loaded in wp_head
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 'fcm-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' );
    wp_enqueue_script( 'fcm-bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js', array(), null, true );
});

get_header(); 
?>

<div class="container my-5">
    <?php
    if ( ! is_user_logged_in() ) :
    ?>
        <div class="alert alert-warning text-center">
            <?php _e( 'You must be logged in to view the dashboard.', 'conference-manager' ); ?> 
            <a href="<?php echo site_url('/login'); ?>" class="alert-link">Login here</a>
        </div>
    <?php
    else:
        $user_id = get_current_user_id();
        $user = get_userdata( $user_id );
        $active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'profile';
    ?>
        <div class="row">
            <!-- Sidebar Navigation -->
            <div class="col-md-3 mb-4">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4 text-center">
                        <?php 
                        $photo_id = get_user_meta( $user_id, 'passport_photo_id', true ); 
                        if ( $photo_id ) : 
                            echo wp_get_attachment_image( $photo_id, 'thumbnail', false, array('class' => 'rounded-circle mb-3 border', 'style' => 'width: 120px; height: 120px; object-fit: cover;') );
                        else :
                            echo '<div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3 border" style="width: 120px; height: 120px;"><i class="bi bi-person text-secondary" style="font-size: 3rem;"></i></div>';
                        endif;
                        ?>
                        <h5 class="fw-bold mb-1"><?php echo esc_html( $user->first_name . ' ' . $user->last_name ); ?></h5>
                        <p class="text-muted small mb-3"><?php echo esc_html( $user->user_email ); ?></p>
                        
                        <div class="nav flex-column nav-pills text-start" role="tablist" aria-orientation="vertical">
                            <a class="nav-link mb-2 rounded-3 <?php echo $active_tab === 'profile' ? 'active shadow-sm' : 'bg-light text-dark'; ?>" href="?tab=profile">
                                <i class="bi bi-person-circle me-2"></i> Profile
                            </a>
                            <a class="nav-link mb-2 rounded-3 <?php echo $active_tab === 'bookings' ? 'active shadow-sm' : 'bg-light text-dark'; ?>" href="?tab=bookings">
                                <i class="bi bi-calendar-check me-2"></i> Bookings
                            </a>
                            <a class="nav-link mb-2 rounded-3 <?php echo $active_tab === 'submissions' ? 'active shadow-sm' : 'bg-light text-dark'; ?>" href="?tab=submissions">
                                <i class="bi bi-file-earmark-text me-2"></i> Submissions
                            </a>
                            <a class="nav-link mt-4 text-danger bg-light rounded-3" href="<?php echo wp_logout_url( site_url() ); ?>">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="col-md-9">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <?php if ( $active_tab === 'profile' ) : ?>
                            
                            <h3 class="fw-bold mb-4">My Profile</h3>

                            <?php if ( isset( $_GET['updated'] ) ) : ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    Profile updated successfully.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ( isset( $_GET['photo_updated'] ) ) : ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    Passport photo uploaded successfully.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <?php if ( isset( $_GET['fcm_error'] ) ) : ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo esc_html( urldecode( $_GET['fcm_error'] ) ); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <div class="row g-5">
                                <!-- Photo Upload -->
                                <div class="col-lg-5 border-end">
                                    <h5 class="fw-semibold mb-3">Passport Photo</h5>
                                    <form method="post" action="" enctype="multipart/form-data" class="bg-light p-3 rounded-3">
                                        <?php wp_nonce_field( 'fcm_photo_action', 'fcm_photo_nonce' ); ?>
                                        <div class="mb-3">
                                            <label class="form-label small text-muted">Upload a clear passport photograph (Max 2MB)</label>
                                            <input class="form-control form-control-sm" type="file" name="fcm_passport_photo" accept="image/*" required>
                                        </div>
                                        <button type="submit" name="fcm_submit_passport_photo" class="btn btn-outline-primary btn-sm w-100">Upload Photo</button>
                                    </form>

                                    <h5 class="fw-semibold mt-5 mb-3">Change Password</h5>
                                    <form method="post" action="" class="bg-light p-3 rounded-3">
                                        <?php wp_nonce_field( 'fcm_password_action', 'fcm_password_nonce' ); ?>
                                        <div class="mb-3">
                                            <label class="form-label small text-muted">New Password</label>
                                            <input type="password" class="form-control form-control-sm" name="fcm_new_password" required>
                                        </div>
                                        <button type="submit" name="fcm_submit_password_change" class="btn btn-outline-danger btn-sm w-100">Update Password</button>
                                    </form>
                                </div>

                                <!-- Profile Form -->
                                <div class="col-lg-7">
                                    <h5 class="fw-semibold mb-3">Personal Information</h5>
                                    <form method="post" action="">
                                        <?php wp_nonce_field( 'fcm_profile_action', 'fcm_profile_nonce' ); ?>
                                        
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-medium">First Name</label>
                                                <input type="text" class="form-control bg-light" name="fcm_first_name" value="<?php echo esc_attr( $user->first_name ); ?>" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-medium">Last Name</label>
                                                <input type="text" class="form-control bg-light" name="fcm_last_name" value="<?php echo esc_attr( $user->last_name ); ?>" required>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label fw-medium">Phone Number</label>
                                                <input type="text" class="form-control bg-light" name="fcm_phone" value="<?php echo esc_attr( get_user_meta( $user_id, 'fcm_phone', true ) ); ?>" required>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label fw-medium">Institution/Organization</label>
                                                <input type="text" class="form-control bg-light" name="fcm_institution" value="<?php echo esc_attr( get_user_meta( $user_id, 'fcm_institution', true ) ); ?>">
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label fw-medium">Occupation</label>
                                                <input type="text" class="form-control bg-light" name="fcm_occupation" value="<?php echo esc_attr( get_user_meta( $user_id, 'fcm_occupation', true ) ); ?>">
                                            </div>
                                            <div class="col-12 mt-4 text-end">
                                                <button type="submit" name="fcm_submit_profile_update" class="btn btn-primary px-4 shadow-sm">Save Changes</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        <?php elseif ( $active_tab === 'bookings' ) : ?>
                            
                            <h3 class="fw-bold mb-4">My Bookings & Receipts</h3>
                            <?php
                            $bookings = get_posts( array(
                                'post_type' => 'conference_booking',
                                'author' => $user_id,
                                'numberposts' => -1,
                                'post_status' => 'publish'
                            ) );

                            if ( ! empty( $bookings ) ) {
                                echo '<div class="row g-4">';
                                foreach ( $bookings as $booking ) {
                                    $conference_id = get_post_meta( $booking->ID, 'conference_id', true );
                                    $status = get_post_meta( $booking->ID, 'payment_status', true );
                                    $amount = get_post_meta( $booking->ID, 'amount', true );
                                    $type = get_post_meta( $booking->ID, 'booking_type', true );
                                    
                                    $badge_class = $status === 'paid' ? 'bg-success' : 'bg-warning text-dark';
                                    
                                    echo '<div class="col-12">';
                                    echo '<div class="card border-0 bg-light rounded-4">';
                                    echo '<div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-center">';
                                    
                                    echo '<div class="mb-3 mb-md-0">';
                                    echo '<h5 class="fw-bold text-primary mb-1">' . ( $conference_id ? get_the_title( $conference_id ) : 'Submission Fee' ) . '</h5>';
                                    echo '<p class="text-muted small mb-2">Booking Reference: #' . $booking->ID . '</p>';
                                    echo '<div class="d-flex gap-3 text-dark">';
                                    echo '<span><i class="bi bi-tag me-1 text-muted"></i>' . ( $type ? ucwords(str_replace('_', ' ', $type)) : 'Attendance' ) . '</span>';
                                    echo '<span><i class="bi bi-cash me-1 text-muted"></i>' . ( $amount > 0 ? 'NGN ' . number_format($amount, 2) : 'Free' ) . '</span>';
                                    echo '</div>';
                                    echo '<div class="mt-2"><span class="badge ' . $badge_class . ' px-3 py-2 rounded-pill">' . strtoupper($status) . '</span></div>';
                                    echo '</div>';
                                    
                                    if ( $status === 'paid' ) {
                                        $qr_url = FCM_QR_Generator::get_qr_code_url( $booking->ID, '100x100' );
                                        echo '<div class="text-center bg-white p-2 rounded-3 shadow-sm border">';
                                        echo '<img src="' . esc_url( $qr_url ) . '" alt="QR Code" class="img-fluid" style="width: 80px;" />';
                                        echo '<div class="small text-muted mt-1 fw-bold" style="font-size: 0.75rem;">Admit One</div>';
                                        echo '</div>';
                                    }
                                    
                                    echo '</div></div></div>';
                                }
                                echo '</div>';
                            } else {
                                echo '<div class="text-center p-5 bg-light rounded-4">';
                                echo '<i class="bi bi-calendar-x text-muted mb-3" style="font-size: 3rem;"></i>';
                                echo '<h5 class="text-muted">No bookings found</h5>';
                                echo '<p class="text-muted small">You have not registered for any conferences yet.</p>';
                                echo '</div>';
                            }
                            ?>

                        <?php elseif ( $active_tab === 'submissions' ) : ?>
                            
                            <h3 class="fw-bold mb-4">My Submissions</h3>
                            <div class="text-center p-5 bg-light rounded-4">
                                <i class="bi bi-file-earmark-text text-muted mb-3" style="font-size: 3rem;"></i>
                                <h5 class="text-muted">No submissions found</h5>
                                <p class="text-muted small">Your abstract and paper submissions will appear here.</p>
                                <button class="btn btn-outline-primary mt-3" disabled>Submit Abstract (Coming Soon)</button>
                            </div>

                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<?php get_footer(); ?>
