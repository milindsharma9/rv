@extends('admin.layouts.master')
@section('content')
<h1>{{ trans('admin/contact.manage_contact') }}</h1>
<div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">{{ trans('admin/contact.list') }}</div>
        </div>
        <div class="portlet-body">
            <table class="table table-striped table-hover table-responsive datatable" id="datatable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Message</th>
                                    <th>Feedback</th>
                                    <th>Operations </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contact as $row)
                                <tr>
                                    <td>{{ $row->name }}</td>
                                    <td>{{ $row->email }}</td>
                                    @php $formattedMessage = $row->message; @endphp
                                    @if (strlen($formattedMessage) > 100) 
                                        @php $formattedMessage = substr($formattedMessage, 0, 100); @endphp
                                        @php $formattedMessage = $formattedMessage . "..."; @endphp
                                    @endif
                                    <td>{{ $formattedMessage }}</td>
                                    @php $formattedFeedback = $row->feedback; @endphp
                                    @if (strlen($formattedFeedback) > 100) 
                                        @php $formattedFeedback = substr($formattedFeedback, 0, 100); @endphp
                                        @php $formattedFeedback = $formattedFeedback . "..."; @endphp
                                    @endif
                                    <td>{{ $formattedFeedback }}</td>
                                    <td>
                                        {!! link_to_route('admin.contact.edit', trans('admin/contact.edit'), array($row->id), array('class' => 'btn btn-xs btn-info')) !!}
                                    </td>
                                </tr>
                                 @endforeach
                            </tbody>
            </table>
        </div>
</div>
@endsection