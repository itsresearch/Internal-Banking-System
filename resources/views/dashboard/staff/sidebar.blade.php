<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
          <div class="app-brand demo">
            <a href="index.html" class="app-brand-link">
              <span class="app-brand-logo demo">
                <span class="text-primary">
                  <svg
                    width="25"
                    viewBox="0 0 25 42"
                    version="1.1"
                    xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink">
                    
                    <g id="g-app-brand" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <g id="Brand-Logo" transform="translate(-27.000000, -15.000000)">
                        <g id="Icon" transform="translate(27.000000, 15.000000)">
                          <g id="Mask" transform="translate(0.000000, 8.000000)">
                            <mask id="mask-2" fill="white">
                              <use xlink:href="#path-1"></use>
                            </mask>
                            <use fill="currentColor" xlink:href="#path-1"></use>
                            <g id="Path-3" mask="url(#mask-2)">
                              <use fill="currentColor" xlink:href="#path-3"></use>
                              <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-3"></use>
                            </g>
                            <g id="Path-4" mask="url(#mask-2)">
                              <use fill="currentColor" xlink:href="#path-4"></use>
                              <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-4"></use>
                            </g>
                          </g>
                          <g
                            id="Triangle"
                            transform="translate(19.000000, 11.000000) rotate(-300.000000) translate(-19.000000, -11.000000) ">
                            <use fill="currentColor" xlink:href="#path-5"></use>
                            <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-5"></use>
                          </g>
                        </g>
                      </g>
                    </g>
                  </svg>
                </span>
              </span>
              <span class="app-brand-text demo menu-text fw-bold ms-2">Sneat</span>
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
              <i class="bx bx-chevron-left d-block d-xl-none align-middle"></i>
            </a>
          </div>

          <div class="menu-divider mt-0"></div>

          <div class="menu-inner-shadow"></div>

          <ul class="menu-inner py-1">
            <!-- Dashboards -->
            <li class="menu-item active open">
              <a href="{{ route('dashboard.staff') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-smile"></i>
                <div class="text-truncate" data-i18n="Dashboards">Dashboards</div>
              </a>
            </li>

            <!-- Layouts -->
            <li class="menu-item">
              <a href="{{ route('customers.customersList') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-layout"></i>
                <div class="text-truncate" data-i18n="Layouts">Customers</div>
              </a>
            </li>

            <li class="menu-header small text-uppercase">
              <span class="menu-header-text">Transactions</span>
            </li>
            <li class="menu-item">
              <a href="{{ route('teller.deposit') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-plus-circle"></i>
                <div class="text-truncate" data-i18n="Deposit">Deposit</div>
              </a>
            </li>
            <li class="menu-item">
              <a href="{{ route('teller.withdrawal') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-minus-circle"></i>
                <div class="text-truncate" data-i18n="Withdrawal">Withdrawal</div>
              </a>
            </li>
            <li class="menu-item">
              <a href="{{ route('teller.transfer') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-transfer"></i>
                <div class="text-truncate" data-i18n="Transfer">Transfer</div>
              </a>
            </li>
            <li class="menu-item">
              <a href="{{ route('teller.history') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-receipt"></i>
                <div class="text-truncate" data-i18n="History">History</div>
              </a>
            </li>

          </ul>
        </aside>