@@component('mail::message')
#
Dear {{$email->email}},
<div>Mã vé của bạn là : {{$departure->name}}<br></div>
<div>Người đặt : {{$email->customers_name}}<br></div>
<div>Bắt đầu : {{$departure->go_location_city}}, {{$departure->go_location_district}}, {{$departure->go_location_wards}}<br></div>
<div>Điểm đến :  {{$departure->come_location_city}}, {{$departure->come_location_district}}, {{$departure->come_location_wards}}<br></div>
<div>Thời gian :  {{$departure->start_time}}<br></div>
<div>Giá : {{$email->price}}<br></div>
<div>Hình thức thanh toán : {{$email->paymentMethod}}<br></div>
<div>Số ghế : {{$email->quantity}}<br></div>
@component('mail::button', ['url' => 'đường dẫn fe'])
Link
@endcomponent
Thanks,<br>
{{ config('app.name') }}
@endcomponent