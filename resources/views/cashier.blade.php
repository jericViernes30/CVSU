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
    <div class="w-2/4 mx-auto hidden absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-50" id="scanner">
        <video class="mx-auto" id="preview" width="1%"></video><br>
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
    <div class="w-full flex h-[92%]">
        {{-- navigations --}}
        <div class="w-[5%] py-6">
            <div class="flex w-full flex-col items-center justify-center mb-8">
                <img src="{{asset('images/home-hover.png')}}" alt="Home Icon" class="w-1/3">
                <p class="text-xs text-main">Home</p>
            </div>
            <div class="flex w-full flex-col items-center justify-center mb-8">
                <img src="{{asset('images/cashier.png')}}" alt="Cashier Icon" class="w-1/3">
                <p class="text-xs">Cashier</p>
            </div>
            <div class="flex w-full flex-col items-center justify-center">
                <img src="{{asset('images/history.png')}}" alt="Cashier Icon" class="w-1/3">
                <p class="text-xs">History</p>
            </div>
        </div>
        {{-- POS --}}
        <div class="w-[95%] flex py-10 bg-[#e5e5e5]">
            <div class="w-full grid grid-cols-3 grid-rows-3">
                <div class="w-10/12 grid grid-cols-5 grid-rows-5 row-start-2 col-start-2 shadow-2xl bg-[#f2f2f2]">
                    <p class="row-start-2 col-start-2 col-span-3 text-center">Starting money</p>
                    <form action="{{route('start_shift')}}" method="POST" class="w-full row-start-3 col-start-2 col-span-3">
                        @csrf
                        <input type="number" name="starting_cash" class="w-full rounded-xl outline-none border border-[#bebebe] focus:border focus:border-main px-4 py-2 mb-3">
                        <button class="w-full bg-main py-2 uppercase text-xs rounded-xl">Start Shift</button>
                    </form>
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
    </script>
</body>
</html>