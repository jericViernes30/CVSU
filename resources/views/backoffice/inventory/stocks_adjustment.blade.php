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
        <p class="text-lg text-white">Stocks Adjustment</p>
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
                <a href="{{route('office.items_list')}}" class="w-full flex items-center justify-center h-auto py-4">
                    <img src="{{asset('images/products.png')}}" alt="" class="w-[30px] h-auto">
                </a>
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
            <div class="w-full bg-slate-50 shadow-md p-6">
                <div class="w-full flex items-center text-sm py-4 px-5 text-gray-500 border-b border-[#dadada] text-left">
                    <p class="w-[40%]">Item</p>
                    <p class="w-[10%]">In Stock</p>
                    <p class="w-[15%]">Status</p>
                    <p class="w-[15%]">Last update</p>
                    <p class="w-[15%]">Recent adjustment</p>
                    <p class="w-[5%]"></p>
                </div>
                <div class="w-full">
                    @foreach ($item as $i)
                    @php
                        $quantity = $i->quantity;
                        $cost = $i->cost * $quantity;
                        $retail = $i->retail * $quantity;
                        $profit = $retail - $cost;
                    @endphp
                        <div class="w-full flex items-center text-sm py-4 px-5 text-gray-700 border-b border-[#dadada] text-left">
                            <p class="w-[40%]">{{$i->item}}</p>
                            <p class="w-[10%]">{{$i->quantity}}</p>
                            @php
                                if($quantity >= 100){
                                    echo '<p class="w-[15%] text-green-500 font-medium">High amount</p>';
                                } elseif($quantity >= 50){
                                    echo '<p class="w-[15%] text-blue-500 font-medium">Good amount</p>';
                                } elseif($quantity >= 20){
                                    echo '<p class="w-[15%] text-orange-500 font-medium">Low amount</p>';
                                } elseif($quantity >= 1){
                                    echo '<p class="w-[15%] text-red-500 font-medium">Critically low amount</p>';
                                } elseif($quantity == 0){
                                    echo '<p class="w-[15%] text-red-500 font-medium">No stocks</p>';
                                }
                            @endphp
                            </p>
                            <p class="w-[15%]">{{ \Carbon\Carbon::parse($i->updated_at)->format('F j, Y - g:i A') }}</p>
                            <p class="w-[15%]">{{$i->update_reason}}</p>
                            <a href="{{route('office.adjust', ['item_name' => $i->item])}}" class="w-[5%] flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="15" height="15"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z"/></svg>
                            </a>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination links -->
                

                <!-- Your existing pagination UI -->
                <div class="w-full flex items-center gap-10 pt-2 px-6 text-sm text-gray-500">
                    {{ $item->links() }}
                    <div class="flex items-center gap-1 w-[8%]">
                        <p>Page: {{ $item->currentPage() }}</p>
                        <p>of {{ $item->lastPage() }}</p>
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
    </script>
</body>
</html>