<div class="page-sidebar-wrapper">
    <div class="page-sidebar navbar-collapse collapse">
        <ul class="page-sidebar-menu"
            data-keep-expanded="false"
            data-auto-scroll="true"
            data-slide-speed="200">
			  <li @if(isset(explode('/',Request::path())[1]) && explode('/',Request::path())[1] == strtolower('users')) class="active" @endif>
                 <a href="{{ route('admin.users.index') }}">
                    <i class="fa fa-sign-out fa-fw"></i>
                    <span class="title">{{ trans('Users') }}</span>
                </a>
            </li>
			
			 <li @if(isset(explode('/',Request::path())[1]) && explode('/',Request::path())[1] == strtolower('manuals')) class="active" @endif>
                 <a href="{{ route('admin.manuals.index') }}">
                    <i class="fa fa-sign-out fa-fw"></i>
                    <span class="title">{{ trans('Manuals') }}</span>
                </a>
            </li>
			
			 <li @if(isset(explode('/',Request::path())[1]) && explode('/',Request::path())[1] == strtolower('clients')) class="active" @endif>
                 <a href="{{ route('admin.clients.index') }}">
                    <i class="fa fa-sign-out fa-fw"></i>
                    <span class="title">{{ trans('Clients') }}</span>
                </a>
            </li>
			
			            <li @if(isset(explode('/',Request::path())[1]) && explode('/',Request::path())[1] == strtolower('changePassword')) class="active" @endif>
                 <a href="{{ route('admin.changePassword') }}">
                    <i class="fa fa-sign-out fa-fw"></i>
                    <span class="title">{{ trans('Change Password') }}</span>
                </a>
            </li>
			<li > 
                <a href="{{ url('logout') }}">
                    <i class="fa fa-sign-out fa-fw"></i>
                    <span class="title">{{ trans('admin/admin.partials-sidebar-logout') }}</span>
                </a>
            </li>
			
			
            <!--li @if(isset(explode('/',Request::path())[1]) && explode('/',Request::path())[1] == strtolower('events')) class="active" @endif>
                <a href="{{ route('admin.events.index') }}">
                    <i class="fa fa-sign-out fa-fw"></i>
                    <span class="title">{{ trans('admin/events.events_label') }}</span>
                </a>
            </li>
            <li @if(isset(explode('/',Request::path())[1]) && explode('/',Request::path())[1] == strtolower('Occasions')) class="active" @endif>
                <a href="{{ route('admin.occasions.index') }}">
                    <i class="fa fa-sign-out fa-fw"></i>
                    <span class="title">{{ trans('Occasions') }}</span>
                </a>
            </li>
            <li @if(isset(explode('/',Request::path())[1]) && explode('/',Request::path())[1] == strtolower('Categories')) class="active" @endif>
                <a href="{{ route('admin.categories.index') }}">
                    <i class="fa fa-sign-out fa-fw"></i>
                    <span class="title">{{ trans('Categories') }}</span>
                </a>
            </li>
            <li @if(isset(explode('/',Request::path())[1]) && explode('/',Request::path())[1] == strtolower('banners')) class="active" @endif>
                <a href="{{ route('admin.banners.list') }}">
                    <i class="fa fa-sign-out fa-fw"></i>
                    <span class="title">{{ trans('Manage Banners') }}</span>
                </a>
            </li>
            <li @if(isset(explode('/',Request::path())[1]) && explode('/',Request::path())[1] == strtolower('Products')) class="active" @endif>
                <a href="{{ route('admin.products.index') }}">
                    <i class="fa fa-sign-out fa-fw"></i>
                    <span class="title">{{ trans('Products') }}</span>
                </a>
            </li>
            <li @if(isset(explode('/',Request::path())[1]) && explode('/',Request::path())[1] == strtolower('Bundles')) class="active" @endif>
                <a href="{{ route('admin.bundles.index') }}">
                    <i class="fa fa-sign-out fa-fw"></i>
                    <span class="title">{{ trans('Bundles') }}</span>
                </a>
            </li>
          
            <li @if(isset(explode('/',Request::path())[1]) && explode('/',Request::path())[1] == strtolower('Vendors')) class="active" @endif>
                <a href="{{ route('admin.vendors.index') }}">
                    <i class="fa fa-sign-out fa-fw"></i>
                    <span class="title">{{ trans('Vendors') }}</span>
                </a>
            </li>
            <li @if(isset(explode('/',Request::path())[1]) && explode('/',Request::path())[1] == strtolower('Drivers')) class="active" @endif>
                <a href="{{ route('admin.drivers.index') }}">
                    <i class="fa fa-sign-out fa-fw"></i>
                    <span class="title">{{ trans('Drivers') }}</span>
                </a>
            </li>
            <li @if(isset(explode('/',Request::path())[1]) && explode('/',Request::path())[1] == strtolower('Orders')) class="active" @endif>
                <a href="{{ route('admin.orders.index') }}">
                    <i class="fa fa-sign-out fa-fw"></i>
                    <span class="title">{{ trans('Orders') }}</span>
                </a>
            </li>
            <li @if(isset(explode('/',Request::path())[1]) && explode('/',Request::path())[1] == strtolower('postcode')) class="active" @endif>
                 <a href="{{ route('admin.postcode.index') }}">
                    <i class="fa fa-sign-out fa-fw"></i>
                    <span class="title">{{ trans('Manage Postcodes') }}</span>
                </a>
            </li>
            <li @if(isset(explode('/',Request::path())[1]) && explode('/',Request::path())[1] == strtolower('configurations')) class="active" @endif>
                 <a href="{{ route('admin.configurations.manage') }}">
                    <i class="fa fa-sign-out fa-fw"></i>
                    <span class="title">{{ trans('Manage Configurations') }}</span>
                </a>
            </li>

            <li @if(isset(explode('/',Request::path())[1]) && explode('/',Request::path())[1] == strtolower('history')) class="active" @endif>
                 <a href="{{ route('admin.payment.history') }}">
                    <i class="fa fa-sign-out fa-fw"></i>
                    <span class="title">{{ trans('Release Payment History') }}</span>
                </a>
            </li>
            <li @if(isset(explode('/',Request::path())[1]) && explode('/',Request::path())[1] == strtolower('payout')) class="active" @endif>
                 <a href="{{ route('admin.payout.summary') }}">
                    <i class="fa fa-sign-out fa-fw"></i>
                    <span class="title">{{ trans('Manage Payouts') }}</span>
                </a>
            </li>
            <li @if(isset(explode('/',Request::path())[1]) && explode('/',Request::path())[1] == strtolower('bankdetails')) class="active" @endif>
                 <a href="{{ route('admin.bank') }}">
                    <i class="fa fa-sign-out fa-fw"></i>
                    <span class="title">{{ trans('Bank Details') }}</span>
                </a>
            </li>

            <li @if(isset(explode('/',Request::path())[1]) && explode('/',Request::path())[1] == strtolower('faq')) class="active" @endif>
                 <a href="{{ route('admin.faq.index') }}">
                    <i class="fa fa-sign-out fa-fw"></i>
                    <span class="title">{{ trans('Manage Faq') }}</span>
                </a>
            </li>
            <li @if(isset(explode('/',Request::path())[1]) && explode('/',Request::path())[1] == strtolower('cms')) class="active" @endif>
                 <a href="{{ route('admin.cms.index') }}">
                    <i class="fa fa-sign-out fa-fw"></i>
                    <span class="title">{{ trans('CMS') }}</span>
                </a>
            </li>
            <li @if(isset(explode('/',Request::path())[1]) && explode('/',Request::path())[1] == strtolower('blog')) class="active" @endif>
                 <a href="{{ route('admin.blog.index') }}">
                    <i class="fa fa-sign-out fa-fw"></i>
                    <span class="title">{{ trans('Blog') }}</span>
                </a>
            </li>
            <li @if(isset(explode('/',Request::path())[1]) && explode('/',Request::path())[1] == strtolower('keyword')) class="active" @endif>
                 <a href="{{ route('admin.keyword.index') }}">
                    <i class="fa fa-sign-out fa-fw"></i>
                    <span class="title">{{ trans('Keyword') }}</span>
                </a>
            </li>
            <li @if(isset(explode('/',Request::path())[1]) && explode('/',Request::path())[1] == strtolower('locale')) class="active" @endif>
                 <a href="{{ route('admin.locale.index') }}">
                    <i class="fa fa-sign-out fa-fw"></i>
                    <span class="title">{{ trans('Locale Template') }}</span>
                </a>
            </li>
            <li @if(isset(explode('/',Request::path())[1]) && explode('/',Request::path())[1] == strtolower('brand')) class="active" @endif>
                 <a href="{{ route('admin.brand.index') }}">
                    <i class="fa fa-sign-out fa-fw"></i>
                    <span class="title">{{ trans('Brand Template') }}</span>
                </a>
            </li>
            <li @if(isset(explode('/',Request::path())[1]) && explode('/',Request::path())[1] == strtolower('contact')) class="active" @endif>
                 <a href="{{ route('admin.contact.index') }}">
                    <i class="fa fa-sign-out fa-fw"></i>
                    <span class="title">{{ trans('Contact Us') }}</span>
                </a>
            </li> -->
            
        </ul>
    </div>
</div>
