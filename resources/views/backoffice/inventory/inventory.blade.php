<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/5bf9be4e76.js" crossorigin="anonymous"></script>
    @vite('resources/css/app.css')
    <title>Back Office</title>
</head>
<body class="w-full h-screen bg-[#fefefe]">
    <div class="w-full flex items-center h-[7%] bg-[#db121c] px-10">
        <p class="text-lg text-white">Inventory</p>
    </div>
    <div class="w-full h-[93%] flex z-0">
        <div class="w-[5%] pt-10 bg-[#fefefe]">
            <div class="flex w-2/3 mx-auto flex-col items-center justify-center pb-4 mb-3">
                <img src="{{asset('images/logo-transparent.png')}}" alt="">
            </div>
            <div class="w-full relative">
                <button onclick="openDashboard()" class="w-full flex items-center justify-center h-auto py-4">
                    <img src="{{asset('images/chart-new.png')}}" alt="" class="w-[30px] h-auto">
                </button>
                <div id="dash_options" class="hidden w-[200px] absolute left-20 top-0 z-10 bg-slate-50 text-sm">
                    <div class="w-full flex flex-col py-2">
                        <a href="{{route('office.dashboard')}}" class="hover:bg-[#e6e6e6] p-2">Sales summary</a>
                        <a href="{{route('office.sales_by_item')}}" class="hover:bg-[#e6e6e6] p-2">Sales by item</a>
                        <a href="{{route('office.sales_history')}}" class="hover:bg-[#e6e6e6] p-2">Sales history</a>
                    </div>
                </div>
            </div>
            <div class="w-full relative">
                <a href="{{route('office.items_list')}}" class="w-full flex items-center justify-center h-auto py-4">
                    <img src="{{asset('images/prod-new.png')}}" alt="" class="w-[30px] h-auto">
                </a>
            </div>
            <div class="w-full relative">
                <button onclick="openInventoryOptions()" class="w-full flex items-center justify-center h-auto py-4 bg-[#f5a7a4]">
                    <img src="{{asset('images/inv-red.png')}}" alt="" class="w-[30px] h-auto">
                </button>
                <div id="inventory_options" class="hidden w-[200px] absolute left-20 top-0 z-10 bg-slate-50 p-3 text-sm">
                    <div class="w-full flex flex-col gap-3">
                        <a href="{{route('office.stocks_adjustment')}}">Stocks Adjustment</a>
                        <a href="{{route('office.inventory')}}">Inventory</a>
                    </div>
                </div>
            </div>
            {{-- <div class="w-full relative">
                <a href="{{route('qr_printing')}}" class="w-full flex items-center justify-center h-auto py-4">
                    <img src="{{asset('images/qr.png')}}" alt="" class="w-[30px] h-auto">
                </a>
            </div> --}}
            <div class="w-full relative">
                <a href="{{route('office.cashiers')}}" class="w-full flex items-center justify-center h-auto py-4">
                    <img src="{{asset('images/employee-new.png')}}" alt="" class="w-[30px] h-auto">
                </a>
            </div>
            <div class="w-full relative">
                <a href="{{route('office.supplier')}}" class="w-full flex items-center justify-center h-auto py-4">
                    <img src="{{asset('images/supplier-new.png')}}" alt="" class="w-[30px] h-auto">
                </a>
            </div>
            <div class="w-full relative">
                <a href="{{route('office.ordering')}}" class="w-full flex items-center justify-center h-auto py-4">
                    <img src="{{asset('images/order-new.png')}}" alt="" class="w-[30px] h-auto">
                </a>
            </div>
            <div class="w-full relative">
                <a href="{{route('office.logout')}}" class="w-full flex items-center justify-center h-auto py-4">
                    <img src="{{asset('images/logout-new.png')}}" alt="" class="w-[30px] h-auto">
                </a>
            </div>
        </div>
        <div class="w-[95%] bg-[#f2f2f2] z-0 p-7">
            <div class="w-full bg-slate-50 shadow-md py-10 flex justify-evenly mb-10">
                <div class="w-1/4 flex flex-col items-center">
                    <p class="text-gray-700 text-md">Total Item Cost</p>
                    <p class="text-4xl">&#8369;{{$total_cost}}.00</p>
                </div>
                <div class="w-1/4 flex flex-col items-center">
                    <p class="text-gray-700 text-md">Total Retail Value</p>
                    <p class="text-4xl">&#8369;{{$total_retail}}.00</p>
                </div>
                <div class="w-1/4 flex flex-col items-center">
                    <p class="text-gray-700 text-md">Potential Profit</p>
                    <p class="text-4xl">&#8369;{{$profit}}.00</p>
                </div>
                <div class="w-1/4 flex flex-col items-center">
                    <p class="text-gray-700 text-md justify-start">Margin</p>
                    <p class="text-4xl">{{$margin}}%</p>
                </div>
            </div>
            <div class="w-full bg-slate-50 shadow-md py-2">
                {{-- <div class="w-full border-b border-[#dadada] pb-1">
                    <button class="font-medium text-gray-600 uppercase px-5 py-1">Export</button>
                </div> --}}
                <div class="w-full flex items-center text-sm py-4 px-5 text-gray-500 border-b border-[#dadada]">
                    <p class="w-[50%]">Item</p>
                    <p class="w-[10%]">In Stock</p>
                    <p class="w-[10%]">Cost</p>
                    <p class="w-[10%]">Retail value</p>
                    <p class="w-[10%]">Potential profit</p>
                    <p class="w-[10%]">Margin</p>
                </div>
                @foreach ($items as $item)
                    @php
                        $quantity = $item->quantity;
                        $cost = $item->cost * $quantity;
                        $retail = $item->retail * $quantity;
                        $profit = $retail - $cost;
                    @endphp
                    <div class="w-full flex items-center text-sm py-4 px-5 text-gray-700 border-b border-[#dadada]">
                        <p class="w-[50%]">{{$item->item}}</p>
                        <p class="w-[10%]">{{$item->quantity}}</p>
                        <p class="w-[10%]">
                            @php
                                echo '&#8369;'. $item->cost;
                            @endphp
                        </p>
                        <p class="w-[10%]">
                            @php
                                echo '&#8369;'.$item->retail
                            @endphp
                        </p>
                        <p class="w-[10%]">
                            @php
                                echo '&#8369;'.$profit;
                            @endphp
                        </p>
                        <p class="w-[10%]">
                            @php
                                $margin = ($profit/$retail) * 100;
                                $formatted_margin = number_format($margin, 2);
                                echo $formatted_margin.'%';
                            @endphp
                        </p>
                    </div>
                @endforeach
                <div class="w-full flex items-center gap-10 pt-2 px-6 text-sm text-gray-500">
                    {{ $items->links() }}
                    <div class="flex items-center gap-1 w-[8%]">
                        <p>Page: {{ $items->currentPage() }}</p>
                        <p>of {{ $items->lastPage() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function openInventoryOptions(){
            var inventoryOptions = document.getElementById('inventory_options')
            inventoryOptions.classList.toggle('hidden')
        }

        function openDashboard(){
            var inventoryOptions = document.getElementById('dash_options')
            inventoryOptions.classList.toggle('hidden')
        }

        function openItems(){
            var inventoryOptions = document.getElementById('items_options')
            inventoryOptions.classList.toggle('hidden')
        }
    </script>
</body>
</html>