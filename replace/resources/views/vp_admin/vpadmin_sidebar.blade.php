<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon">
            <img src="{{ asset('img/idslogo.png') }}" alt="Logo"
                style="width: 55px; height: auto; filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.8));">

        </div>
        <div class="sidebar-brand-text mx-3">IDSC <sup></sup></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-1">

    <!-- Nav Item - Dsashboard -->
    <li class="nav-item {{ request()->is('vpadmin_dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('vpadmin_dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Sidebar Menu:
    </div>
    <li class="nav-item {{ request()->is('vpadmin/transactions*')  ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTransactions"
            aria-expanded="true" aria-controls="collapseTransactions">
            <i class="fas fa-fw fa-exchange-alt"></i>
            <span>Transactions</span>
        </a>

        <div id="collapseTransactions"
            class="collapse {{ request()->is('vpadmin/transactions*')  || request()->is('vpadmin/transactions/*') ? 'show' : '' }}"
            aria-labelledby="headingTransactions" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">


                <!-- <a href="#" class="collapse-item collapsed" data-toggle="collapse" data-target="#collapseAllTransactions"
                    aria-expanded="true" aria-controls="collapseAllTransactions">
                    All Transactions
                </a> -->

                <!-- <div id="collapseAllTransactions"
                    class="collapse pl-3 {{ request()->is('vpadmin/transactions') || request()->is('vpadmin/transactions/*') ? 'show' : '' }}">
                    
                </div> -->

                <a class="collapse-item {{ request()->is('vpadmin/transactions') ? 'active' : '' }}"
                    href="{{ route('vpadmin.transactions') }}">
                    College
                </a>
                <a class="collapse-item {{ request()->is('vpadmin/transactions/shs') ? 'active' : '' }}"
                    href="{{ route('vpadmin.transactions.shs') }}">
                    Senior High
                </a>
                <a class="collapse-item {{ request()->is('vpadmin/transactions/other') ? 'active' : '' }}"
                    href="{{ route('vpadmin.transactions.other') }}">
                    Other Fees
                </a>
                <a class="collapse-item {{ request()->is('vpadmin/transactions/uniform') ? 'active' : '' }}"
                    href="{{ route('vpadmin.transactions.uniform') }}">
                    Uniform
                </a>
                <a class="collapse-item {{ request()->is('vpadmin/transactions/old') ? 'active' : '' }}"
                    href="{{ route('vpadmin.transactions.old') }}">
                    Old Account
                </a>
                <!-- <a class="collapse-item {{ request()->is('accountant/pending-voids') ? 'active' : '' }}"
                    href="{{ route('accountant.pending_voids') }}">
                    Pending Voids
                </a> -->

            </div>
        </div>

    </li>
    <li class="nav-item {{ request()->is('vpadmin/daily-collection*') || request()->is('vpadmin/collection-summary*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCollections"
            aria-expanded="true" aria-controls="collapseCollections">
            <i class="fas fa-fw fa-calendar-day"></i>
            <span>Collections</span>
        </a>
        <div id="collapseCollections"
            class="collapse {{ request()->is('vpadmin/daily-collection*') || request()->is('vpadmin/collection-summary*') ? 'show' : '' }}"
            aria-labelledby="headingCollections" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->is('vpadmin/daily-collection') ? 'active' : '' }}"
                    href="{{ route('vpadmin.daily_collection') }}">
                    Cashier Daily Reports
                </a>
                <a class="collapse-item {{ request()->is('vpadmin/collection-summary') ? 'active' : '' }}"
                    href="{{ route('vpadmin.collection_summary') }}">
                    Collection Summary
                </a>
            </div>
        </div>
    </li>
    <li class="nav-item {{ request()->is('vpadmin/bank-deposit') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('vpadmin.bank_deposit') }}">
            <i class="fas fa-fw fa-credit-card"></i>
            <span>Bank Deposit</span>
        </a>
    </li>

     <!-- Nav Item - OLD ACCOUNTS -->
    <li class="nav-item {{ request()->is('vpadmin/old-accounts') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('vpadmin.old_accounts') }}">
            <i class="fas fa-fw fa-folder-open"></i>
            <span>Old Accounts</span>
        </a>
    </li>

    <!-- Nav Item - STUDENT ACCOUNT SUMMARY -->

     <li class="nav-item {{ request()->is('vpadmin/student-summary') || request()->is('vpadmin/shs-summary') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseShs"
            aria-expanded="true" aria-controls="collapseShs">
            <i class="fas fa-fw fa-calendar-day"></i>
            <span>SOA</span>
        </a>
        <div id="collapseShs"
            class="collapse {{ request()->is('vpadmin/student-summary') || request()->is('vpadmin/shs-summary') ? 'show' : '' }}"
            aria-labelledby="headingCollections" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->is('vpadmin/student-summary') ? 'active' : '' }}"
                    href="{{ route('vpadmin.student_summary') }}">
                   College
                </a>
                <a class="collapse-item {{ request()->is('vpadmin/shs-summary') ? 'active' : '' }}"
                    href="{{ route('vpadmin.shs_summary') }}">
                    Senior High
                </a>
            </div>
        </div>
    </li>
    <!-- Nav Item - Fees -->
    <li class="nav-item {{ request()->is('fees/*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseFees"
            aria-expanded="{{ request()->is('fees/*') ? 'true' : 'false' }}" aria-controls="collapseFees">
            <i class="fas fa-fw fa-dollar-sign"></i>
            <span>Fees</span>
        </a>
        <div id="collapseFees" class="collapse {{ request()->is('fees/*') ? 'show' : '' }}"
            aria-labelledby="headingFees" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->is('fees/edit-tuition') ? 'active' : '' }}"
                    href="{{ url('fees/edit-tuition') }}">Manage Scholarship Fees</a>
                <a class="collapse-item {{ request()->is('fees/misc-fees') ? 'active' : '' }}"
                    href="{{ url('fees/misc-fees') }}">Misc. Fees Manager</a>
                <a class="collapse-item {{ request()->is('fees/other-fees') ? 'active' : '' }}"
                    href="{{ url('fees/other-fees') }}">Manage Other Fees</a>
            </div>
        </div>
    </li>


    <!-- Nav Item - Academic -->
    <li class="nav-item {{ request()->is('academic/*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAcademic"
            aria-expanded="{{ request()->is('academic/*') ? 'true' : 'false' }}" aria-controls="collapseAcademic">
            <i class="fas fa-fw fa-calendar-alt"></i>
            <span>Academic</span>
        </a>
        <div id="collapseAcademic" class="collapse {{ request()->is('academic/*') ? 'show' : '' }}"
            aria-labelledby="headingAcademic" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->is('academic/term-configuration') ? 'active' : '' }}"
                    href="{{ url('academic/term-configuration') }}">Term Configuration</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - User Management -->
    <li class="nav-item {{ request()->is('user-management/*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUserManagement"
            aria-expanded="{{ request()->is('user-management/*') ? 'true' : 'false' }}"
            aria-controls="collapseUserManagement">
            <i class="fas fa-fw fa-users"></i>
            <span>User Management</span>
        </a>
        <div id="collapseUserManagement" class="collapse {{ request()->is('user-management/*') ? 'show' : '' }}"
            aria-labelledby="headingUserManagement" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->is('user-management/add-new') ? 'active' : '' }}"
                    href="{{ url('user-management/add-new') }}">Add New User</a>
                <a class="collapse-item {{ request()->is('user-management/activate') ? 'active' : '' }}"
                    href="{{ url('user-management/activate') }}">Manage User Status</a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->