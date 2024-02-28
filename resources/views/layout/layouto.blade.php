 @include('include.header')
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
    <div id="main-wrapper">
        
        <header class="topbar">
            <nav class="navbar top-navbar navbar-expand-md navbar-dark">
                <div class="navbar-header border-right">
                    <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i class="ti-menu ti-close"></i></a>
                    <a class="navbar-brand" href="index.html">
                        <!-- Logo icon -->
                        <b class="logo-icon">
                           
                            <img src="{{url('assets/images/logos/logo-icon.png')}}" alt="homepage" class="dark-logo" />
                          
                        </b>
                        
                    </a>
                    <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><i class="ti-more"></i></a>
                </div>
                
                <div class="navbar-collapse collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav float-left mr-auto">
                        <li class="nav-item d-none d-md-block"><a class="nav-link sidebartoggler waves-effect waves-light" href="javascript:void(0)" data-sidebartype="mini-sidebar"><i class="mdi mdi-menu font-18"></i></a></li>
                    </ul>
                    <ul class="navbar-nav float-right">
                      
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle waves-effect waves-dark" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            
                                <span class="ml-2 font-medium">Steve</span><span class="fas fa-angle-down ml-2"></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
                                <div class="d-flex no-block align-items-center p-3 mb-2 border-bottom">
                                    <div class=""><img src="../../assets/images/users/1.jpg" alt="user" class="rounded" width="80"></div>
                                    <div class="ml-2">
                                        <h4 class="mb-0">Pda Account</h4>
                                        <p class=" mb-0 text-muted">pdaadmin@gmail.com</p>
                                       <!--  <a href="javascript:void(0)" class="btn btn-sm btn-danger text-white mt-2 btn-rounded">View Profile</a> -->
                                    </div>
                                </div>
                                <a class="dropdown-item" href="javascript:void(0)"><i class="ti-settings mr-1 ml-1"></i> Account Setting</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="javascript:void(0)"><i class="fa fa-power-off mr-1 ml-1"></i> Logout</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
      
        <aside class="left-sidebar">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <li class="sidebar-item">
                          
                            <ul aria-expanded="false" class="collapse  first-level">
                                <li class="sidebar-item">
                                    <a href="javascript:void(0)" class="sidebar-link">
                                        <i class="ti-user"></i>
                                        <span class="hide-menu"> My Profile </span>
                                    </a>
                                </li>
                               
                               
                              
                             
                            </ul>
                        </li>
                       
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                                <i class="mdi mdi-cart-outline"></i>
                                <span class="hide-menu">Ecommerce</span>
                            </a>
                            <ul aria-expanded="false" class="collapse first-level">
                                <li class="sidebar-item">
                                    <a href="eco-products.html" class="sidebar-link">
                                        <i class="mdi mdi-cards-variant"></i>
                                        <span class="hide-menu">Products</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="eco-products-cart.html" class="sidebar-link">
                                        <i class="mdi mdi-cart"></i>
                                        <span class="hide-menu">Products Cart</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="eco-products-edit.html" class="sidebar-link">
                                        <i class="mdi mdi-cart-plus"></i>
                                        <span class="hide-menu">Products Edit</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="eco-products-detail.html" class="sidebar-link">
                                        <i class="mdi mdi-camera-burst"></i>
                                        <span class="hide-menu">Product Details</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="eco-products-orders.html" class="sidebar-link">
                                        <i class="mdi mdi-chart-pie"></i>
                                        <span class="hide-menu">Product Orders</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="eco-products-checkout.html" class="sidebar-link">
                                        <i class="mdi mdi-clipboard-check"></i>
                                        <span class="hide-menu">Products Checkout</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                                <i class="mdi mdi-format-color-fill"></i>
                                <span class="hide-menu">Ui Elements </span>
                                <span class="badge badge-info badge-pill ml-auto mr-3 font-medium px-2 py-1">12</span>
                            </a>
                            <ul aria-expanded="false" class="collapse first-level">
                                <li class="sidebar-item">
                                    <a href="ui-buttons.html" class="sidebar-link">
                                        <i class="mdi mdi-toggle-switch"></i>
                                        <span class="hide-menu"> Buttons</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="ui-modals.html" class="sidebar-link">
                                        <i class="mdi mdi-tablet"></i>
                                        <span class="hide-menu"> Modals</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="ui-tab.html" class="sidebar-link">
                                        <i class="mdi mdi-sort-variant"></i>
                                        <span class="hide-menu"> Tab</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="ui-tooltip-popover.html" class="sidebar-link">
                                        <i class="mdi mdi-image-filter-vintage"></i>
                                        <span class="hide-menu"> Tooltip &amp; Popover</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="ui-notification.html" class="sidebar-link">
                                        <i class="mdi mdi-message-bulleted"></i>
                                        <span class="hide-menu"> Notification</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="ui-progressbar.html" class="sidebar-link">
                                        <i class="mdi mdi-poll"></i>
                                        <span class="hide-menu"> Progressbar</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="ui-typography.html" class="sidebar-link">
                                        <i class="mdi mdi-format-line-spacing"></i>
                                        <span class="hide-menu"> Typography</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="ui-bootstrap.html" class="sidebar-link">
                                        <i class="mdi mdi-bootstrap"></i>
                                        <span class="hide-menu"> Bootstrap Ui</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="ui-breadcrumb.html" class="sidebar-link">
                                        <i class="mdi mdi-equal"></i>
                                        <span class="hide-menu"> Breadcrumb</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="ui-list-media.html" class="sidebar-link">
                                        <i class="mdi mdi-file-video"></i>
                                        <span class="hide-menu"> List Media</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="ui-grid.html" class="sidebar-link">
                                        <i class="mdi mdi-view-module"></i>
                                        <span class="hide-menu"> Grid</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="ui-carousel.html" class="sidebar-link">
                                        <i class="mdi mdi-view-carousel"></i>
                                        <span class="hide-menu"> Carousel</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                                <i class="mdi mdi-content-copy"></i>
                                <span class="hide-menu">Sample Pages</span>
                                <span class="badge badge-warning text-white badge-pill ml-auto mr-3 font-medium px-2 py-1">25</span>
                            </a>
                            <ul aria-expanded="false" class="collapse first-level">
                                <li class="sidebar-item">
                                    <a href="starter-kit.html" class="sidebar-link">
                                        <i class="mdi mdi-crop-free"></i>
                                        <span class="hide-menu">Starter Kit</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a class="has-arrow sidebar-link" href="javascript:void(0)" aria-expanded="false">
                                        <i class="mdi mdi-email-open-outline"></i>
                                        <span class="hide-menu">Email Templates</span>
                                    </a>
                                    <ul aria-expanded="false" class="collapse second-level">
                                        <li class="sidebar-item">
                                            <a href="email-templete-alert.html" class="sidebar-link">
                                                <i class="mdi mdi-message-alert"></i>
                                                <span class="hide-menu"> Alert </span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="email-templete-basic.html" class="sidebar-link">
                                                <i class="mdi mdi-message-bulleted"></i>
                                                <span class="hide-menu"> Basic</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="email-templete-billing.html" class="sidebar-link">
                                                <i class="mdi mdi-message-draw"></i>
                                                <span class="hide-menu"> Billing</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="email-templete-password-reset.html" class="sidebar-link">
                                                <i class="mdi mdi-message-bulleted-off"></i>
                                                <span class="hide-menu"> Password-Reset</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="sidebar-item">
                                    <a class="has-arrow sidebar-link" href="javascript:void(0)" aria-expanded="false">
                                        <i class="mdi mdi-account-circle"></i>
                                        <span class="hide-menu">Authentication</span>
                                    </a>
                                    <ul aria-expanded="false" class="collapse second-level">
                                        <li class="sidebar-item">
                                            <a href="authentication-login1.html" class="sidebar-link">
                                                <i class="mdi mdi-account-key"></i>
                                                <span class="hide-menu"> Login </span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="authentication-login2.html" class="sidebar-link">
                                                <i class="mdi mdi-account-key"></i>
                                                <span class="hide-menu"> Login 2 </span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="authentication-register1.html" class="sidebar-link">
                                                <i class="mdi mdi-account-plus"></i>
                                                <span class="hide-menu"> Register</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="authentication-register2.html" class="sidebar-link">
                                                <i class="mdi mdi-account-plus"></i>
                                                <span class="hide-menu"> Register 2</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="authentication-lockscreen.html" class="sidebar-link">
                                                <i class="mdi mdi-account-off"></i>
                                                <span class="hide-menu"> Lockscreen</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="authentication-recover-password.html" class="sidebar-link">
                                                <i class="mdi mdi-account-convert"></i>
                                                <span class="hide-menu"> Recover password</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="sidebar-item">
                                    <a class="has-arrow sidebar-link" href="javascript:void(0)" aria-expanded="false">
                                        <i class="mdi mdi-alert-box"></i>
                                        <span class="hide-menu">Error Pages</span>
                                    </a>
                                    <ul aria-expanded="false" class="collapse second-level">
                                        <li class="sidebar-item">
                                            <a href="error-400.html" class="sidebar-link">
                                                <i class="mdi mdi-alert-outline"></i>
                                                <span class="hide-menu"> Error 400 </span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="error-403.html" class="sidebar-link">
                                                <i class="mdi mdi-alert-outline"></i>
                                                <span class="hide-menu"> Error 403</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="error-404.html" class="sidebar-link">
                                                <i class="mdi mdi-alert-outline"></i>
                                                <span class="hide-menu"> Error 404</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="error-500.html" class="sidebar-link">
                                                <i class="mdi mdi-alert-outline"></i>
                                                <span class="hide-menu"> Error 500</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="error-503.html" class="sidebar-link">
                                                <i class="mdi mdi-alert-outline"></i>
                                                <span class="hide-menu"> Error 503</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="sidebar-item">
                                    <a href="pages-animation.html" class="sidebar-link">
                                        <i class="mdi mdi-debug-step-over"></i>
                                        <span class="hide-menu">Animation</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="pages-search-result.html" class="sidebar-link">
                                        <i class="mdi mdi-search-web"></i>
                                        <span class="hide-menu">Search Result</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="pages-gallery.html" class="sidebar-link">
                                        <i class="mdi mdi-camera-iris"></i>
                                        <span class="hide-menu">Gallery</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="pages-treeview.html" class="sidebar-link">
                                        <i class="mdi mdi-file-tree"></i>
                                        <span class="hide-menu">Treeview</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="pages-block-ui.html" class="sidebar-link">
                                        <i class="mdi mdi-codepen"></i>
                                        <span class="hide-menu">Block UI</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="pages-session-timeout.html" class="sidebar-link">
                                        <i class="mdi mdi-timer-off"></i>
                                        <span class="hide-menu">Session Timeout</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="pages-session-idle-timeout.html" class="sidebar-link">
                                        <i class="mdi mdi-timer-sand-empty"></i>
                                        <span class="hide-menu">Session Idle Timeout</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="pages-utility-classes.html" class="sidebar-link">
                                        <i class="mdi mdi-tune"></i>
                                        <span class="hide-menu">Helper Classes</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="pages-maintenance.html" class="sidebar-link">
                                        <i class="mdi mdi-camera-iris"></i>
                                        <span class="hide-menu">Maintenance Page</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                                <i class="mdi mdi-apps"></i>
                                <span class="hide-menu">Apps</span>
                            </a>
                            <ul aria-expanded="false" class="collapse first-level">
                                <li class="sidebar-item">
                                    <a href="app-chats.html" class="sidebar-link">
                                        <i class="mdi mdi-comment-processing-outline"></i>
                                        <span class="hide-menu">Chat Message</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a class="has-arrow sidebar-link" href="javascript:void(0)" aria-expanded="false">
                                        <i class="mdi mdi-inbox-arrow-down"></i>
                                        <span class="hide-menu">Inbox</span>
                                    </a>
                                    <ul aria-expanded="false" class="collapse second-level">
                                        <li class="sidebar-item">
                                            <a href="inbox-email.html" class="sidebar-link">
                                                <i class="mdi mdi-email"></i>
                                                <span class="hide-menu"> Email </span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="inbox-email-detail.html" class="sidebar-link">
                                                <i class="mdi mdi-email-alert"></i>
                                                <span class="hide-menu"> Email Detail </span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="inbox-email-compose.html" class="sidebar-link">
                                                <i class="mdi mdi-email-secure"></i>
                                                <span class="hide-menu"> Email Compose </span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="sidebar-item">
                                    <a class="has-arrow sidebar-link" href="javascript:void(0)" aria-expanded="false">
                                        <i class="ti-user"></i>
                                        <span class="hide-menu">Contacts</span>
                                    </a>
                                    <ul aria-expanded="false" class="collapse second-level">
                                        <li class="sidebar-item">
                                            <a href="contact-list.html" class="sidebar-link">
                                                <i class="icon-people"></i>
                                                <span class="hide-menu"> Contact List </span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="contact-grid.html" class="sidebar-link">
                                                <i class="icon-user-follow"></i>
                                                <span class="hide-menu"> Contacts Grid </span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="sidebar-item">
                                    <a class="has-arrow sidebar-link" href="javascript:void(0)" aria-expanded="false">
                                        <i class="mdi mdi-bookmark-plus-outline"></i>
                                        <span class="hide-menu">Tickets</span>
                                    </a>
                                    <ul aria-expanded="false" class="collapse second-level">
                                        <li class="sidebar-item">
                                            <a href="ticket-list.html" class="sidebar-link">
                                                <i class="mdi mdi-book-multiple"></i>
                                                <span class="hide-menu"> Ticket List </span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="ticket-detail.html" class="sidebar-link">
                                                <i class="mdi mdi-book-plus"></i>
                                                <span class="hide-menu"> Ticket Detail </span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="sidebar-item">
                                    <a href="app-taskboard.html" class="sidebar-link">
                                        <i class="mdi mdi-bulletin-board"></i>
                                        <span class="hide-menu"> Taskboard </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <div class="devider"></div>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                                <i class="mdi mdi-tune-vertical"></i>
                                <span class="hide-menu">Sidebar Type </span>
                            </a>
                            <ul aria-expanded="false" class="collapse  first-level">
                                <li class="sidebar-item">
                                    <a href="sidebar-type-minisidebar.html" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu"> Minisidebar </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="sidebar-type-iconsidebar.html" class="sidebar-link">
                                        <i class="mdi mdi-view-parallel"></i>
                                        <span class="hide-menu"> Icon Sidebar </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="sidebar-type-overlaysidebar.html" class="sidebar-link">
                                        <i class="mdi mdi-view-day"></i>
                                        <span class="hide-menu"> Overlay Sidebar </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="sidebar-type-fullsidebar.html" class="sidebar-link">
                                        <i class="mdi mdi-view-array"></i>
                                        <span class="hide-menu"> Full Sidebar </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                                <i class="mdi mdi-content-copy"></i>
                                <span class="hide-menu">Page Layouts </span>
                            </a>
                            <ul aria-expanded="false" class="collapse  first-level">
                                <li class="sidebar-item">
                                    <a href="layout-inner-fixed-left-sidebar.html" class="sidebar-link">
                                        <i class="mdi mdi-format-align-left"></i>
                                        <span class="hide-menu"> Inner Fixed Left Sidebar </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="layout-inner-fixed-right-sidebar.html" class="sidebar-link">
                                        <i class="mdi mdi-format-align-right"></i>
                                        <span class="hide-menu"> Inner Fixed Right Sidebar </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="layout-inner-left-sidebar.html" class="sidebar-link">
                                        <i class="mdi mdi-format-float-left"></i>
                                        <span class="hide-menu"> Inner Left Sidebar </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="layout-inner-right-sidebar.html" class="sidebar-link">
                                        <i class="mdi mdi-format-float-right"></i>
                                        <span class="hide-menu"> Inner Right Sidebar </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="page-layout-fixed-header.html" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu"> Fixed Header </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="page-layout-fixed-sidebar.html" class="sidebar-link">
                                        <i class="mdi mdi-view-parallel"></i>
                                        <span class="hide-menu"> Fixed Sidebar </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="page-layout-fixed-header-sidebar.html" class="sidebar-link">
                                        <i class="mdi mdi-view-column"></i>
                                        <span class="hide-menu"> Fixed Header &amp; Sidebar </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="page-layout-boxed-layout.html" class="sidebar-link">
                                        <i class="mdi mdi-view-carousel"></i>
                                        <span class="hide-menu"> Box Layout </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <div class="devider"></div>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                                <i class="mdi mdi-clipboard-text"></i>
                                <span class="hide-menu">Forms</span>
                            </a>
                            <ul aria-expanded="false" class="collapse first-level">
                                <li class="sidebar-item">
                                    <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                                        <i class="mdi mdi-collage"></i>
                                        <span class="hide-menu">Form Elements</span>
                                    </a>
                                    <ul aria-expanded="false" class="collapse first-level">
                                        <li class="sidebar-item">
                                            <a href="form-inputs.html" class="sidebar-link">
                                                <i class="mdi mdi-priority-low"></i>
                                                <span class="hide-menu"> Forms Input</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="form-input-groups.html" class="sidebar-link">
                                                <i class="mdi mdi-rounded-corner"></i>
                                                <span class="hide-menu"> Input Groups</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="form-input-grid.html" class="sidebar-link">
                                                <i class="mdi mdi-select-all"></i>
                                                <span class="hide-menu"> Input Grid</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="form-checkbox-radio.html" class="sidebar-link">
                                                <i class="mdi mdi-shape-plus"></i>
                                                <span class="hide-menu"> Checkboxes &amp; Radios</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="form-bootstrap-touchspin.html" class="sidebar-link">
                                                <i class="mdi mdi-switch"></i>
                                                <span class="hide-menu"> Bootstrap Touchspin</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="form-bootstrap-switch.html" class="sidebar-link">
                                                <i class="mdi mdi-toggle-switch-off"></i>
                                                <span class="hide-menu"> Bootstrap Switch</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="form-select2.html" class="sidebar-link">
                                                <i class="mdi mdi-relative-scale"></i>
                                                <span class="hide-menu"> Select2</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="form-dual-listbox.html" class="sidebar-link">
                                                <i class="mdi mdi-tab-unselected"></i>
                                                <span class="hide-menu"> Dual Listbox</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="form-xditable.html" class="sidebar-link">
                                                <i class="mdi mdi-loop"></i>
                                                <span class="hide-menu"> X-editable</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="sidebar-item">
                                    <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                                        <i class="mdi mdi-receipt"></i>
                                        <span class="hide-menu">Form Layouts</span>
                                    </a>
                                    <ul aria-expanded="false" class="collapse first-level">
                                        <li class="sidebar-item">
                                            <a href="form-basic.html" class="sidebar-link">
                                                <i class="mdi mdi-vector-difference-ba"></i>
                                                <span class="hide-menu"> Basic Forms</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="form-horizontal.html" class="sidebar-link">
                                                <i class="mdi mdi-file-document-box"></i>
                                                <span class="hide-menu"> Form Horizontal</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="form-actions.html" class="sidebar-link">
                                                <i class="mdi mdi-code-greater-than"></i>
                                                <span class="hide-menu"> Form Actions</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="form-row-separator.html" class="sidebar-link">
                                                <i class="mdi mdi-code-equal"></i>
                                                <span class="hide-menu"> Row Separator</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="form-bordered.html" class="sidebar-link">
                                                <i class="mdi mdi-flip-to-front"></i>
                                                <span class="hide-menu"> Form Bordered</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="form-striped-row.html" class="sidebar-link">
                                                <i class="mdi mdi-content-duplicate"></i>
                                                <span class="hide-menu"> Striped Rows</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="form-detail.html" class="sidebar-link">
                                                <i class="mdi mdi-cards-outline"></i>
                                                <span class="hide-menu"> Form Detail</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="form-material.html" class="sidebar-link">
                                                <i class="mdi mdi-content-duplicate"></i>
                                                <span class="hide-menu"> Form Material</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="form-float-input.html" class="sidebar-link">
                                                <i class="mdi mdi-logout"></i>
                                                <span class="hide-menu"> Form Float Input</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="sidebar-item">
                                    <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                                        <i class="mdi mdi-code-equal"></i>
                                        <span class="hide-menu">Form Addons</span>
                                    </a>
                                    <ul aria-expanded="false" class="collapse first-level">
                                        <li class="sidebar-item">
                                            <a href="form-paginator.html" class="sidebar-link">
                                                <i class="mdi mdi-export"></i>
                                                <span class="hide-menu"> Paginator</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="form-img-cropper.html" class="sidebar-link">
                                                <i class="mdi mdi-crop"></i>
                                                <span class="hide-menu"> Image Cropper</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="form-dropzone.html" class="sidebar-link">
                                                <i class="mdi mdi-crosshairs-gps"></i>
                                                <span class="hide-menu"> Dropzone</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="form-mask.html" class="sidebar-link">
                                                <i class="mdi mdi-box-shadow"></i>
                                                <span class="hide-menu"> Form Mask</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="form-typeahead.html" class="sidebar-link">
                                                <i class="mdi mdi-cards-variant"></i>
                                                <span class="hide-menu"> Form Typehead</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="sidebar-item">
                                    <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                                        <i class="mdi mdi-alert-box"></i>
                                        <span class="hide-menu">Form Validation</span>
                                    </a>
                                    <ul aria-expanded="false" class="collapse first-level">
                                        <li class="sidebar-item">
                                            <a href="form-bootstrap-validation.html" class="sidebar-link">
                                                <i class="mdi mdi-credit-card-scan"></i>
                                                <span class="hide-menu"> Bootstrap Validation</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="form-custom-validation.html" class="sidebar-link">
                                                <i class="mdi mdi-credit-card-plus"></i>
                                                <span class="hide-menu"> Custom Validation</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="sidebar-item">
                                    <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                                        <i class="mdi mdi-pencil-box-outline"></i>
                                        <span class="hide-menu">Form Pickers</span>
                                    </a>
                                    <ul aria-expanded="false" class="collapse first-level">
                                        <li class="sidebar-item">
                                            <a href="form-picker-colorpicker.html" class="sidebar-link">
                                                <i class="mdi mdi-calendar-plus"></i>
                                                <span class="hide-menu"> Form Colorpicker</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="form-picker-datetimepicker.html" class="sidebar-link">
                                                <i class="mdi mdi-calendar-clock"></i>
                                                <span class="hide-menu"> Form Datetimepicker</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="form-picker-bootstrap-rangepicker.html" class="sidebar-link">
                                                <i class="mdi mdi-calendar-range"></i>
                                                <span class="hide-menu"> Form Bootstrap Rangepicker</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="form-picker-bootstrap-datepicker.html" class="sidebar-link">
                                                <i class="mdi mdi-calendar-check"></i>
                                                <span class="hide-menu"> Form Bootstrap Datepicker</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="form-picker-material-datepicker.html" class="sidebar-link">
                                                <i class="mdi mdi-calendar-text"></i>
                                                <span class="hide-menu"> Form Material Datepicker</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="sidebar-item">
                                    <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                                        <i class="mdi mdi-dns"></i>
                                        <span class="hide-menu">Form Editor</span>
                                    </a>
                                    <ul aria-expanded="false" class="collapse first-level">
                                        <li class="sidebar-item">
                                            <a href="form-editor-ckeditor.html" class="sidebar-link">
                                                <i class="mdi mdi-drawing"></i>
                                                <span class="hide-menu">Ck Editor</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="form-editor-quill.html" class="sidebar-link">
                                                <i class="mdi mdi-drupal"></i>
                                                <span class="hide-menu">Quill Editor</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="form-editor-summernote.html" class="sidebar-link">
                                                <i class="mdi mdi-brightness-6"></i>
                                                <span class="hide-menu">Summernote Editor</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="form-editor-tinymce.html" class="sidebar-link">
                                                <i class="mdi mdi-bowling"></i>
                                                <span class="hide-menu">Tinymce Edtor</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="sidebar-item">
                                    <a href="form-wizard.html" class="sidebar-link">
                                        <i class="mdi mdi-cube-send"></i>
                                        <span class="hide-menu">Form Wizard</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="form-repeater.html" class="sidebar-link">
                                        <i class="mdi mdi-creation"></i>
                                        <span class="hide-menu">Form Repeater</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                                <i class="mdi mdi-table"></i>
                                <span class="hide-menu">Tables</span>
                                <span class="badge badge-danger text-white badge-pill ml-auto mr-3 font-medium px-2 py-1">11</span>
                            </a>
                            <ul aria-expanded="false" class="collapse first-level">
                                <li class="sidebar-item">
                                    <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                                        <i class="mdi mdi-border-none"></i>
                                        <span class="hide-menu">Bootstrap Tables</span>
                                    </a>
                                    <ul aria-expanded="false" class="collapse first-level">
                                        <li class="sidebar-item">
                                            <a href="table-basic.html" class="sidebar-link">
                                                <i class="mdi mdi-border-all"></i>
                                                <span class="hide-menu">Basic Table </span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="table-dark-basic.html" class="sidebar-link">
                                                <i class="mdi mdi-border-left"></i>
                                                <span class="hide-menu">Dark Basic Table </span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="table-sizing.html" class="sidebar-link">
                                                <i class="mdi mdi-border-outside"></i>
                                                <span class="hide-menu">Sizing Table </span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="table-layout-coloured.html" class="sidebar-link">
                                                <i class="mdi mdi-border-bottom"></i>
                                                <span class="hide-menu">Coloured Table Layout</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="sidebar-item">
                                    <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                                        <i class="mdi mdi-border-inside"></i>
                                        <span class="hide-menu">Datatables</span>
                                    </a>
                                    <ul aria-expanded="false" class="collapse first-level">
                                        <li class="sidebar-item">
                                            <a href="table-datatable-basic.html" class="sidebar-link">
                                                <i class="mdi mdi-border-vertical"></i>
                                                <span class="hide-menu"> Basic Initialisation</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="table-datatable-api.html" class="sidebar-link">
                                                <i class="mdi mdi-blur-linear"></i>
                                                <span class="hide-menu"> API</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="table-datatable-advanced.html" class="sidebar-link">
                                                <i class="mdi mdi-border-style"></i>
                                                <span class="hide-menu"> Advanced Initialisation</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="sidebar-item">
                                    <a href="table-bootstrap.html" class="sidebar-link">
                                        <i class="mdi mdi-border-horizontal"></i>
                                        <span class="hide-menu">Table Bootstrap</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="table-jsgrid.html" class="sidebar-link">
                                        <i class="mdi mdi-border-top"></i>
                                        <span class="hide-menu">Table Jsgrid</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="table-responsive.html" class="sidebar-link">
                                        <i class="mdi mdi-border-style"></i>
                                        <span class="hide-menu">Table Responsive</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="table-footable.html" class="sidebar-link">
                                        <i class="mdi mdi-tab-unselected"></i>
                                        <span class="hide-menu">Table Footable</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                                <i class="mdi mdi-chart-bar"></i>
                                <span class="hide-menu">Charts</span>
                            </a>
                            <ul aria-expanded="false" class="collapse first-level">
                                <li class="sidebar-item">
                                    <a href="chart-morris.html" class="sidebar-link">
                                        <i class="mdi mdi-image-filter-tilt-shift"></i>
                                        <span class="hide-menu">Morris Chart</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="chart-chart-js.html" class="sidebar-link">
                                        <i class="mdi mdi-svg"></i>
                                        <span class="hide-menu">Chartjs</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="chart-sparkline.html" class="sidebar-link">
                                        <i class="mdi mdi-chart-histogram"></i>
                                        <span class="hide-menu">Sparkline Chart</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="chart-chartist.html" class="sidebar-link">
                                        <i class="mdi mdi-blur"></i>
                                        <span class="hide-menu">Chartist Chart</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                                        <i class="mdi mdi-chemical-weapon"></i>
                                        <span class="hide-menu">C3 Charts</span>
                                    </a>
                                    <ul aria-expanded="false" class="collapse first-level">
                                        <li class="sidebar-item">
                                            <a href="chart-c3-axis.html" class="sidebar-link">
                                                <i class="mdi mdi-arrange-bring-to-front"></i>
                                                <span class="hide-menu">Axis Chart</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="chart-c3-bar.html" class="sidebar-link">
                                                <i class="mdi mdi-arrange-send-to-back"></i>
                                                <span class="hide-menu">Bar Chart</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="chart-c3-data.html" class="sidebar-link">
                                                <i class="mdi mdi-backup-restore"></i>
                                                <span class="hide-menu">Data Chart</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="chart-c3-line.html" class="sidebar-link">
                                                <i class="mdi mdi-backburger"></i>
                                                <span class="hide-menu">Line Chart</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="sidebar-item">
                                    <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                                        <i class="mdi mdi-chart-areaspline"></i>
                                        <span class="hide-menu">Echarts</span>
                                    </a>
                                    <ul aria-expanded="false" class="collapse first-level">
                                        <li class="sidebar-item">
                                            <a href="chart-echart-basic.html" class="sidebar-link">
                                                <i class="mdi mdi-chart-line"></i>
                                                <span class="hide-menu">Basic Charts</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="chart-echart-bar.html" class="sidebar-link">
                                                <i class="mdi mdi-chart-scatterplot-hexbin"></i>
                                                <span class="hide-menu">Bar Chart</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="chart-echart-pie-doughnut.html" class="sidebar-link">
                                                <i class="mdi mdi-chart-pie"></i>
                                                <span class="hide-menu">Pie &amp; Doughnut Chart</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <div class="devider"></div>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                                <i class="mdi mdi-credit-card-multiple"></i>
                                <span class="hide-menu">Cards</span>
                            </a>
                            <ul aria-expanded="false" class="collapse  first-level">
                                <li class="sidebar-item">
                                    <a href="ui-cards.html" class="sidebar-link">
                                        <i class="mdi mdi-layers"></i>
                                        <span class="hide-menu"> Basic Cards</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="ui-card-customs.html" class="sidebar-link">
                                        <i class="mdi mdi-credit-card-scan"></i>
                                        <span class="hide-menu">Custom Cards</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="ui-card-weather.html" class="sidebar-link">
                                        <i class="mdi mdi-weather-fog"></i>
                                        <span class="hide-menu">Weather Cards</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="ui-card-draggable.html" class="sidebar-link">
                                        <i class="mdi mdi-bandcamp"></i>
                                        <span class="hide-menu">Draggable Cards</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                                <i class="mdi mdi-credit-card-multiple"></i>
                                <span class="hide-menu">Components</span>
                            </a>
                            <ul aria-expanded="false" class="collapse  first-level">
                                <li class="sidebar-item">
                                    <a href="component-sweetalert.html" class="sidebar-link">
                                        <i class="mdi mdi-layers"></i>
                                        <span class="hide-menu"> Sweet Alert</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="component-nestable.html" class="sidebar-link">
                                        <i class="mdi mdi-credit-card-scan"></i>
                                        <span class="hide-menu">Nestable</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="component-noui-slider.html" class="sidebar-link">
                                        <i class="mdi mdi-weather-fog"></i>
                                        <span class="hide-menu">Noui slider</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="component-rating.html" class="sidebar-link">
                                        <i class="mdi mdi-bandcamp"></i>
                                        <span class="hide-menu">Rating</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="component-toastr.html" class="sidebar-link">
                                        <i class="mdi mdi-poll"></i>
                                        <span class="hide-menu">Toastr</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <div class="devider"></div>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                                <i class="mdi mdi-settings"></i>
                                <span class="hide-menu">Widgets </span>
                            </a>
                            <ul aria-expanded="false" class="collapse  first-level">
                                <li class="sidebar-item">
                                    <a href="widgets-apps.html" class="sidebar-link">
                                        <i class="mdi mdi-comment-processing-outline"></i>
                                        <span class="hide-menu"> Apps Widgets </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="widgets-data.html" class="sidebar-link">
                                        <i class="mdi mdi-calendar"></i>
                                        <span class="hide-menu"> Data Widgets </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="widgets-charts.html" class="sidebar-link">
                                        <i class="mdi mdi-bulletin-board"></i>
                                        <span class="hide-menu"> Charts Widgets</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                                <i class="mdi mdi-face"></i>
                                <span class="hide-menu">Icons</span>
                            </a>
                            <ul aria-expanded="false" class="collapse first-level">
                                <li class="sidebar-item">
                                    <a href="icon-material.html" class="sidebar-link">
                                        <i class="mdi mdi-emoticon"></i>
                                        <span class="hide-menu"> Material Icons </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="icon-fontawesome.html" class="sidebar-link">
                                        <i class="mdi mdi-emoticon-cool"></i>
                                        <span class="hide-menu"> Fontawesome Icons</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="icon-themify.html" class="sidebar-link">
                                        <i class="mdi mdi-chart-bubble"></i>
                                        <span class="hide-menu"> Themify Icons</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="icon-weather.html" class="sidebar-link">
                                        <i class="mdi mdi-weather-cloudy"></i>
                                        <span class="hide-menu"> Weather Icons</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="icon-simple-lineicon.html" class="sidebar-link">
                                        <i class="mdi mdi mdi-image-broken-variant"></i>
                                        <span class="hide-menu"> Simple Line icons</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="icon-flag.html" class="sidebar-link">
                                        <i class="mdi mdi-flag-triangle"></i>
                                        <span class="hide-menu"> Flag Icons</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="map-google.html" aria-expanded="false">
                                <i class="mdi mdi-google-maps"></i>
                                <span class="hide-menu">Google Map</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="map-vector.html" aria-expanded="false">
                                <i class="mdi mdi-map-marker-radius"></i>
                                <span class="hide-menu">Vector Map</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                                <i class="mdi mdi-account-multiple"></i>
                                <span class="hide-menu">Users</span>
                            </a>
                            <ul aria-expanded="false" class="collapse first-level">
                                <li class="sidebar-item">
                                    <a href="ui-user-card.html" class="sidebar-link">
                                        <i class="mdi mdi-account-box"></i>
                                        <span class="hide-menu"> User Card </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="pages-profile.html" class="sidebar-link">
                                        <i class="mdi mdi-account-network"></i>
                                        <span class="hide-menu"> User Profile</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="ui-user-contacts.html" class="sidebar-link">
                                        <i class="mdi mdi-account-star-variant"></i>
                                        <span class="hide-menu"> User Contact</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                                <i class="mdi mdi-ungroup"></i>
                                <span class="hide-menu">Invoice</span>
                            </a>
                            <ul aria-expanded="false" class="collapse first-level">
                                <li class="sidebar-item">
                                    <a href="pages-invoice.html" class="sidebar-link">
                                        <i class="mdi mdi-vector-triangle"></i>
                                        <span class="hide-menu"> Invoice Layout </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="pages-invoice-list.html" class="sidebar-link">
                                        <i class="mdi mdi-vector-rectangle"></i>
                                        <span class="hide-menu"> Invoice List</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                                <i class="mdi mdi-apple-safari"></i>
                                <span class="hide-menu">Timeline</span>
                            </a>
                            <ul aria-expanded="false" class="collapse first-level">
                                <li class="sidebar-item">
                                    <a href="timeline-center.html" class="sidebar-link">
                                        <i class="mdi mdi-clock-fast"></i>
                                        <span class="hide-menu"> Center Timeline </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="timeline-horizontal.html" class="sidebar-link">
                                        <i class="mdi mdi-clock-end"></i>
                                        <span class="hide-menu"> Horizontal Timeline</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="timeline-left.html" class="sidebar-link">
                                        <i class="mdi mdi-clock-in"></i>
                                        <span class="hide-menu"> Left Timeline</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="timeline-right.html" class="sidebar-link">
                                        <i class="mdi mdi-clock-start"></i>
                                        <span class="hide-menu"> Right Timeline</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="app-calendar.html" aria-expanded="false">
                                <i class="mdi mdi-calendar-check"></i>
                                <span class="hide-menu">Calendar</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                                <i class="mdi mdi-notification-clear-all"></i>
                                <span class="hide-menu">Multi level dd</span>
                            </a>
                            <ul aria-expanded="false" class="collapse first-level">
                                <li class="sidebar-item">
                                    <a href="javascript:void(0)" class="sidebar-link">
                                        <i class="mdi mdi-octagram"></i>
                                        <span class="hide-menu"> item 1.1</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="javascript:void(0)" class="sidebar-link">
                                        <i class="mdi mdi-octagram"></i>
                                        <span class="hide-menu"> item 1.2</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a class="has-arrow sidebar-link" href="javascript:void(0)" aria-expanded="false">
                                        <i class="mdi mdi-playlist-plus"></i>
                                        <span class="hide-menu">Menu 1.3</span>
                                    </a>
                                    <ul aria-expanded="false" class="collapse second-level">
                                        <li class="sidebar-item">
                                            <a href="javascript:void(0)" class="sidebar-link">
                                                <i class="mdi mdi-octagram"></i>
                                                <span class="hide-menu"> item 1.3.1</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="javascript:void(0)" class="sidebar-link">
                                                <i class="mdi mdi-octagram"></i>
                                                <span class="hide-menu"> item 1.3.2</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="javascript:void(0)" class="sidebar-link">
                                                <i class="mdi mdi-octagram"></i>
                                                <span class="hide-menu"> item 1.3.3</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="javascript:void(0)" class="sidebar-link">
                                                <i class="mdi mdi-octagram"></i>
                                                <span class="hide-menu"> item 1.3.4</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="sidebar-item">
                                    <a href="javascript:void(0)" class="sidebar-link">
                                        <i class="mdi mdi-playlist-check"></i>
                                        <span class="hide-menu"> item 1.4</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <div class="devider"></div>
                        <li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="https://wrappixel.com/ampleadmin/docs/documentation.html" aria-expanded="false">
                                <i class="mdi mdi-adjust text-danger"></i>
                                <span class="hide-menu">Documentation</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="authentication-login1.html" aria-expanded="false">
                                <i class="mdi mdi-adjust text-info"></i>
                                <span class="hide-menu">Log Out</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="pages-faq.html" aria-expanded="false">
                                <i class="mdi mdi-adjust text-success"></i>
                                <span class="hide-menu">FAQs</span>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>
        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
    @yield('content')
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- customizer Panel -->
    <!-- ============================================================== -->
    <aside class="customizer">
        <a href="javascript:void(0)" class="service-panel-toggle">
            <i class="fa fa-spin fa-cog"></i>
        </a>
        <div class="customizer-body">
            <ul class="nav customizer-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">
                        <i class="mdi mdi-wrench font-20"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#chat" role="tab" aria-controls="chat" aria-selected="false">
                        <i class="mdi mdi-message-reply font-20"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">
                        <i class="mdi mdi-star-circle font-20"></i>
                    </a>
                </li>
            </ul>
            <div class="tab-content" id="pills-tabContent">
                <!-- Tab 1 -->
                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                    <div class="p-3 border-bottom">
                        <!-- Sidebar -->
                        <h5 class="font-medium mb-2 mt-2">Layout Settings</h5>

                        <div class="custom-control custom-checkbox mt-2">
                            <input type="checkbox" class="custom-control-input sidebartoggler" name="collapssidebar" id="collapssidebar">
                            <label class="custom-control-label" for="collapssidebar">Collapse Sidebar</label>
                        </div>
                        <div class="custom-control custom-checkbox mt-2">
                            <input type="checkbox" class="custom-control-input" name="sidebar-position" id="sidebar-position">
                            <label class="custom-control-label" for="sidebar-position">Fixed Sidebar</label>
                        </div>
                        <div class="custom-control custom-checkbox mt-2">
                            <input type="checkbox" class="custom-control-input" name="header-position" id="header-position">
                            <label class="custom-control-label" for="header-position">Fixed Header</label>
                        </div>
                        <div class="custom-control custom-checkbox mt-2">
                            <input type="checkbox" class="custom-control-input" name="boxed-layout" id="boxed-layout">
                            <label class="custom-control-label" for="boxed-layout">Boxed Layout</label>
                        </div>
                    </div>
                    <div class="p-3 border-bottom">
                        <!-- Logo BG -->
                        <h5 class="font-medium mb-2 mt-2">Logo Backgrounds</h5>
                        <ul class="theme-color">
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-logobg="skin1"></a>
                            </li>
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-logobg="skin2"></a>
                            </li>
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-logobg="skin3"></a>
                            </li>
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-logobg="skin4"></a>
                            </li>
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-logobg="skin5"></a>
                            </li>
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-logobg="skin6"></a>
                            </li>
                        </ul>
                        <!-- Logo BG -->
                    </div>
                    <div class="p-3 border-bottom">
                        <!-- Navbar BG -->
                        <h5 class="font-medium mb-2 mt-2">Navbar Backgrounds</h5>
                        <ul class="theme-color">
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-navbarbg="skin1"></a>
                            </li>
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-navbarbg="skin2"></a>
                            </li>
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-navbarbg="skin3"></a>
                            </li>
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-navbarbg="skin4"></a>
                            </li>
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-navbarbg="skin5"></a>
                            </li>
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-navbarbg="skin6"></a>
                            </li>
                        </ul>
                        <!-- Navbar BG -->
                    </div>
                    <div class="p-3 border-bottom">
                        <!-- Logo BG -->
                        <h5 class="font-medium mb-2 mt-2">Sidebar Backgrounds</h5>
                        <ul class="theme-color">
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-sidebarbg="skin1"></a>
                            </li>
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-sidebarbg="skin2"></a>
                            </li>
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-sidebarbg="skin3"></a>
                            </li>
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-sidebarbg="skin4"></a>
                            </li>
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-sidebarbg="skin5"></a>
                            </li>
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-sidebarbg="skin6"></a>
                            </li>
                        </ul>
                        <!-- Logo BG -->
                    </div>
                </div>
                <!-- End Tab 1 -->
                <!-- Tab 2 -->
                <div class="tab-pane fade" id="chat" role="tabpanel" aria-labelledby="pills-profile-tab">
                    <ul class="mailbox list-style-none mt-3">
                        <li>
                            <div class="message-center chat-scroll">
                                <a href="javascript:void(0)" class="message-item" id='chat_user_1' data-user-id='1'>
                                    <span class="user-img">
                                        <img src="../../assets/images/users/1.jpg" alt="user" class="rounded-circle">
                                        <span class="profile-status online pull-right"></span>
                                    </span>
                                    <div class="mail-contnet">
                                        <h5 class="message-title">Pavan kumar</h5>
                                        <span class="mail-desc">Just see the my admin!</span>
                                        <span class="time">9:30 AM</span>
                                    </div>
                                </a>
                                <!-- Message -->
                                <a href="javascript:void(0)" class="message-item" id='chat_user_2' data-user-id='2'>
                                    <span class="user-img">
                                        <img src="../../assets/images/users/2.jpg" alt="user" class="rounded-circle">
                                        <span class="profile-status busy pull-right"></span>
                                    </span>
                                    <div class="mail-contnet">
                                        <h5 class="message-title">Sonu Nigam</h5>
                                        <span class="mail-desc">I've sung a song! See you at</span>
                                        <span class="time">9:10 AM</span>
                                    </div>
                                </a>
                                <!-- Message -->
                                <a href="javascript:void(0)" class="message-item" id='chat_user_3' data-user-id='3'>
                                    <span class="user-img">
                                        <img src="../../assets/images/users/3.jpg" alt="user" class="rounded-circle">
                                        <span class="profile-status away pull-right"></span>
                                    </span>
                                    <div class="mail-contnet">
                                        <h5 class="message-title">Arijit Sinh</h5>
                                        <span class="mail-desc">I am a singer!</span>
                                        <span class="time">9:08 AM</span>
                                    </div>
                                </a>
                                <!-- Message -->
                                <a href="javascript:void(0)" class="message-item" id='chat_user_4' data-user-id='4'>
                                    <span class="user-img">
                                        <img src="../../assets/images/users/4.jpg" alt="user" class="rounded-circle">
                                        <span class="profile-status offline pull-right"></span>
                                    </span>
                                    <div class="mail-contnet">
                                        <h5 class="message-title">Nirav Joshi</h5>
                                        <span class="mail-desc">Just see the my admin!</span>
                                        <span class="time">9:02 AM</span>
                                    </div>
                                </a>
                                <!-- Message -->
                                <!-- Message -->
                                <a href="javascript:void(0)" class="message-item" id='chat_user_5' data-user-id='5'>
                                    <span class="user-img">
                                        <img src="../../assets/images/users/5.jpg" alt="user" class="rounded-circle">
                                        <span class="profile-status offline pull-right"></span>
                                    </span>
                                    <div class="mail-contnet">
                                        <h5 class="message-title">Sunil Joshi</h5>
                                        <span class="mail-desc">Just see the my admin!</span>
                                        <span class="time">9:02 AM</span>
                                    </div>
                                </a>
                                <!-- Message -->
                                <!-- Message -->
                                <a href="javascript:void(0)" class="message-item" id='chat_user_6' data-user-id='6'>
                                    <span class="user-img">
                                        <img src="../../assets/images/users/6.jpg" alt="user" class="rounded-circle">
                                        <span class="profile-status offline pull-right"></span>
                                    </span>
                                    <div class="mail-contnet">
                                        <h5 class="message-title">Akshay Kumar</h5>
                                        <span class="mail-desc">Just see the my admin!</span>
                                        <span class="time">9:02 AM</span>
                                    </div>
                                </a>
                                <!-- Message -->
                                <!-- Message -->
                                <a href="javascript:void(0)" class="message-item" id='chat_user_7' data-user-id='7'>
                                    <span class="user-img">
                                        <img src="../../assets/images/users/7.jpg" alt="user" class="rounded-circle">
                                        <span class="profile-status offline pull-right"></span>
                                    </span>
                                    <div class="mail-contnet">
                                        <h5 class="message-title">Pavan kumar</h5>
                                        <span class="mail-desc">Just see the my admin!</span>
                                        <span class="time">9:02 AM</span>
                                    </div>
                                </a>
                                <!-- Message -->
                                <!-- Message -->
                                <a href="javascript:void(0)" class="message-item" id='chat_user_8' data-user-id='8'>
                                    <span class="user-img">
                                        <img src="../../assets/images/users/8.jpg" alt="user" class="rounded-circle">
                                        <span class="profile-status offline pull-right"></span>
                                    </span>
                                    <div class="mail-contnet">
                                        <h5 class="message-title">Varun Dhavan</h5>
                                        <span class="mail-desc">Just see the my admin!</span>
                                        <span class="time">9:02 AM</span>
                                    </div>
                                </a>
                                <!-- Message -->
                            </div>
                        </li>
                    </ul>
                </div>
                <!-- End Tab 2 -->
                <!-- Tab 3 -->
                <div class="tab-pane fade p-3" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                    <h6 class="mt-3 mb-3">Activity Timeline</h6>
                    <div class="steamline">
                        <div class="sl-item">
                            <div class="sl-left bg-success">
                                <i class="ti-user"></i>
                            </div>
                            <div class="sl-right">
                                <div class="font-medium">Meeting today
                                    <span class="sl-date"> 5pm</span>
                                </div>
                                <div class="desc">you can write anything </div>
                            </div>
                        </div>
                        <div class="sl-item">
                            <div class="sl-left bg-info">
                                <i class="fas fa-image"></i>
                            </div>
                            <div class="sl-right">
                                <div class="font-medium">Send documents to Clark</div>
                                <div class="desc">Lorem Ipsum is simply </div>
                            </div>
                        </div>
                        <div class="sl-item">
                            <div class="sl-left">
                                <img class="rounded-circle" alt="user" src="../../assets/images/users/2.jpg"> </div>
                            <div class="sl-right">
                                <div class="font-medium">Go to the Doctor
                                    <span class="sl-date">5 minutes ago</span>
                                </div>
                                <div class="desc">Contrary to popular belief</div>
                            </div>
                        </div>
                        <div class="sl-item">
                            <div class="sl-left">
                                <img class="rounded-circle" alt="user" src="../../assets/images/users/1.jpg"> </div>
                            <div class="sl-right">
                                <div>
                                    <a href="javascript:void(0)">Stephen</a>
                                    <span class="sl-date">5 minutes ago</span>
                                </div>
                                <div class="desc">Approve meeting with tiger</div>
                            </div>
                        </div>
                        <div class="sl-item">
                            <div class="sl-left bg-primary">
                                <i class="ti-user"></i>
                            </div>
                            <div class="sl-right">
                                <div class="font-medium">Meeting today
                                    <span class="sl-date"> 5pm</span>
                                </div>
                                <div class="desc">you can write anything </div>
                            </div>
                        </div>
                        <div class="sl-item">
                            <div class="sl-left bg-info">
                                <i class="fas fa-image"></i>
                            </div>
                            <div class="sl-right">
                                <div class="font-medium">Send documents to Clark</div>
                                <div class="desc">Lorem Ipsum is simply </div>
                            </div>
                        </div>
                        <div class="sl-item">
                            <div class="sl-left">
                                <img class="rounded-circle" alt="user" src="../../assets/images/users/4.jpg"> </div>
                            <div class="sl-right">
                                <div class="font-medium">Go to the Doctor
                                    <span class="sl-date">5 minutes ago</span>
                                </div>
                                <div class="desc">Contrary to popular belief</div>
                            </div>
                        </div>
                        <div class="sl-item">
                            <div class="sl-left">
                                <img class="rounded-circle" alt="user" src="../../assets/images/users/6.jpg"> </div>
                            <div class="sl-right">
                                <div>
                                    <a href="javascript:void(0)">Stephen</a>
                                    <span class="sl-date">5 minutes ago</span>
                                </div>
                                <div class="desc">Approve meeting with tiger</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Tab 3 -->
            </div>
        </div>
    </aside>
    <div class="chat-windows"></div>
    @include('include.footer')
   <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
   
