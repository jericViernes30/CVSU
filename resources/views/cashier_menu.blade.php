<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/webrtc-adapter/3.3.3/adapter.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.1.10/vue.min.js"></script>
    <script type="text/javascript" src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    @vite('resources/css/app.css')
    <title>Dashboard</title>
</head>
<body class="w-full h-screen bg-[#ffd962]">
    
    <div id="cash_management" class="bg-main rounded-md w-1/5 mx-auto hidden absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-50" id="scanner">
        <form action="{{route('cash_management')}}" method="POST" class="p-4 text-sm">
            @csrf
            <select name="reason" id="" class="w-full py-2 rounded-sm text-center mb-3 outline-none">
                <option value="paid_in">Paid in</option>
                <option value="paid_out">Paid out</option>
            </select>
            <div class="w-full flex flex-col mb-4">
                <label for="">Enter amount</label>
                <input type="number" name="amount" class="outline-none py-2 rounded-sm px-2 mt-1">
            </div>
            <button class="w-full py-2 rounded-sm uppercase font-medium bg-[#fefefe]">Confirm</button>
        </form>
    </div>
    {{-- Top bar --}}
    <div class="w-full flex items-center h-[8%] px-20 border-b border-bd">
        <div class="w-1/6">
            <div class="">
                <img src="{{asset('images/logo2.png')}}" alt="" class="w-1/2">
            </div>
        </div>
    </div>
    {{-- main --}}
    <div id="main" class="w-full flex h-[92%]">
        {{-- navigations --}}
        <div class="w-[5%] py-6">
            <a href="{{route('dashboard')}}" class="flex w-full flex-col items-center justify-center py-4">
                <img src="{{asset('images/home.png')}}" alt="Home Icon" class="w-1/3">
                <p class="text-xs">Home</p>
            </a>
            <a href="{{route('cashier')}}" class="flex w-full flex-col items-center justify-center py-4 bg-[#e5e5e5]">
                <img src="{{asset('images/cashier.png')}}" alt="Cashier Icon" class="w-1/3">
                <p class="text-xs">Cashier</p>
            </a>
            <a href="{{route('history')}}" class="flex w-full flex-col items-center justify-center py-4">
                <img src="{{asset('images/history.png')}}" alt="Cashier Icon" class="w-1/3">
                <p class="text-xs">History</p>
            </a>
            <a href="{{route('office.login')}}" class="flex w-full flex-col items-center justify-center py-4" target="__blank">
                <img src="{{asset('images/ranking.png')}}" alt="Cashier Icon" class="w-1/3">
                <p class="text-xs">Back Office</p>
            </a>
        </div>
        {{-- POS --}}
        <div class="w-[95%] flex py-10 bg-[#e5e5e5]">
            <div class="w-1/3 block mx-auto bg-[#fefefe] py-4">
                <div class="w-full flex gap-2 items-center justify-evenly py-2 px-4 mb-2">
                    <button onclick="openCashManagement()" class="w-1/2 py-2 border-2 border-main rounded-sm text-main">Cash management</button>
                    <form action="{{ route('end-shift') }}" method="POST" class="w-1/2">
                        @csrf
                        <button class="w-full py-2 border-2 border-main rounded-sm text-main">End shift</button>
                    </form>
                </div>
                <div class="w-full py-2 border-b">
                    <div class="px-4 flex items-center justify-between text-sm">
                        <p>Shift opened: {{$shift->cashier}}</p>
                        <p>{{$shift->created_at}}</p>
                    </div>
                </div>
                <div class="w-full border-b pb-2">
                    <p class="font-semibold text-main py-4 px-4 text-xs">Cash drawer</p>
                    <div class="w-full px-4 pb-4 flex items-center justify-between">
                        <p>Starting cash</p>
                        <p>&#8369;{{$shift->starting_cash}}</p>
                    </div>
                    <div class="w-full px-4 pb-4 flex items-center justify-between">
                        <p>Cash payment</p>
                        <p>&#8369;{{$cash}}</p>
                    </div>
                    <div class="w-full px-4 pb-4 flex items-center justify-between">
                        <p>Paid in</p>
                        <p>&#8369;{{$shift->cash_in === null ? '0.0' : $shift->cash_in . '.0' }}</p>
                    </div>
                    <div class="w-full px-4 pb-4 flex items-center justify-between">
                        <p>Paid out</p>
                        <p>&#8369;{{$shift->cash_out === null ? '0.0' : $shift->cash_out . '.0' }}</p>
                    </div>
                    <p class="font-semibold text-main py-4 px-4 text-xs">Sales summary</p>
                    <div class="w-full px-4 pb-4 flex items-center justify-between">
                        <p>Gross sales</p>
                        <p>&#8369;{{$cash}}</p>
                    </div>
                    <div class="w-full px-4 pb-4 flex items-center justify-between">
                        <p>Net sales</p>
                        <p>&#8369;{{$net_sales}}</p>
                    </div>
                    <div class="w-full px-4 flex items-center justify-between font-semibold">
                        <p>Expected cash amount</p>
                        <p>&#8369;{{($shift->closing_cash + $shift->cash_in) - $shift->cash_out}}</p>
                    </div>
                </div>
                <div>
                    
                </div>
            </div>
        </div>
    </div>
    <script>
        function setAmount(amount){
            event.preventDefault()
            var cash = document.getElementById('cash')
            cash.value = amount
        }

        function openCashManagement(){
            var cash_management = document.getElementById('cash_management')
            var main = document.getElementById('main')

            
            cash_management.classList.toggle('hidden')
            if(!cash_management.classList.contains('hidden')){
                main.style.filter = 'blur(5px)'
            } else{
                main.style.filter = 'blur(0)'
            }
        }
    </script>
</body>
</html>