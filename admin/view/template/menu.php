<div class="app-brand demo ">
    <a href="/home" class="app-brand-link">
      <span class=" demo menu-text fw-bolder ms-auto">EV Charging Display</span>
    </a>
    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="bx bx-chevron-left bx-sm align-middle"></i>
    </a>
</div>
<div class="menu-inner-shadow"></div>
    <ul class="menu-inner py-1 ps ps--active-y">
        <!-- Dashboards -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Welcome <?=$currentUser->displayName ?></span>
        </li>
        <li class="menu-item">
            <a href="/home/logout" class="menu-link">
                <i class="menu-icon tf-icons bx bx-log-out"></i>
                <div data-i18n="Logout">Logout</div>
            </a>
        </li>
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Menu</span>
        </li>
        <li class="menu-item">
            <a href="/CMS" class="menu-link">
                <i class="menu-icon tf-icons bx bx-folder-open"></i>
                <div data-i18n="CMS">CMS</div>
            </a>
        </li>
        <!--
        <li class="menu-item">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-home"></i>
            <div data-i18n="Dashboards">Home</div>
        </a>
        <ul class="menu-sub">
            <li class="menu-item">
            <a href="dashboards-analytics.html" class="menu-link">
                <div data-i18n="Analytics">Analytics</div>
            </a>
            </li>
            <li class="menu-item">
            <a href="dashboards-crm.html" class="menu-link">
                <div data-i18n="CRM">CRM</div>
            </a>
            </li>
            <li class="menu-item">
            <a href="dashboards-ecommerce.html" class="menu-link">
                <div data-i18n="eCommerce">eCommerce</div>
            </a>
            </li>
        </ul>
        </li>
        -->

        <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
        <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
        </div>
        <div class="ps__rail-y" style="top: 0px; height: 893px; right: 4px;">
        <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 463px;"></div>
        </div>
    </ul>