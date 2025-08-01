<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('president.dashboard') }}">
        <div class="sidebar-brand-icon">
            <img src="{{ asset('img/idslogo.png') }}" alt="Logo"
                style="width: 55px; height: auto; filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.8));">
        </div>
        <div class="mx-3 sidebar-brand-text">IDSC</div>
    </a>

    <!-- Divider -->
    <hr class="my-1 sidebar-divider">

    <!-- Dashboard -->
    <li class="nav-item {{ request()->routeIs('president.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('president.dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <li class="nav-item {{ request()->is('president/transactions*')  ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTransactions"
            aria-expanded="true" aria-controls="collapseTransactions">
            <i class="fas fa-fw fa-exchange-alt"></i>
            <span>Transactions</span>
        </a>

        <div id="collapseTransactions"
            class="collapse {{ request()->is('president/transactions*')  || request()->is('president/transactions/*') ? 'show' : '' }}"
            aria-labelledby="headingTransactions" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">


                <!-- <a href="#" class="collapse-item collapsed" data-toggle="collapse" data-target="#collapseAllTransactions"
                    aria-expanded="true" aria-controls="collapseAllTransactions">
                    All Transactions
                </a> -->

                <!-- <div id="collapseAllTransactions"
                    class="collapse pl-3 {{ request()->is('president/transactions') || request()->is('president/transactions/*') ? 'show' : '' }}">
                    
                </div> -->

                <a class="collapse-item {{ request()->is('president/transactions') ? 'active' : '' }}"
                    href="{{ route('president.transactions') }}">
                    College
                </a>
                <a class="collapse-item {{ request()->is('president/transactions/shs') ? 'active' : '' }}"
                    href="{{ route('president.transactions.shs') }}">
                    Senior High
                </a>
                <a class="collapse-item {{ request()->is('president/transactions/other') ? 'active' : '' }}"
                    href="{{ route('president.transactions.other') }}">
                    Other Fees
                </a>
                <a class="collapse-item {{ request()->is('president/transactions/uniform') ? 'active' : '' }}"
                    href="{{ route('president.transactions.uniform') }}">
                    Uniform
                </a>
                <a class="collapse-item {{ request()->is('president/transactions/old') ? 'active' : '' }}"
                    href="{{ route('president.transactions.old') }}">
                    Old Account
                </a>
                <!-- <a class="collapse-item {{ request()->is('accountant/pending-voids') ? 'active' : '' }}"
                    href="{{ route('accountant.pending_voids') }}">
                    Pending Voids
                </a> -->

            </div>
        </div>

    </li>
     <li class="nav-item {{ request()->is('president/daily-collection*') || request()->is('president/collection-summary*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCollections"
            aria-expanded="true" aria-controls="collapseCollections">
            <i class="fas fa-fw fa-calendar-day"></i>
            <span>Collections</span>
        </a>
        <div id="collapseCollections"
            class="collapse {{ request()->is('president/daily-collection*') || request()->is('president/collection-summary*') ? 'show' : '' }}"
            aria-labelledby="headingCollections" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->is('president/daily-collection') ? 'active' : '' }}"
                    href="{{ route('president.daily_collection') }}">
                    Cashier Daily Reports
                </a>
                <a class="collapse-item {{ request()->is('president/collection-summary') ? 'active' : '' }}"
                    href="{{ route('president.collection_summary') }}">
                    Collection Summary
                </a>
            </div>
        </div>
    </li>
     <li class="nav-item {{ request()->is('president/student-summary') || request()->is('president/shs-summary') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseShs"
            aria-expanded="true" aria-controls="collapseShs">
            <i class="fas fa-fw fa-calendar-day"></i>
            <span>SOA</span>
        </a>
        <div id="collapseShs"
            class="collapse {{ request()->is('president/student-summary') || request()->is('president/shs-summary') ? 'show' : '' }}"
            aria-labelledby="headingCollections" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->is('president/student-summary') ? 'active' : '' }}"
                    href="{{ route('president.student_summary') }}">
                   College
                </a>
                <a class="collapse-item {{ request()->is('president/shs-summary') ? 'active' : '' }}"
                    href="{{ route('president.shs_summary') }}">
                    Senior High
                </a>
            </div>
        </div>
    </li>
    <li class="nav-item {{ request()->is('president/bank-deposit') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('president.bank_deposit') }}">
            <i class="fas fa-fw fa-credit-card"></i>
            <span>Bank Deposit</span>
        </a>
    </li>
     <li class="nav-item {{ request()->routeIs('president.online_payments') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('president.online_payments') }}">
         <i class="fas fa-wallet fa-fw"></i>
            <span>Online Payments</span>
        </a>
    </li>
    <li class="nav-item {{ request()->routeIs('president.accounting-dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('president.accounting-dashboard') }}">
            <i class="fas fa-fw fa-calculator"></i>
            <span>Accounting</span>
        </a>
    </li>

    <!-- Analytics Submenu -->
    <!-- <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAnalytics"
            aria-expanded="false" aria-controls="collapseAnalytics">
            <i class="fas fa-fw fa-chart-bar"></i>
            <span>Analytics</span>
        </a>
        <div id="collapseAnalytics" class="collapse" data-parent="#accordionSidebar">
            <div class="py-2 bg-white rounded collapse-inner">
                <a class="collapse-item {{ request()->routeIs('president.revenue-trends') ? 'active' : '' }}"
                    href="{{ route('president.revenue-trends') }}">Revenue Trends</a>
                <a class="collapse-item {{ request()->routeIs('president.scholarships-discounts') ? 'active' : '' }}"
                    href="{{ route('president.scholarships-discounts') }}">Scholarships & Discounts</a>
                <a class="collapse-item {{ request()->routeIs('president.enrollment-heatmap') ? 'active' : '' }}"
                    href="{{ route('president.enrollment-heatmap') }}">Enrollment Heatmap</a>
                <a class="collapse-item {{ request()->routeIs('president.financial-alerts') ? 'active' : '' }}"
                    href="{{ route('president.financial-alerts') }}">Financial Alerts</a>
            </div>
        </div>
    </li> -->

    <!-- Reports Dropdown/Submenu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseReports"
            aria-expanded="false" aria-controls="collapseReports">
            <i class="fas fa-fw fa-folder-open"></i>
            <span>Reports</span>
        </a>
        <div id="collapseReports" class="collapse" data-parent="#accordionSidebar">
            <div class="py-2 bg-white rounded collapse-inner">
                <a class="collapse-item" href="#">Monthly</a>
                <a class="collapse-item" href="#">Annual</a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler -->
    <div class="text-center d-none d-md-inline">
        <button class="border-0 rounded-circle" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->