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
<body class="w-full h-screen bg-[#fefefe]">
    <div class="w-2/4 mx-auto hidden absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-50" id="scanner">
        <video class="mx-auto" id="preview" width="1%"></video><br>
    </div>
    {{-- Top bar --}}
    <div class="w-full flex items-center h-[8%] px-20 border-b border-bd">
        <div class="w-1/6">
            <p class="text-lg font-semibold">MAMITA'S</p>
        </div>
    </div>
    {{-- main --}}
    <div class="w-full flex h-[92%]">
        {{-- navigations --}}
        <div class="w-[5%] border-r border-bd py-6">
            <a href="{{route('dashboard')}}" class="flex w-full flex-col items-center justify-center mb-8">
                <img src="{{asset('images/home.png')}}" alt="Home Icon" class="w-1/3">
                <p class="text-xs">Home</p>
            </a>
            <div class="flex w-full flex-col items-center justify-center mb-8">
                <img src="{{asset('images/cashier.png')}}" alt="Cashier Icon" class="w-1/3">
                <p class="text-xs">Cashier</p>
            </div>
            <div class="flex w-full flex-col items-center justify-center">
                <img src="{{asset('images/history-hover.png')}}" alt="Cashier Icon" class="w-1/3">
                <p class="text-xs text-main">History</p>
            </div>
        </div>
        {{-- POS --}}
        <div class="w-[95%] flex p-10 bg-[#ededed]">
            <div class="w-full block mx-auto shadow-2xl rounded-md p-10 bg-[#f4f4f4] overflow-hidden">
                <div class="mb-5">
                    <form action="{{route('purchased_date')}}" method="GET" class="flex gap-2 items-center">
                        <input type="date" id="date" name="date" class="px-2 py-1 border border-bd rounded-md">
                        <button class="px-10 py-1 bg-main uppercase rounded-md">Filter</button>
                    </form>
                    <script>
                        // Get today's date
                        const today = new Date().toISOString().split('T')[0];
                        // Set the input value to today's date
                        document.getElementById('date').value = today;
                      </script>
                </div>
                <div class="w-full">
                    <div class="w-full flex p-2 bg-[#bebebe] uppercase font-medium">
                        <p class="w-[15%]">Ticket Number</p>
                        <p class="w-[20%]">Date</p>
                        <p class="w-[15%]">Cashier</p>
                        <p class="w-[20%]">Customer</p>
                        <p class="w-[10%]">Type</p>
                        <p class="w-[15%]">Total</p>
                        <div class="w-[5%]"></div>
                    </div>
                    @forelse ($history as $sale)
                        <div class="w-full flex border-b border-[#bebebe] p-2">
                            <p class="w-[15%]">1-{{ $sale->ticket }}</p>
                            <p class="w-[20%]">{{ $sale->created_at->format('F j, Y H:i:s') }}</p>
                            <p class="w-[15%]">{{ $sale->cashier }}</p>
                            <p class="w-[20%]">{{ $sale->customer }}</p>
                            <p class="w-[10%]">{{ $sale->type }}</p>
                            <p class="w-[15%]">&#8369; {{ $sale->total }}</p>
                            <div class="w-[5%]">
                                <button class="w-full py-1 bg-main rounded-md uppercase block mx-auto text-white text-sm">
                                    View
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-500 py-4">No purchases made</div>
                    @endforelse
                </div>
            </div>  
        </div>
    </div>
</body>
</html>