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
        <div class="w-[5%] pt-10">
            <div class="w-full relative">
                <button onclick="openDashboard()" class="w-full flex items-center justify-center h-auto py-4">
                    <img src="{{asset('images/chart.png')}}" alt="" class="w-[30px] h-auto">
                </button>
                <div id="dash_options" class="hidden w-[200px] absolute left-20 top-0 z-10 bg-slate-50 p-3 text-sm">
                    <div class="w-full flex flex-col gap-3">
                        <a href="{{route('office.dashboard')}}">Sales Summary</a>
                        <a href="{{route('office.sales_by_item')}}">Sales by item</a>
                        <a href="{{route('office.sales_history')}}">Sales history</a>
                    </div>
                </div>
            </div>
            <div class="w-full relative">
                <button onclick="openItems()" class="w-full flex items-center justify-center h-auto py-4">
                    <img src="{{asset('images/products.png')}}" alt="" class="w-[30px] h-auto">
                </button>
                <div id="items_options" class="hidden w-[200px] absolute left-20 top-0 z-10 bg-slate-50 p-3 text-sm">
                    <div class="w-full flex flex-col gap-3">
                        <a href="{{route('office.items_list')}}">Items List</a>
                    </div>
                </div>
            </div>
            <div class="w-full relative">
                <button onclick="openInventoryOptions()" class="w-full flex items-center justify-center h-auto py-4 border-l-[3px] border-[#039be5] bg-[#f2f2f2]">
                    <img src="{{asset('images/inventory.png')}}" alt="" class="w-[30px] h-auto">
                </button>
                <div id="inventory_options" class="hidden w-[200px] absolute left-20 top-0 z-10 bg-slate-50 p-3 text-sm">
                    <div class="w-full flex flex-col gap-3">
                        <a href="{{route('office.stocks_adjustment')}}">Stocks Adjustment</a>
                        <a href="{{route('office.inventory')}}">Inventory</a>
                    </div>
                </div>
            </div>
            <div class="w-full relative">
                <a href="{{route('office.cashiers')}}" class="w-full flex items-center justify-center h-auto py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" fill="#5c6bc0" width="25" height="25"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192h42.7c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0H21.3C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7h42.7C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3H405.3zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352H378.7C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7H154.7c-14.7 0-26.7-11.9-26.7-26.7z"/></svg>
                </a>
            </div>
            <div class="w-full relative">
                <form action="{{route('office.logout')}}" method="POST" class="w-full flex items-center justify-center h-auto py-4">
                    @csrf
                    <button>
                        <i class="fa-solid fa-arrow-right-from-bracket fa-xl"></i>
                    </button>
                </form>
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