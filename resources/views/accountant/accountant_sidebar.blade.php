<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center"
        href="{{ route('accountant.accountant_db') }}">
        <div class="sidebar-brand-icon">
            <img src="{{ asset('img/idslogo.png') }}" alt="Logo"
                style="width: 55px; height: auto; filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.8));">
        </div>
        <div class="sidebar-brand-text mx-3">IDSC</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-1">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->is('accountant/accountant_db') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('accountant.accountant_db') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Nav Item - Transactions Group -->
    <li class="nav-item {{ request()->is('accountant/transactions*') || request()->is('accountant/pending-voids') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTransactions"
            aria-expanded="true" aria-controls="collapseTransactions">
            <i class="fas fa-fw fa-exchange-alt"></i>
            <span>Transactions</span>
        </a>

        <div id="collapseTransactions"
            class="collapse {{ request()->is('accountant/transactions*') || request()->is('accountant/pending-voids') || request()->is('accountant/transactions/*') ? 'show' : '' }}"
            aria-labelledby="headingTransactions" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">


                <a href="#" class="collapse-item collapsed" data-toggle="collapse" data-target="#collapseAllTransactions"
                    aria-expanded="true" aria-controls="collapseAllTransactions">
                    All Transactions
                </a>

                <div id="collapseAllTransactions"
                    class="collapse pl-3 {{ request()->is('accountant/transactions') || request()->is('accountant/transactions/*') ? 'show' : '' }}">
                    <a class="collapse-item {{ request()->is('accountant/transactions') ? 'active' : '' }}"
                        href="{{ route('accountant.transactions') }}">
                        College
                    </a>
                    <a class="collapse-item {{ request()->is('accountant/transactions/shs') ? 'active' : '' }}"
                        href="{{ route('accountant.transactions.shs') }}">
                        Senior High
                    </a>
                    <a class="collapse-item {{ request()->is('accountant/transactions/other') ? 'active' : '' }}"
                        href="{{ route('accountant.transactions.other') }}">
                        Other Fees
                    </a>
                    <a class="collapse-item {{ request()->is('accountant/transactions/uniform') ? 'active' : '' }}"
                        href="{{ route('accountant.transactions.uniform') }}">
                        Uniform
                    </a>
                    <a class="collapse-item {{ request()->is('accountant/transactions/old') ? 'active' : '' }}"
                        href="{{ route('accountant.transactions.old') }}">
                        Old Account
                    </a>
                </div>


                <a class="collapse-item {{ request()->is('accountant/pending-voids') ? 'active' : '' }}"
                    href="{{ route('accountant.pending_voids') }}">
                    Pending Voids
                </a>

            </div>
        </div>

    </li>
    <!-- Nav Item - Collections  -->
    <!-- || request()->is('accountant/collection-summary*') -->
    <li class="nav-item {{ request()->is('accountant/daily-collection*') || request()->is('accountant/collection-summary*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCollections"
            aria-expanded="true" aria-controls="collapseCollections">
            <i class="fas fa-fw fa-calendar-day"></i>
            <span>Collections</span>
        </a>
        <div id="collapseCollections"
            class="collapse {{ request()->is('accountant/daily-collection*') || request()->is('accountant/collection-summary*') ? 'show' : '' }}"
            aria-labelledby="headingCollections" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->is('accountant/daily-collection') ? 'active' : '' }}"
                    href="{{ route('accountant.daily_collection') }}">
                    Cashier Daily Reports
                </a>
                <a class="collapse-item {{ request()->is('accountant/collection-summary') ? 'active' : '' }}"
                    href="{{ route('accountant.collection_summary') }}">
                    Collection Summary
                </a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Back Deposit -->
    <li class="nav-item {{ request()->is('accountant/bank-deposit') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('accountant.bank_deposit') }}">
            <i class="fas fa-fw fa-credit-card"></i>
            <span>Bank Deposit</span>
        </a>
    </li>
    <!-- Nav Item - OLD ACCOUNTS -->
    <li class="nav-item {{ request()->is('accountant/old-accounts') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('accountant.old_accounts') }}">
            <i class="fas fa-fw fa-folder-open"></i>
            <span>Old Accounts</span>
        </a>
    </li>

    <!-- Nav Item - STUDENT ACCOUNT SUMMARY -->
    <!-- <li class="nav-item {{ request()->is('accountant/student-summary') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('accountant.student_summary') }}">
            <i class="fas fa-fw fa-clipboard-list"></i>
            <span>SOA</span>
        </a>
    </li> -->
     <li class="nav-item {{ request()->is('accountant/student-summary') || request()->is('accountant/shs-summary') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseShs"
            aria-expanded="true" aria-controls="collapseShs">
            <i class="fas fa-fw fa-calendar-day"></i>
            <span>SOA</span>
        </a>
        <div id="collapseShs"
            class="collapse {{ request()->is('accountant/student-summary') || request()->is('accountant/shs-summary') ? 'show' : '' }}"
            aria-labelledby="headingCollections" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->is('accountant/student-summary') ? 'active' : '' }}"
                    href="{{ route('accountant.student_summary') }}">
                   College
                </a>
                <a class="collapse-item {{ request()->is('vpadmin/shs-summary') ? 'active' : '' }}"
                    href="{{ route('accountant.shs_summary') }}">
                    Senior High
                </a>
            </div>
        </div>
    </li>
    <!-- Nav Item - SOA -->
    <!-- <li class="nav-item {{ request()->is('accountant/soa') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('accountant.soa') }}">
            <i class="fas fa-fw fa-file-invoice"></i>
            <span>SOA</span>
        </a>
    </li> -->

    <!-- Nav Item - Student Ledger -->
    <li class="nav-item {{ request()->is('accountant/student-ledger') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('accountant.student_ledger') }}">
            <i class="fas fa-fw fa-book"></i>
            <span>Student Ledger</span>
        </a>
    </li>

    <!-- Nav Item - Promisories -->
    <li class="nav-item {{ request()->is('accountant/promisories') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('accountant.promisories') }}">
            <i class="fas fa-fw fa-handshake"></i>
            <span>Promisories</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->