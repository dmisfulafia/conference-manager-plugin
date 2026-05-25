@extends('layouts.admin')

@section('title', 'Manage Users')
@section('page_title', 'User Accounts Directory')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0">Review registered conference attendees, manage user passwords, and delete unverified or duplicate accounts.</p>
    @if(Auth::user()->isSuperAdmin())
        <button class="btn btn-academic px-4 py-2" data-bs-toggle="modal" data-bs-target="#addAdminModal">
            <i class="bi bi-person-plus-fill me-2"></i> Add Administrator
        </button>
    @endif
</div>

<div class="table-responsive shadow-sm">
    <table id="usersTable" class="table table-hover align-middle mb-0" style="width:100%">
        <thead class="table-light">
            <tr>
                <th>Title</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Other Names</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Country</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td class="fw-semibold">{{ $user->title }}</td>
                    <td>{{ $user->first_name }}</td>
                    <td>{{ $user->last_name }}</td>
                    <td class="text-muted">{{ $user->other_names ?? '-' }}</td>
                    <td><span class="badge bg-light text-dark font-monospace">{{ $user->email }}</span></td>
                    <td>{{ $user->phone }}</td>
                    <td>{{ $user->country }}</td>
                    <td>
                        @if($user->role === 'super_admin')
                            <span class="badge bg-danger text-white">SUPER ADMIN</span>
                        @elseif($user->role === 'admin')
                            <span class="badge bg-primary text-white">ADMIN</span>
                        @else
                            <span class="badge bg-secondary text-white">ATTENDEE</span>
                        @endif
                    </td>
                    <td>
                        @if($user->email_verified_at !== null)
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i> Verified</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><i class="bi bi-shield-slash-fill me-1"></i> Unverified</span>
                        @endif
                    </td>
                    <td>
                        <!-- Password and Delete Action Rules -->
                        @if($user->email_verified_at !== null)
                            <!-- Verified Accounts: Can change password, but CANNOT delete -->
                            @if(!$user->isAdmin() || Auth::user()->isSuperAdmin())
                                <button class="btn btn-warning btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#changePasswordModal{{ $user->id }}">
                                    <i class="bi bi-key-fill"></i> Password
                                </button>
                            @else
                                <button class="btn btn-secondary btn-sm" disabled title="Only Super Admin can change another Admin's password.">
                                    <i class="bi bi-lock-fill"></i> Protected
                                </button>
                            @endif
                        @else
                            <!-- Unverified Accounts: Can DELETE, but password changing is blocked -->
                            @if(!$user->isAdmin())
                                <form action="{{ route('admin.users.delete', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you absolutely sure you want to delete this UNVERIFIED account? This action is permanent.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm shadow-sm">
                                        <i class="bi bi-trash-fill"></i> Delete
                                    </button>
                                </form>
                            @else
                                <span class="text-muted small">System Admin</span>
                            @endif
                        @endif
                    </td>
                </tr>

                @if($user->email_verified_at !== null && (!$user->isAdmin() || Auth::user()->isSuperAdmin()))
                <!-- Change Password Modal for this specific user -->
                <div class="modal fade" id="changePasswordModal{{ $user->id }}" tabindex="-1" aria-labelledby="changePasswordLabel{{ $user->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                            <div class="modal-header border-bottom-0 pb-0" style="background-color: var(--fulafia-gold); color: white; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 20px;">
                                <h5 class="modal-title heading-font fw-bold" id="changePasswordLabel{{ $user->id }}">Change Password</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('admin.users.password', $user) }}" method="POST" class="p-4">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Account</label>
                                    <input type="text" class="form-control bg-light" value="{{ $user->title }} {{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})" disabled>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">New Password</label>
                                    <input type="password" name="password" class="form-control" required placeholder="Minimum 8 characters">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Confirm Password</label>
                                    <input type="password" name="password_confirmation" class="form-control" required placeholder="Confirm new password">
                                </div>
                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn btn-gold btn-lg">Update Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </tbody>
    </table>
</div>

@if(Auth::user()->isSuperAdmin())
<!-- Add Admin Modal -->
<div class="modal fade" id="addAdminModal" tabindex="-1" aria-labelledby="addAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 pb-0" style="background-color: var(--academic-green); color: white; border-top-left-radius: 16px; border-top-right-radius: 16px; padding: 20px;">
                <h5 class="modal-title heading-font fw-bold" id="addAdminModalLabel">Create Administrator Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.add-admin') }}" method="POST" class="p-4">
                @csrf
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-semibold">Title</label>
                        <select name="title" class="form-select" required>
                            <option value="Prof.">Prof.</option>
                            <option value="Dr.">Dr.</option>
                            <option value="Mr.">Mr.</option>
                            <option value="Mrs.">Mrs.</option>
                            <option value="Ms.">Ms.</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">First Name</label>
                        <input type="text" name="first_name" class="form-control" required placeholder="First name">
                    </div>
                    <div class="col-md-5 mb-3">
                        <label class="form-label fw-semibold">Last Name</label>
                        <input type="text" name="last_name" class="form-control" required placeholder="Last name">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" class="form-control" required placeholder="e.g. admin@fulafia.edu.ng">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Phone Number</label>
                        <input type="text" name="phone" class="form-control" required placeholder="e.g. 080XXXXXXXX">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Gender</label>
                        <select name="gender" class="form-select" required>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Role Level</label>
                        <select name="role" class="form-select" required>
                            <option value="admin">Regular Admin</option>
                            <option value="super_admin">Super Admin</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Country</label>
                        <input type="text" name="country" class="form-control" required value="Nigeria">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Occupation</label>
                        <input type="text" name="occupation" class="form-control" required value="University Staff">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Other Names</label>
                        <input type="text" name="other_names" class="form-control" placeholder="Middle Name (Optional)">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control" required placeholder="Minimum 8 characters">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required placeholder="Re-enter password">
                    </div>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-academic btn-lg">Create Admin Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#usersTable').DataTable({
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'FULafia_Conference_Attendees',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                    }
                },
                {
                    extend: 'csvHtml5',
                    title: 'FULafia_Conference_Attendees',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                    }
                },
                'print'
            ],
            language: {
                searchPlaceholder: "Search accounts...",
                search: ""
            }
        });
        
        // Style search box to look beautiful in Bootstrap
        $('.dataTables_filter input').addClass('form-control d-inline-block w-auto ms-2 mb-3');
    });
</script>
@endsection
