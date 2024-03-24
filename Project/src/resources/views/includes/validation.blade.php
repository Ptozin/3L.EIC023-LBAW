@if(session()->has('success'))
    <div>{{session()->get('success')}}</div>
@endif

@if(session()->has('danger'))
    <div><strong>{{session()->get('danger')}}</strong></div>
@endif