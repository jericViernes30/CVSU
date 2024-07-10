<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    <script src="{{ asset('jquery/jquery.js') }}"></script>
    @vite('resources/css/app.css')
    <title>Dashboard</title>
</head>
<body class="w-full h-screen relative">
    <div id="quantity-div" class="w-1/6 p-4 absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-red-500 hidden">
        <p>Input quantity</p>
        <input type="number" id="quantity-input" class="w-full py-2 text-center rounded-sm outline-none border" value="1">
        <button id="submit-quantity" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded-sm">Submit</button>
    </div>
    <div class="w-1/5 mx-auto hidden absolute bottom-0 left-0 z-50" id="scanner">
        <video class="mx-auto" id="preview" width="100%"></video><br>
    </div>
    {{-- Top bar --}}
    {{-- <div class="w-full flex items-center h-[8%] px-20 border-b border-bd">
        <div class="w-1/6">
            <div class="">
                <img src="{{asset('images/logo2.png')}}" alt="" class="w-1/2">
            </div>
        </div>
        <div class="w-10/12 flex items-center"> --}}
            {{-- <input id="search" type="search" name="search" placeholder="Search here ..." class="py-1 px-4 outline-none w-[40%] border border-bd rounded-full bg-[#e5e5e5]"> --}}
        {{-- </div>
    </div> --}}
    {{-- main --}}
    <div class="w-full flex h-full">
        {{-- navigations --}}
        <div class="w-[6%] py-6 bg-white relative">
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
            <a href="{{route('inventory')}}" class="flex w-2/3 mx-auto flex-col items-center justify-center py-4">
                <img src="{{asset('images/inv-new.png')}}" alt="Cashier Icon" class="w-1/3">
                <p class="text-xs text-[#565857]">Inventory</p>
            </a>
            <a href="{{route('orders')}}" class="flex w-2/3 mx-auto flex-col items-center justify-center py-4 rounded-xl bg-[#f5a7a4]">
                <img src="{{asset('images/order-red.png')}}" alt="Cashier Icon" class="w-1/3">
                <p class="text-xs text-[#e5231a]">Orders</p>
            </a>
            <a href="{{route('office.login')}}" target="__blank" class="flex w-2/3 mx-auto flex-col items-center justify-center py-4">
                <img src="{{asset('images/backoffice-new.png')}}" alt="Cashier Icon" class="w-1/3">
                <p class="text-xs text-[#565857]">Office</p>
            </a>
        </div>
        {{-- POS --}}
        <div class="w-[94%] h-full grid grid-cols-2 grid-rows-12 gap-6 bg-[#e4e4e4] p-6">
            {{-- selection --}}
            <div class="w-full flex items-center justify-between h-full col-span-2 row-span-2 bg-white rounded-xl p-4">
                <p class="text-lg font-semibold">Orders list</p>
                <div>
                    <button id="submitSelected" class="py-2 rounded-lg border border-main text-main text-sm px-4">Complete Order</button>
                </div>
            </div>
            <div class="row-span-10 bg-white rounded-xl p-4 w-full h-full">
                <p class="pb-2 border-b border-[#565857] font-medium text-[#565857]">All orders</p>
                <div id="buttons" class="w-full py-2 h-[90%] overflow-y-auto">
                    @foreach ($suppliers as $supplierName => $supplierData)
                    <button class="w-full flex justify-between p-6 rounded-lg shadow-md mb-2 border border-[#565857] supp-btn" data-supplier="{{$supplierName}}">
                        <div class="w-1/3 flex gap-2">
                            <p>Supplier:</p>
                            <p>{{$supplierName}}</p>
                        </div>
                        <div class="w-1/3 flex gap-2">
                            <p>Number of orders:</p>
                            <p>{{ $supplierData['count'] }}</p>
                        </div>
                        <div class="w-1/3 flex gap-2">
                            <p>Total item quantity:</p>
                            <p>{{ $supplierData['quantity_sum'] }}</p>
                        </div>
                        {{-- <h2>{{ $supplierName }}</h2>
                        <p>Number of Orders: {{ $supplierData['count'] }}</p>
                        <p>Total Quantity: {{ $supplierData['quantity_sum'] }}</p>
                        @foreach ($supplierData['orders'] as $order)
                            <p>{{ $order->supplier_name }} - Quantity: {{ $order->quantity }}</p>
                        @endforeach --}}
                    </button>
                    @endforeach
                </div>
            </div>
            <div class="col-start-2 row-span-10 bg-white rounded-xl h-full p-4">
                <p class="pb-2 border-b border-[#565857] font-medium text-[#565857]">Order details</p>
                <div class="w-full h-full">
                    <p id="supplier_name" class="py-6 font-semibold text-xl text-main"></p>
                    <div id="orders_list" class="w-full">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function(){
            $('#buttons').on('click', '.supp-btn', function(){
                let supplierName = $(this).data('supplier')
                $('#supplier_name').text(supplierName)
                var url = "{{ route('supplier_name', ['name' => ':name']) }}"
                url = url.replace(':name', supplierName)

                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(response){
                        let ordersDiv = $('#orders_list')
                        ordersDiv.empty()

                        response.orders.forEach(function(order) {
                            // Parse the created_at date string into a Date object
                            var date = new Date(order.created_at);

                            // Format the date as "F d, Y"
                            var formattedDate = date.toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'long',
                                day: '2-digit'
                            });

                            var orderButton = `
                            <label class="w-full flex gap-4 py-3">
                                <input class="" type="checkbox" id="checkbox" name="id" value="${order.id}">
                                <p class="w-1/2">${order.item}</p>
                                <p class="w-1/4 text-right">${order.quantity}pcs</p>
                                <p class="w-1/4 text-right">${formattedDate}</p>
                            </label>
                            `;

                            ordersDiv.append(orderButton);
                        });

                    },
                    error: function(xhr, status, error){
                        console.error(xhr, status, error)
                    }
                })
            })
        })

    $(document).on('click', '#submitSelected', function() {
        // Gather the selected checkbox values
        let selectedIds = $('input[name="id"]:checked').map(function() {
            return this.value;
        }).get();

        console.log(selectedIds)
        
        // Check if any checkboxes are selected
        if (selectedIds.length === 0) {
            alert('No items selected');
            return;
        }
        
        // Send the selected IDs to the server
        $.ajax({
            url: "{{ route('complete_order') }}", // Ensure this URL matches your route
            method: 'POST', // Use POST method
            contentType: 'application/json', // Send JSON data
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token
            },
            data: JSON.stringify({ ids: selectedIds }), // Convert array to JSON
            success: function(response) {
                // Handle the response from the server
                console.log('Success:', response);
            },
            error: function(xhr, status, error) {
                // Handle errors
                console.error('Error:', status, error);
            }
        });
    });
    </script>
</body>
</html>