<style>
    .modal-dialog {
        max-height: 90vh;
        max-width: 600px;
    }

    .modal-content {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    .modal-header {
        background-color: rgb(28, 200, 138);
        color: white;
        border-bottom: none;
        padding: 15px 20px;
    }

    .modal-title {
        font-weight: 600;
        font-size: 18px;
    }

    .close {
        opacity: 0.8;
        font-size: 1.4rem;
    }

    .close:hover {
        opacity: 1;
    }

    .modal-body {
        padding: 25px;
    }

    .list-group-item {
        border: none;
        font-weight: 500;
        padding: 12px 15px;
        transition: background 0.2s;
        cursor: pointer;
    }

    .list-group-item:hover:not(.active) {
        background-color: #f1f1f1;
    }

    .list-group-item.active {
        background-color: rgb(28, 200, 138) !important;
        color: white;
        border-radius: 8px;
    }

   
    .form-group label {
        font-weight: 500;
        margin-bottom: 6px;
    }

    .form-control {
        border-radius: 8px;
        transition: 0.2s;
    }

    .form-control:focus {
        border-color: rgb(28, 200, 138);
        box-shadow: 0 0 0 0.15rem rgba(28, 200, 138, 0.25);
    }

   
    .btn-theme {
        background-color: rgb(28, 200, 138);
        border-color: rgb(28, 200, 138);
        color: white;
        border-radius: 8px;
        padding: 8px 18px;
        font-weight: 500;
    }

    .btn-theme:hover {
        background-color: rgb(24, 180, 125);
        border-color: rgb(24, 180, 125);
    }

    .btn-outline-theme {
        border-radius: 8px;
        border-color: rgb(28, 200, 138);
        color: rgb(28, 200, 138);
        font-weight: 500;
    }

    .btn-outline-theme:hover {
        background-color: rgb(28, 200, 138);
        color: white;
    }

    /* Password toggle button */
    .input-group-append .btn {
        border-radius: 0 8px 8px 0;
    }
</style>


<div class="modal fade" id="settingsModal" tabindex="-1" role="dialog" aria-labelledby="settingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header theme-bg theme-text">
                <h5 class="modal-title" style="color: black;"><i class="fas fa-cogs mr-2" ></i>Settings</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="color: black;"> 
                    <span style="color: black;">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 border-right">
                        <div class="list-group" id="settingsMenu">
                            <button class="list-group-item list-group-item-action active" data-section="security">
                                <i class="fas fa-shield-alt mr-2"></i>Security
                            </button>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div id="section-security">
                            <h5>Change Password</h5>
                            <form method="POST" action="{{ route('password.change') }}">
                                @csrf

                                <div class="form-group">
                                    <label for="currentPassword">Current Password</label>
                                    <div class="input-group">
                                        <input type="password" name="currentPassword" class="form-control" id="currentPassword" required placeholder="Enter current password">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-theme toggle-password" type="button" data-target="#currentPassword">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="newPassword">New Password</label>
                                    <div class="input-group">
                                        <input type="password" name="newPassword" class="form-control" id="newPassword" required placeholder="Enter new password">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-theme toggle-password" type="button" data-target="#newPassword">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="newPassword_confirmation">Confirm New Password</label>
                                    <div class="input-group">
                                        <input type="password" name="newPassword_confirmation" class="form-control" id="newPassword_confirmation" required placeholder="Confirm new password">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-theme toggle-password" type="button" data-target="#newPassword_confirmation">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-theme">Update Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('#settingsMenu button').forEach(button => {
        button.addEventListener('click', () => {
            document.querySelectorAll('#settingsMenu button').forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            const section = button.getAttribute('data-section');
            document.querySelectorAll('[id^="section-"]').forEach(sec => sec.style.display = 'none');
            document.getElementById('section-' + section).style.display = 'block';
        });
    });

    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function () {
            const target = document.querySelector(this.dataset.target);
            const icon = this.querySelector('i');
            if (target.type === 'password') {
                target.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                target.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });
</script>
