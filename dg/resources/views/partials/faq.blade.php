@php
    $returnUrl = route('customer.dashboard');
@endphp
@if(isset(Auth::user()['id']) && Auth::user()['fk_users_role'] == config('appConstants.vendor_role_id'))
    @php
        $returnUrl = route('store.profile');
    @endphp
@endif
@php
    $returnUrl = URL::previous();
@endphp
<!--<div class="order-title">
    <div class="container">
        <div class="row">
            <h3 class="title"><a href="{{$returnUrl}}" class="btn-red">&lt; Back</a><span class="title-content">F.A.Q's</span></h3>
        </div>
    </div>
</div>-->

<div class="faq-tabs">
    <ul class="nav nav-tabs tab-level-1">
        @php
            $i=0;
        @endphp
        @foreach($userGroup as $group)
            @php
                $class = '';
            @endphp
            @if($i==0)
                @php
                    $class = 'active';
                @endphp
            @endif
            @php
                $userGroupRefinedName = strtolower($group);
                $i++;
            @endphp
            @if(isset($isStore) && ($isStore))
                @php
                    $class = '';
                @endphp
                @if($userGroupRefinedName == 'retailers')
                    @php
                        $class = 'active';
                    @endphp
                @endif
            @endif
            <li class={{$class}}><a data-toggle="tab" data-intercom-event="faq_{{$userGroupRefinedName}}" href="#{{$userGroupRefinedName}}-tab"><span>{{$group}}</span></a></li>
        @endforeach
    </ul>
</div>

<div class="faq-content-tab-wrap tab-content">
    <!-- User Group Tab -->
        @php
            $i=0;
        @endphp
    @foreach($aFaqList as $faqUserGroup)
        @php
            $class = '';
        @endphp
        @if($i==0)
            @php
                $class = 'in active';
            @endphp
        @endif
        @php
            $userGroupRefinedName = strtolower($faqUserGroup['name']);
            $i++;
        @endphp
        @if(isset($isStore) && ($isStore))
            @php
                $class = '';
            @endphp
            @if($userGroupRefinedName == 'retailers')
                @php
                    $class = 'in active';
                @endphp
            @endif
        @endif
        <div id="{{$userGroupRefinedName}}-tab" class="tab-pane fade {{$class}}">
            <!-- Category Group -->
            @foreach($faqUserGroup['category'] as $faqCategory)
                <div class="other-stuff">
                    <div class="container">
                        <div class="row">
                            <h3 class="title"><span class="title-content">{{$faqCategory['name']}}</span></h3>
                            <ul>
                                <!-- Faq -->
                                @foreach($faqCategory['faq'] as $faq)
                                    <li>
                                        <a class="accr-title">{{$faq['title']}}</a>
                                        <div class="accr-content">
                                            <?php echo $faq['description']; ?>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</div>