@component('mail::message')
# {{ __("Welcome :name to Insoftar",['name'=>$name])}}


{{ __("User :email",['email'=>$email])}}
{{ __("Password :password",['password'=>$password])}}


@component('mail::button',['url'=>route('home')])
    {{__("Go To Insoftar")}}
@endcomponent
@endcomponent