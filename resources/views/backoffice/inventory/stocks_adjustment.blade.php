<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/5bf9be4e76.js" crossorigin="anonymous"></script>
    <script src="{{ asset('jquery/jquery.js') }}"></script>
    @vite('resources/css/app.css')
    <title>Back Office</title>
</head>
<body class="w-full h-screen bg-[#fefefe] relative">
    <div id="coverup" class="hidden w-full bg-main h-screen absolute z-50 opacity-30"></div>
    <form id="filterForm">
        <div id="filterModal" class="hidden py-1 w-1/3 bg-[#f0f0f0] shadow-3xl absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-50 rounded-2xl">
            <div class="flex py-3 px-5 justify-between items-center">
                <p class="font-medium">Filter Items</p>
                <button onclick="closeFilter(event)">
                    <img src="{{asset('images/close.png')}}" alt="" class="w-3/5 block mx-auto">
                </button>
            </div>
            <div class="w-full border-y-2 border-[#c7c3c3] py-3 px-5">
                <p class="font-medium">Where</p>
                <div class="w-full flex gap-4 mb-5">
                    <div class="w-1/2 flex flex-col gap-1">
                        <label for="">Stocks</label>
                        <div class="w-full flex items-center gap-2 py-2 rounded-xl text-xs">
                            <label class="w-1/2 flex items-center gap-1">
                                <input type="radio" name="quantity" value="less_50">
                                <p>Less than 50</p>
                            </label>
                            <label class="w-1/2 flex items-center gap-1">
                                <input type="radio" name="quantity" value="less_100">
                                <p>Less than 100</p>
                            </label>
                        </div>
                    </div>
                    <div class="w-1/2 flex flex-col gap-1">
                        <label for="">Expiration Date</label>
                        <div class="w-full flex items-center gap-2 py-2 rounded-xl text-xs">
                            <label class="w-1/2 flex items-center gap-1">
                                <input type="radio" name="expiration_date" value="less_15">
                                <p>15 days left</p>
                            </label>
                            <label class="w-1/2 flex items-center gap-1">
                                <input type="radio" name="expiration_date" value="less_60">
                                <p>1 month left</p>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="w-full flex justify-end items-center">
                    <button type="button" id="unselect" class="px-6 py-2 rounded-sm underline text-xs">Unselect</button>
                </div>
                <div class="h-[1px] border-b-2 border-[#c7c1c1] mb-5"></div>
                <div class="w-full flex gap-4 mb-5">
                    <div class="w-1/2 flex flex-col gap-1">
                        <label for="">Color</label>
                        <select name="color" id="color" class="
                        w-full py-2 px-3 rounded-xl outline-none">
                            <option value="" selected disabled></option>
                            <option value="red">Red</option>
                            <option value="blue">Blue</option>
                            <option value="yellow">Yellow</option>
                        </select>
                    </div>
                    <div class="w-1/2 flex flex-col gap-1">
                        <label for="">Size</label>
                        <select name="size" id="size" class="
                        w-full py-2 px-3 rounded-xl outline-none">
                            <option value="" selected disabled></option>
                            <option value="less_200">Less than 200g/ml</option>
                            <option value="200_400">Greater than 200g/ml & Less than 400g/ml</option>
                            <option value="400_1000">Greater than 400g/ml & Less than 1000g/ml</option>
                            <option value="1000_2000">Greater than 1kg/L & Less than 2kg/L</option>
                        </select>
                    </div>
                </div>
                <div class="w-full flex gap-4">
                    <div class="w-1/2 flex flex-col gap-1">
                        <label for="">Category</label>
                        <select name="category" id="category" class="
                        w-full py-2 px-3 rounded-xl outline-none">
                            <option value="" selected disabled></option>
                            <option value="dry_goods">Dry Goods</option>
                            <option value="wet_goods">Wet Goods</option>
                        </select>
                    </div>
                    <div class="w-1/2 flex flex-col gap-1">
                        <label for="">Price (&#8369;)</label>
                        <div class="w-full flex gap-3 items-center">
                            <div class="flex w-1/2">
                                <input type="number" name="price_from" id="price_from" placeholder="1" class="w-full px-1 py-2 outline-none rounded-xl">
                            </div>
                            <p>to</p>
                            <div class="flex w-1/2">
                                <input type="number" name="price_to" id="price_to" placeholder="ex. 1000" class="w-full px-1 py-2 outline-none rounded-xl">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full flex justify-end py-3 px-4">
                <button type="button" id="applyFilterButton" class="px-8 py-2 bg-main text-white text-sm rounded-xl">Apply</button>
            </div>
        </div>
    </form>
    <div class="w-full flex items-center h-[7%] bg-[#db121c] px-10">
        <p class="text-lg text-white">Stocks Adjustment</p>
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
            <div class="w-full bg-slate-50 shadow-md p-6">
                <div class="w-full flex gap-4 px-5">
                    <input id="item_search" class="w-1/4 border-2 rounded-lg outline-none px-6 py-2" type="search" name="key" placeholder="Search for item name">
                    <button onclick="openFilter()" class="px-10 py-2 rounded-lg text-center bg-main text-white font-medium uppercase text-xs">Advance filter</button>
                </div>
                <div class="w-full flex items-center text-sm px-5 text-gray-500 border-b border-[#dadada] text-left">
                    
                    <button id="name_order" class="w-[35%] items-center flex gap-2 pr-2 py-4 hover:bg-[#ddd5d5]">
                        <p>Item Name</p>
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="10" height="10"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M182.6 137.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-9.2 9.2-11.9 22.9-6.9 34.9s16.6 19.8 29.6 19.8l256 0c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-128-128z"/></svg>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="10" height="10"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M137.4 374.6c12.5 12.5 32.8 12.5 45.3 0l128-128c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8L32 192c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l128 128z"/></svg>
                        </div>
                    </button>
                    <button id="stock_order" class="w-[8%] items-center flex gap-2 py-4 hover:bg-[#ddd5d5]">
                        <p>In Stock</p>
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="10" height="10"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M182.6 137.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-9.2 9.2-11.9 22.9-6.9 34.9s16.6 19.8 29.6 19.8l256 0c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-128-128z"/></svg>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="10" height="10"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M137.4 374.6c12.5 12.5 32.8 12.5 45.3 0l128-128c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8L32 192c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l128 128z"/></svg>
                        </div>
                    </button>
                    <p class="w-[12%]">Exp. Date</p>
                    <p class="w-[15%]">Status</p>
                    <p class="w-[15%]">Recent adjustment</p>
                    <p class="w-[15%]"></p>
                </div>
                <div id="filteredItems" class="w-full  h-[500px] overflow-y-auto">
                    @foreach ($items as $i)
                        @php
                            $quantity = $i->quantity;
                            $cost = $i->cost * $quantity;
                            $retail = $i->retail * $quantity;
                            $profit = $retail - $cost;
                            
                            // Get the corresponding latest order for this item if it exists
                            $matchingOrder = $latestOrders[$i->item] ?? null;
                            $expirationDate = $matchingOrder ? (new DateTime($matchingOrder->expiration_date))->format('F j, Y') : 'N/A';
                        @endphp
                        <div class="w-full flex items-center text-sm py-4 px-5 text-gray-700 border-b border-[#dadada] text-left">
                            <p class="w-[35%]" id="item_res">{{$i->item}}</p>
                            <p class="w-[8%]" id="quantity_res">{{$i->quantity}}</p>
                            <button class="w-[12%] text-left" id="expiration_date">{{ $expirationDate }}</button>
                            @php
                                if($i->category != 'Limited Edition' && $quantity >= 100){
                                    echo '<p class="w-[15%] text-green-500 font-medium">High amount</p>';
                                } elseif($i->category != 'Limited Edition' && $quantity >= 50){
                                    echo '<p class="w-[15%] text-blue-500 font-medium">Good amount</p>';
                                } elseif($i->category != 'Limited Edition' && $quantity >= 20){
                                    echo '<p class="w-[15%] text-orange-500 font-medium">Low amount</p>';
                                } elseif($i->category != 'Limited Edition' && $quantity >= 1){
                                    echo '<p class="w-[15%] text-red-500 font-medium">Critically low amount</p>';
                                } elseif($i->category != 'Limited Edition' && $quantity == 0){
                                    echo '<p class="w-[15%] text-red-500 font-medium">Items sold</p>';
                                } elseif($i->category == 'Limited Edition' && $quantity == 0){
                                    echo '<p class="w-[15%] text-red-500 font-medium">Items sold</p>';
                                } elseif($i->category == 'Limited Edition'){
                                    echo '<p class="w-[15%] text-[#FFD700] font-medium">Limited Edition</p>';
                                }
                            @endphp
                            <p class="w-[15%]">{{$i->update_reason}}</p>
                            <a href="{{ route('office.adjust', ['item_name' => $i->item]) }}" class="w-[15%] flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="15" height="15">
                                    <path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z"/>
                                </svg>
                            </a>
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
        $(document).ready(function() {
            $('#applyFilterButton').click(function() {
                $.ajax({
                    url: "{{ route('office.filter_items') }}",
                    type: 'GET',
                    data: $('#filterForm').serialize(),
                    success: function(data) {
                        // console.log(data);
                        $('#filteredItems').empty();
                        if (data.length > 0) {
                            $.each(data, function(index, item) {
                                let quantityStatus = '';
                                const expirationDate = item.latest_order 
                                    ? new Date(item.latest_order.expiration_date).toLocaleDateString('en-US', {
                                        year: 'numeric',
                                        month: 'long',
                                        day: 'numeric'
                                    }) 
                                    : 'N/A';

                                if (item.quantity >= 100) {
                                    quantityStatus = '<p class="w-[15%] text-green-500 font-medium">High amount</p>';
                                } else if (item.quantity >= 50) {
                                    quantityStatus = '<p class="w-[15%] text-blue-500 font-medium">Good amount</p>';
                                } else if (item.quantity >= 20) {
                                    quantityStatus = '<p class="w-[15%] text-orange-500 font-medium">Low amount</p>';
                                } else if (item.quantity >= 1) {
                                    quantityStatus = '<p class="w-[15%] text-red-500 font-medium">Critically low amount</p>';
                                } else {
                                    quantityStatus = '<p class="w-[15%] text-red-500 font-medium">No stocks</p>';
                                }

                                let itemRow = `
                                    <div class="w-full flex items-center text-sm py-4 px-5 text-gray-700 border-b border-[#dadada] text-left">
                                        <p class="w-[35%]" id="item_res">${item.item}</p>
                                        <p class="w-[8%]" id="quantity_res">${item.quantity}</p>
                                        <p class="w-[12%]" id="expiration_date">${expirationDate}</p>
                                        ${quantityStatus}
                                        <p class="w-[15%]">${item.update_reason}</p>
                                        <a href="/back-office/stocks_adjustments/${item.item}" class="w-[15%] flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="15" height="15">
                                                <path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z"/>
                                            </svg>
                                        </a>
                                    </div>`;
                                $('#filteredItems').append(itemRow);
                            });
                        } else {
                            $('#filteredItems').append('<p>No items found</p>');
                        }
                    },
                    error: function() {
                        alert('Error retrieving filtered items');
                    }
                });
            });

            let isAscendingOrder = false; // Track whether the sorting is ascending or default

            $('#name_order').on('click', function() {
                isAscendingOrder = !isAscendingOrder; // Toggle the sorting state

                $.ajax({
                    url: isAscendingOrder ? "{{route('office.name_order')}}" : "{{route('office.default_order')}}",
                    type: 'GET',
                    success: function(data) {
                        console.log(data)
                        $('#filteredItems').empty(); // Clear existing items

                        if (data.length > 0) {
                            $.each(data, function(index, item) {
                                let quantityStatus = '';
                                if (item.quantity >= 100) {
                                    quantityStatus = '<p class="w-[15%] text-green-500 font-medium">High amount</p>';
                                } else if (item.quantity >= 50) {
                                    quantityStatus = '<p class="w-[15%] text-blue-500 font-medium">Good amount</p>';
                                } else if (item.quantity >= 20) {
                                    quantityStatus = '<p class="w-[15%] text-orange-500 font-medium">Low amount</p>';
                                } else if (item.quantity >= 1) {
                                    quantityStatus = '<p class="w-[15%] text-red-500 font-medium">Critically low amount</p>';
                                } else {
                                    quantityStatus = '<p class="w-[15%] text-red-500 font-medium">No stocks</p>';
                                }

                                // Access expiration date from latest_order if available
                                const expirationDate = item.latest_order && item.latest_order.expiration_date 
                                    ? new Date(item.latest_order.expiration_date).toLocaleDateString('en-US', {
                                        year: 'numeric',
                                        month: 'long',
                                        day: 'numeric'
                                    }) 
                                    : 'N/A'; // Use 'N/A' if expiration date is not available

                                let itemRow = `
                                    <div class="w-full flex items-center text-sm py-4 px-5 text-gray-700 border-b border-[#dadada] text-left">
                                        <p class="w-[35%]" id="item_res">${item.item}</p>
                                        <p class="w-[8%]" id="quantity_res">${item.quantity}</p>
                                        <p class="w-[12%]" id="expiration_date">${expirationDate}</p>
                                        ${quantityStatus}
                                        <p class="w-[15%]">${item.update_reason}</p>
                                        <a href="/back-office/stocks_adjustments/${item.item}" class="w-[15%] flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="15" height="15">
                                                <path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z"/>
                                            </svg>
                                        </a>
                                    </div>`;
                                
                                $('#filteredItems').append(itemRow);
                            });

                        } else {
                            $('#filteredItems').append('<p>No items found</p>');
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
                        $('#filteredItems').empty(); // Clear existing items

                        if (data.length > 0) {
                            $.each(data, function(index, item) {
                                 // Access expiration date from latest_order if available
                                 const expirationDate = item.latest_order && item.latest_order.expiration_date 
                                    ? new Date(item.latest_order.expiration_date).toLocaleDateString('en-US', {
                                        year: 'numeric',
                                        month: 'long',
                                        day: 'numeric'
                                    }) 
                                    : 'N/A'; // Use 'N/A' if expiration date is not available

                                let quantityStatus = '';

                                if (item.quantity >= 100) {
                                    quantityStatus = '<p class="w-[15%] text-green-500 font-medium">High amount</p>';
                                } else if (item.quantity >= 50) {
                                    quantityStatus = '<p class="w-[15%] text-blue-500 font-medium">Good amount</p>';
                                } else if (item.quantity >= 20) {
                                    quantityStatus = '<p class="w-[15%] text-orange-500 font-medium">Low amount</p>';
                                } else if (item.quantity >= 1) {
                                    quantityStatus = '<p class="w-[15%] text-red-500 font-medium">Critically low amount</p>';
                                } else {
                                    quantityStatus = '<p class="w-[15%] text-red-500 font-medium">No stocks</p>';
                                }

                                let itemRow = `
                                    <div class="w-full flex items-center text-sm py-4 px-5 text-gray-700 border-b border-[#dadada] text-left">
                                        <p class="w-[35%]" id="item_res">${item.item}</p>
                                        <p class="w-[8%]" id="quantity_res">${item.quantity}</p>
                                        <p class="w-[12%]" id="expiration_date">${expirationDate}</p>
                                        ${quantityStatus}
                                        <p class="w-[15%]">${item.update_reason}</p>
                                        <a href="/back-office/stocks_adjustments/${item.item}" class="w-[15%] flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="15" height="15">
                                                <path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z"/>
                                            </svg>
                                        </a>
                                    </div>`;
                                
                                $('#filteredItems').append(itemRow);
                            });
                        } else {
                            $('#filteredItems').append('<p>No items found</p>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', status, error);
                    }
                });
            });

            $('#expiration_date').on('click', function() {
                var itemValue = $('#item_res').text(); // Use .text() to get the text content
                alert(itemValue); // Display the value in the console
            });

            $('#unselect').on('click', function(){
                $('input[type="radio"]').prop('checked', false)
            })

            $('#item_search').on('keyup', function(){
                var key = $(this).val();
                var url = '{{route("office.item_search_v2", ["key" => ":key"])}}';
                url = url.replace(':key', key);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data) {
                        $('#filteredItems').empty(); // Clear existing items

                        if (data.length > 0) {
                            $.each(data, function(index, item) {
                                 // Access expiration date from latest_order if available
                                 const expirationDate = item.latest_order && item.latest_order.expiration_date 
                                    ? new Date(item.latest_order.expiration_date).toLocaleDateString('en-US', {
                                        year: 'numeric',
                                        month: 'long',
                                        day: 'numeric'
                                    }) 
                                    : 'N/A'; // Use 'N/A' if expiration date is not available

                                let quantityStatus = '';

                                if (item.quantity >= 100) {
                                    quantityStatus = '<p class="w-[15%] text-green-500 font-medium">High amount</p>';
                                } else if (item.quantity >= 50) {
                                    quantityStatus = '<p class="w-[15%] text-blue-500 font-medium">Good amount</p>';
                                } else if (item.quantity >= 20) {
                                    quantityStatus = '<p class="w-[15%] text-orange-500 font-medium">Low amount</p>';
                                } else if (item.quantity >= 1) {
                                    quantityStatus = '<p class="w-[15%] text-red-500 font-medium">Critically low amount</p>';
                                } else {
                                    quantityStatus = '<p class="w-[15%] text-red-500 font-medium">No stocks</p>';
                                }

                                let itemRow = `
                                    <div class="w-full flex items-center text-sm py-4 px-5 text-gray-700 border-b border-[#dadada] text-left">
                                        <p class="w-[35%]" id="item_res">${item.item}</p>
                                        <p class="w-[8%]" id="quantity_res">${item.quantity}</p>
                                        <p class="w-[12%]" id="expiration_date">${expirationDate}</p>
                                        ${quantityStatus}
                                        <p class="w-[15%]">${item.update_reason}</p>
                                        <a href="/back-office/stocks_adjustments/${item.item}" class="w-[15%] flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="15" height="15">
                                                <path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z"/>
                                            </svg>
                                        </a>
                                    </div>`;
                                
                                $('#filteredItems').append(itemRow);
                            });
                        } else {
                            $('#filteredItems').append('<p>No items found</p>');
                        }
                    },
                    error: function(xhr, status, error){
                        console.error('AJAX error:', status, error)
                    }
                })
            })
        });


        function openInventoryOptions(){
            var inventoryOptions = document.getElementById('inventory_options')
            inventoryOptions.classList.toggle('hidden')
        }

        function openDashboard(){
            var inventoryOptions = document.getElementById('dash_options')
            inventoryOptions.classList.toggle('hidden')
        }


        function openFilter(){
            var cover = document.getElementById('coverup')
            var modal = document.getElementById('filterModal')

            cover.classList.remove('hidden')
            modal.classList.remove('hidden')
        }

        function closeFilter(event){
            event.preventDefault();
            var cover = document.getElementById('coverup')
            var modal = document.getElementById('filterModal')

            cover.classList.add('hidden')
            modal.classList.add('hidden')
        }
    </script>
</body>
</html>