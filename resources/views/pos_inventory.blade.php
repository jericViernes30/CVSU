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
                <button onclick="window.location.href='{{route('add_item')}}'" class="px-5 bg-[#4d4d4d] font-medium uppercase text-xs py-2 text-white rounded-md">Add Item</button>
                <div class="w-full flex items-center text-sm px-5 text-gray-500 border-b border-[#dadada] text-left">
                    <button id="name_order" class="w-[35%] items-center flex gap-2 pr-2 py-4 hover:bg-[#ddd5d5]">
                        <p>Item Name</p>
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="10" height="10"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M182.6 137.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-9.2 9.2-11.9 22.9-6.9 34.9s16.6 19.8 29.6 19.8l256 0c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-128-128z"/></svg>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="10" height="10"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M137.4 374.6c12.5 12.5 32.8 12.5 45.3 0l128-128c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8L32 192c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l128 128z"/></svg>
                        </div>
                    </button>
                    <button id="stock_order" class="w-[10%] items-center flex gap-2 py-4 hover:bg-[#ddd5d5] justify-end">
                        <p>In Stock</p>
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="10" height="10"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M182.6 137.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-9.2 9.2-11.9 22.9-6.9 34.9s16.6 19.8 29.6 19.8l256 0c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-128-128z"/></svg>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="10" height="10"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M137.4 374.6c12.5 12.5 32.8 12.5 45.3 0l128-128c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8L32 192c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l128 128z"/></svg>
                        </div>
                    </button>
                    <p class="w-[15%] text-right">Status</p>
                    <p class="w-[23%] text-right">Last update</p>
                    <p class="w-[17%] text-right">Recent adjustment</p>
                </div>
                <div id="search_result" class="w-full h-[590px] overflow-y-auto">
                    @foreach ($item as $i)
                    @php
                        $quantity = $i->quantity;
                    @endphp
                        <div class='w-full flex items-center text-sm py-4 px-5 text-gray-700 border-b border-[#dadada] text-left'>
                            <p class='w-[35%]'>{{$i->item}}</p>
                            <p class='w-[10%] text-right'>{{$i->quantity}}</p>
                            @php
                                if($i->category != 'Limited Edition' && $quantity >= 100){
                                    echo '<p class="w-[15%] text-green-500 font-medium text-right">High amount</p>';
                                } elseif($i->category != 'Limited Edition' && $quantity >= 50){
                                    echo '<p class="w-[15%] text-blue-500 font-medium text-right">Good amount</p>';
                                } elseif($i->category != 'Limited Edition' && $quantity >= 20){
                                    echo '<p class="w-[15%] text-orange-500 font-medium text-right">Low amount</p>';
                                } elseif($i->category != 'Limited Edition' && $quantity >= 1){
                                    echo '<p class="w-[15%] text-red-500 font-medium text-right">Critically low amount</p>';
                                } elseif($i->category != 'Limited Edition' && $quantity == 0){
                                    echo '<p class="w-[15%] text-red-500 font-medium text-right">Items sold</p>';
                                } elseif($i->category == 'Limited Edition' && $quantity == 0){
                                    echo '<p class="w-[15%] text-red-500 font-medium text-right">Items sold</p>';
                                } elseif($i->category == 'Limited Edition'){
                                    echo '<p class="w-[15%] text-[#FFD700] font-medium text-right">Limited Edition</p>';
                                } else {
                                    echo '<p class="w-[15%] text-red-500 font-medium text-right">No stocks</p>';
                                }
                            @endphp
                            </p>
                            <p class='w-[23%] text-right'>{{ \Carbon\Carbon::parse($i->updated_at)->format('F j, Y - g:i A') }}</p>
                            <p class='w-[17%] text-right'>{{$i->update_reason ?? 'None'}}</p>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination links -->
                

                <!-- Your existing pagination UI -->
                {{-- <div class="w-full flex items-center gap-10 pt-2 px-6 text-sm text-gray-500">
                    {{ $item->links() }}
                    <div class="flex items-center gap-1 w-[8%]">
                        <p>Page: {{ $item->currentPage() }}</p>
                        <p>of {{ $item->lastPage() }}</p>
                    </div>
                </div> --}}
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
                            } else {
                                quantityClass = 'text-red-500 font-medium';
                                quantityText = 'No stocks';
                            }

                            function formatDate(dateString) {
                            const options = {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric',
                                hour: 'numeric',
                                minute: 'numeric',
                                hour12: true,
                            };

                            const date = new Date(dateString);
                            return date.toLocaleDateString('en-US', options);
                        }

                            var itemList = `
                            <div class='w-full flex items-center text-sm py-4 px-5 text-gray-700 border-b border-[#dadada] text-left'>
                                <p class='w-[35%]'>${menu.item}</p>
                                <p class='w-[10%] text-right'>${menu.quantity}</p>
                                <p class='w-[15%] ${quantityClass} text-right'>${quantityText}</p>
                                <p class='w-[23%] text-right'>${formatDate(menu.updated_at)}</p>
                                <p class='w-[17%] text-right'>${menu.update_reason}</p>
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
            let isAscendingOrder = false; // Track whether the sorting is ascending or default

            $('#name_order').on('click', function() {
                isAscendingOrder = !isAscendingOrder; // Toggle the sorting state

                $.ajax({
                    url: isAscendingOrder ? "{{route('office.name_order')}}" : "{{route('office.default_order')}}",
                    type: 'GET',
                    success: function(data) {
                        console.log(data);
                        $('#search_result').empty(); // Clear existing items

                        // Function to format the date
                        function formatDate(dateString) {
                            const options = {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric',
                                hour: 'numeric',
                                minute: 'numeric',
                                hour12: true,
                            };

                            const date = new Date(dateString);
                            return date.toLocaleDateString('en-US', options);
                        }

                        if (data.length > 0) {
                            $.each(data, function(index, item) {
                                let quantityStatus = '';
                                if (item.quantity >= 100) {
                                    quantityStatus = '<p class="w-[15%] text-green-500 font-medium text-right">High amount</p>';
                                } else if (item.quantity >= 50) {
                                    quantityStatus = '<p class="w-[15%] text-blue-500 font-medium text-right">Good amount</p>';
                                } else if (item.quantity >= 20) {
                                    quantityStatus = '<p class="w-[15%] text-orange-500 font-medium text-right">Low amount</p>';
                                } else if (item.quantity >= 1) {
                                    quantityStatus = '<p class="w-[15%] text-red-500 font-medium text-right">Critically low amount</p>';
                                } else {
                                    quantityStatus = '<p class="w-[15%] text-red-500 font-medium text-right">No stocks</p>';
                                }

                                let itemRow = `
                                    <div class="w-full flex items-center text-sm py-4 px-5 text-gray-700 border-b border-[#dadada] text-left">
                                        <p class="w-[35%]" id="item_res">${item.item}</p>
                                        <p class="w-[10%] text-right" id="quantity_res">${item.quantity}</p>
                                        ${quantityStatus}
                                        <p class='w-[23%] text-right'>${formatDate(item.updated_at)}</p>
                                        <p class='w-[17%] text-right'>${item.update_reason}</p>
                                    </div>`;

                                $('#search_result').append(itemRow);
                            });

                        } else {
                            $('#search_result').append('<p>No items found</p>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', status, error);
                    }
                });

            });

            $('#stock_order').on('click', function() {
                isAscendingOrder = !isAscendingOrder; // Toggle the sorting state

                $.ajax({
                    url: isAscendingOrder ? "{{route('office.stock_order')}}" : "{{route('office.default_stock_order')}}",
                    type: 'GET',
                    success: function(data) {
                        console.log(data)
                        $('#search_result').empty(); // Clear existing items

                        if (data.length > 0) {
                            $.each(data, function(index, item) {

                                let quantityStatus = '';

                                function formatDate(dateString) {
                                    const options = {
                                        year: 'numeric',
                                        month: 'long',
                                        day: 'numeric',
                                        hour: 'numeric',
                                        minute: 'numeric',
                                        hour12: true,
                                    };

                                    const date = new Date(dateString);
                                    return date.toLocaleDateString('en-US', options);
                                }

                                if (item.quantity >= 100) {
                                    quantityStatus = '<p class="w-[15%] text-green-500 font-medium text-right">High amount</p>';
                                } else if (item.quantity >= 50) {
                                    quantityStatus = '<p class="w-[15%] text-blue-500 font-medium text-right">Good amount</p>';
                                } else if (item.quantity >= 20) {
                                    quantityStatus = '<p class="w-[15%] text-orange-500 font-medium text-right">Low amount</p>';
                                } else if (item.quantity >= 1) {
                                    quantityStatus = '<p class="w-[15%] text-red-500 font-medium text-right">Critically low amount</p>';
                                } else {
                                    quantityStatus = '<p class="w-[15%] text-red-500 font-medium text-right">No stocks</p>';
                                }

                                let itemRow = `
                                    <div class="w-full flex items-center text-sm py-4 px-5 text-gray-700 border-b border-[#dadada] text-left">
                                        <p class="w-[35%]" id="item_res">${item.item}</p>
                                        <p class="w-[10%] text-right" id="quantity_res">${item.quantity}</p>
                                        ${quantityStatus}
                                        <p class='w-[23%] text-right'>${formatDate(item.updated_at)}</p>
                                        <p class='w-[17%] text-right'>${item.update_reason}</p>
                                    </div>`;
                                
                                $('#search_result').append(itemRow);
                            });
                        } else {
                            $('#search_result').append('<p>No items found</p>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', status, error);
                    }
                });
            });
        })
    </script>
</body>
</html>