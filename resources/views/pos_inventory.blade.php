<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="{{ asset('jquery/jquery.js') }}"></script>
    @vite('resources/css/app.css')
    <title>Dashboard</title>
</head>
<body class="w-full h-screen bg-[#ffd962]">
    {{-- Top bar --}}
    {{-- <div class="w-full flex items-center h-[8%] px-20 border-b border-bd">
        <div class="w-1/6">
            <div class="">
                <img src="{{asset('images/logo2.png')}}" alt="" class="w-1/2">
            </div>
        </div>
    </div> --}}
    {{-- main --}}
    <div id="main" class="w-full flex h-full z-0">
        {{-- navigations --}}
        <div class="w-[6%] py-6 bg-white">
            <div class="flex w-2/3 mx-auto flex-col items-center justify-center py-4 mb-3">
                <img src="{{asset('images/logo-transparent.png')}}" alt="">
            </div>
            <a href="{{route('dashboard')}}" class="flex w-2/3 mx-auto flex-col items-center justify-center py-4">
                <img src="{{asset('images/products-new.png')}}" alt="Home Icon" class="w-1/3">
                <p class="text-xs text-[#565857]">Home</p>
            </a>
            <a href="{{route('cashier')}}" class="flex w-2/3 mx-auto flex-col items-center justify-center py-4">
                <img src="{{asset('images/cashier-new.png')}}" alt="Cashier Icon" class="w-1/3">
                <p class="text-xs text-[#565857]">Cashier</p>
            </a>
            <a href="{{route('history')}}" class="flex w-2/3 mx-auto flex-col items-center justify-center py-4">
                <img src="{{asset('images/history-new.png')}}" alt="Cashier Icon" class="w-1/3">
                <p class="text-xs text-[#565857]">History</p>
            </a>
            <a href="{{route('inventory')}}" class="flex w-2/3 mx-auto flex-col items-center justify-center py-4 rounded-xl bg-[#f5a7a4]">
                <img src="{{asset('images/inv-red.png')}}" alt="Cashier Icon" class="w-1/3">
                <p class="text-xs text-[#e5231a]">Inventory</p>
            </a>
            <a href="{{route('orders')}}" class="flex w-2/3 mx-auto flex-col items-center justify-center py-4">
                <img src="{{asset('images/order-new.png')}}" alt="Cashier Icon" class="w-1/3">
                <p class="text-xs text-[#565857]">Orders</p>
            </a>
            <a href="{{route('office.login')}}" target="__blank" class="flex w-2/3 mx-auto flex-col items-center justify-center py-4">
                <img src="{{asset('images/backoffice-new.png')}}" alt="Cashier Icon" class="w-1/3">
                <p class="text-xs text-[#565857]">Office</p>
            </a>
        </div>
        {{-- POS --}}
        <div class="w-[94%] flex p-6 bg-[#e5e5e5]">
            <div class="w-full bg-slate-50 shadow-md rounded-xl p-6">
                <input id="item_search" type="search" placeholder="Search for item" name="item" class="w-[400px] px-5 py-2 rounded-lg outline-none border border-[#565857] focus:border-main">
                <div class="w-full flex items-center text-sm py-4 px-5 text-gray-500 border-b border-[#dadada] text-left">
                    <p class="w-[40%]">Item</p>
                    <p class="w-[10%] text-right">In Stock</p>
                    <p class="w-[15%] text-right">Status</p>
                    <p class="w-[15%] text-right">Last update</p>
                    <p class="w-[20%] text-right">Recent adjustment</p>
                </div>
                <div id="search_result" class="w-full">
                    @foreach ($item as $i)
                    @php
                        $quantity = $i->quantity;
                    @endphp
                        <div class='w-full flex items-center text-sm py-4 px-5 text-gray-700 border-b border-[#dadada] text-left'>
                            <p class='w-[40%]'>{{$i->item}}</p>
                            <p class='w-[10%] text-right'>{{$i->quantity}}</p>
                            @php
                                if($quantity >= 100){
                                    echo '<p class="w-[15%] text-green-500 font-medium text-right">High amount</p>';
                                } elseif($quantity >= 50){
                                    echo '<p class="w-[15%] text-blue-500 font-medium text-right">Good amount</p>';
                                } elseif($quantity >= 20){
                                    echo '<p class="w-[15%] text-orange-500 font-medium text-right">Low amount</p>';
                                } elseif($quantity >= 1){
                                    echo '<p class="w-[15%] text-red-500 font-medium text-right">Critically low amount</p>';
                                } elseif($quantity == 0){
                                    echo '<p class="w-[15%] text-red-500 font-medium text-right">No stocks</p>';
                                }
                            @endphp
                            </p>
                            <p class='w-[15%] text-right'>{{ \Carbon\Carbon::parse($i->updated_at)->format('F j, Y - g:i A') }}</p>
                            <p class='w-[20%] text-right'>{{$i->update_reason}}</p>
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
        $(document).ready(function(){
            $('#item_search').on('keyup', function(){
    var key = $(this).val();
    var url = '{{route("item_search", ["key" => ":key"])}}';
    url = url.replace(':key', key);
    // console.log(url)

    $.ajax({
        url: url,
        method: 'GET',
        success: function(response){
            var itemDiv = $('#search_result');
            itemDiv.empty(); // Clear the current contents

            response.menus.forEach(function(menu) {
                var quantityClass = '';
                var quantityText = '';

                if (menu.quantity >= 100) {
                    quantityClass = 'text-green-500 font-medium';
                    quantityText = 'High amount';
                } else if (menu.quantity >= 50) {
                    quantityClass = 'text-blue-500 font-medium';
                    quantityText = 'Good amount';
                } else if (menu.quantity >= 20) {
                    quantityClass = 'text-orange-500 font-medium';
                    quantityText = 'Low amount';
                } else if (menu.quantity >= 1) {
                    quantityClass = 'text-red-500 font-medium';
                    quantityText = 'Critically low amount';
                } else if (menu.quantity == 0) {
                    quantityClass = 'text-red-500 font-medium';
                    quantityText = 'No stocks';
                }

                var itemList = `
                <div class='w-full flex items-center text-sm py-4 px-5 text-gray-700 border-b border-[#dadada] text-left'>
                    <p class='w-[40%]'>${menu.item}</p>
                    <p class='w-[10%]'>${menu.quantity}</p>
                    <p class='w-[15%] ${quantityClass}'>${quantityText}</p>
                    <p class='w-[15%]'>${new Date(menu.updated_at).toLocaleString()}</p>
                    <p class='w-[15%]'>${menu.update_reason}</p>
                </div>
                `;
                itemDiv.append(itemList);
            });
        },
        error: function(xhr, status, error){
            console.error(xhr, status, error);
        }
    })
});

        })
    </script>
</body>
</html>